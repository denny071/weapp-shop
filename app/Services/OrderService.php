<?php

namespace App\Services;

use App\Exceptions\CouponCodeUnavailableException;
use App\Exceptions\InternalException;
use App\Jobs\RefundInstallmentOrder;
use App\Models\CouponCode;
use App\Models\ExpressCompany;
use App\Models\ExpressFreight;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\Order;
use App\Models\ProductSku;
use App\Exceptions\InvalidRequestException;
use App\Jobs\CloseOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;


class OrderService
{
    /**
     * 普通商品下订单
     *
     * @param User $user 用户
     * @param UserAddress $address 用户地址
     * @param $remak string 备注
     * @param $items array 购买商品
     * @param CouponCode|null $coupon 会员卷
     * @param $expressId int 快递公司ID
     * @return mixed
     * @throws CouponCodeUnavailableException
     */
    public function store(User $user, UserAddress $address, $remak, $items, CouponCode $coupon = null,$expressId)
    {
        // 如果传入了优惠券，则先检查是否可用
        if ($coupon) {
            // 但此时我们还没有计算出订单总金额，因此先不校验
            $coupon->checkAvailable($user);
        }
        // 开启一个数据库事务
        $order = \DB::transaction(function () use ($user, $address, $remak, $items, $coupon,$expressId) {
            // 更新此地址的最后使用时间
            $address->update(['last_used_at' => Carbon::now()]);
            // 创建一个订单
            $express = ExpressCompany::find($expressId);
            $freight = ExpressFreight::where(["express_id" => $expressId,"province" => $address->province])->first();

            $order = new Order([
                'address' => [ //  将底座信息放在订单中
                    'address' => $address->full_address,
                    'zip' => $address->zip,
                    'contact_name' => $address->contact_name,
                    'contact_phone' => $address->contact_phone
                ],
                'remark' => $remak,
                'total_amount' => 0,
                'express' => $express->name,
                'freight' => $freight->freight,
                'type' => Order::TYPE_NORMAL,
            ]);
            // 订单关联到当前账户
            $order->user()->associate($user);
            // 写入数据库
            $order->save();
            $productAmount = 0;
            // 遍历用户提交的 SKU
            foreach ($items as $data) {
                $sku = ProductSku::find($data['sku_id']);
                // 创建一个 OrderItem 并直接与当前订单关联
                $item = $order->items()->make([
                    'amount' => $data['amount'],
                    'price' => $sku->price,
                ]);
                $item->product()->associate($sku->product_id);
                $item->productSku()->associate($sku);
                $item->save();
                $productAmount += $sku->price * $data['amount'];
                if ($sku->decreaseStock($data['amount']) <= 0) {
                    throwErrorMessage("Order","149004");
                }
            }

            if ($coupon) {
                // 总金额已经计算出来了，检查是否符合优惠券规则
                $coupon->checkAvailable($user, $productAmount);
                // 把订单金额修改为优惠后的金额
                $productAmount = $coupon->getAdjustedPrice($productAmount);
                // 将订单与优惠券关联
                $order->couponCode()->associate($coupon);
                // 增加优惠券的用量，需判断返回值
                if ($coupon->changeUsed() <= 0) {
                    throwErrorMessage("Order","149005");
                }
            }
            $payDeadline = Carbon::now()->addSecond(config('app.order_ttl'))->toDateTimeString();
            // 更新订单总金额 和 最后付款时间
            $order->update([
                'product_amount' => $productAmount,
                'total_amount' => $productAmount + $freight->freight,
                'pay_deadline' => $payDeadline]);

            // 将下单商品从购物城中移除
            $skuIds = collect($items)->pluck('sku_id');
            app(CartService::class)->remove($skuIds,$user);

            return $order;
        });

        dispatch(new CloseOrder($order, config('app.order_ttl')));

        return $order;
    }

    /**
     * 众筹下单
     *
     * @param User $user 用户
     * @param UserAddress $address 地址
     * @param ProductSku $sku 子产品
     * @param $amount 金额
     * @return mixed
     */
    public function crowdfunding(User $user, UserAddress $address, ProductSku $sku, $amount)
    {
        // 开启事务
        $order = \DB::transaction(function () use ($amount, $sku, $user, $address) {
            // 更新地址最后使用时间
            $address->update(['last_used_at' => Carbon::now()]);
            // 创建一个订单
            $order = new Order([
                'address' => [ // 将地址信息放入订单中
                    'address' => $address->full_address,
                    'zip' => $address->zip,
                    'contact_name' => $address->contact_name,
                    'contact_phone' => $address->contact_phone,
                ],
                'remark' => '',
                'total_amount' => $sku->price * $amount,
                'type' => Order::TYPE_CROWDFUNDING,
            ]);
            // 订单关联到当前用户
            $order->user()->associate($user);
            // 写入数据库
            $order->save();
            // 创建一个新的订单项并与 SKU 关联
            $item = $order->items()->make([
                'amount' => $amount,
                'price' => $sku->price,
            ]);
            $item->product()->associate($sku->product_id);
            $item->productSku()->associate($sku);
            $item->save();
            // 扣减对应 SKU 库存
            if ($sku->decreaseStock($amount) <= 0) {
                throw new InvalidRequestException('该商品库存不足');
            }

            return $order;
        });

        // 众筹结束时间减去当前时间得到剩余秒数
        $crowdfundingTtl = $sku->product->crowdfunding->end_at->getTimestamp() - time();
        // 剩余秒数与默认订单关闭时间取较小值作为订单关闭时间
        dispatch(new CloseOrder($order, min(config('app.order_ttl'), $crowdfundingTtl)));

        return $order;
    }

    /**
     * 秒杀下单
     *
     * @param User $user
     * @param UserAddress $address
     * @param ProductSku $sku
     */
    public function seckill(User $user, UserAddress $userAddress, ProductSku $sku)
    {
        $order = \DB::transaction(function () use ($user, $userAddress ,$sku) {
            // 更新此地的最后使用时间
            // 创建一个订单
            $order = new Order([
                'address'      => [
                    'address'       => $userAddress->province.$userAddress->city.$userAddress->district.$userAddress->address,
                    'zip'           => $userAddress->zip,
                    'contact_name'  => $userAddress->contact_name,
                    'contact_phone' => $userAddress->contact_phone,
                ],
                'remark'       => '',
                'total_amount' => $sku->price,
                'type'         => Order::TYPE_SECKILL
            ]);
            // 订单关联到单钱用户
            $order->user()->associate($user);
            // 写入数据库
            $order->save();
            // 创建一个新的订单项并与 SKU 关联
            $item = $order->items()->make([
                'amount' => 1, // 秒杀商品只能一份
                'price' => $sku->price,
            ]);
            $item->product()->associate($sku->product_id);
            $item->productSku()->associate($sku);
            $item->save();
            // 扣减对应 SKU 库存
            if ($sku->decreaseStock(1) <= 0) {
                throw new InvalidRequestException('该商品库存不足');
            }
            Redis::decr('seckill_sku_'.$sku->id);
            return $order;
        });
        // 秒杀订单的自动关闭时间与普通订单不同
        dispatch(new CloseOrder($order,config('app.seckill_order_ttl')));

        return $order;
    }

    /**
     * 订单退款
     *
     * @param Order $order
     * @throws InternalException
     */
    public function refundOrder(Order $order)
    {
        // 判断该订单的支付方式
        switch ($order->payment_method) {
            case 'wechat':
                // 生成退款订单号
                $refundNo = Order::getAvailableRefundNo();

                app('wechat_pay')->refund([
                    'out_trade_no' => $order->no,
                    'out_refund_no' => $refundNo,
                    'total_fee' => $order->total_amount * 100,
                    'refund_fee' => $order->total_amount * 100,
                    'refund_desc' => '订单退款',
                    'notify_url' => env("WECHAT_REFUND_NOTIFY_URL",""),

                ]);
                $order->update([
                    'refund_no' => $refundNo,
                    'refund_status' => Order::REFUND_STATUS_PROCESSING,
                ]);
                break;
            case 'alipay':
                $refundNo = Order::getAvailableRefundNo();
                $ret = app('alipay')->refund([
                    'out_trade_no' => $order->no,
                    'refund_amount' => $order->total_amount,
                    'out_request_no' => $refundNo,
                ]);
                if ($ret->sub_code) {
                    $extra = $order->extra;
                    $extra['refund_failed_code'] = $ret->sub_code;
                    $order->update([
                        'refund_no' => $refundNo,
                        'refund_status' => Order::REFUND_STATUS_FAILED,
                        'extra' => $extra,
                    ]);
                } else {
                    $order->update([
                        'refund_no' => $refundNo,
                        'refund_status' => Order::REFUND_STATUS_SUCCESS,
                    ]);
                }
                break;
            case 'installment':
                $order->update([
                    'refund_no' => Order::getAvailableRefundNo(), // 生成退款订单号
                    'refund_status' => Order::REFUND_STATUS_PROCESSING, // 将退款状态改为退款中
                ]);
                // 触发退款异步任务
                dispatch(new RefundInstallmentOrder($order));
                break;
            default:
                throw new InternalException('未知订单支付方式：' . $order->payment_method);
                break;
        }
    }
}
