<?php

namespace App\Http\Controllers\V1\Order;

use App\Http\Controllers\V1\Controller;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\UserAddress;
use App\Services\OrderService;
use App\Transformers\OrderTransformer;

/**
 * OrderController 订单
 */
class OrderController extends Controller
{


    /**
     * 订单列表
     *
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     */
    public function index()
    {
        $request = $this->checkRequest();

        $userId = $this->user()->id;

        $hander  = Order::query()->with(['items.product', 'items.productSku'])->where('user_id', $userId);
        switch ($request->order_status) {
            case 0:   // 全部

                break;
            case 1:   // 待发货
                $hander->whereNull("paid_at")->where("ship_status", Order::SHIP_STATUS_PENDING)->whereNull("status");
                break;
            case 2:   // 已发货
                $hander->where("ship_status", Order::SHIP_STATUS_DELIVERED);
                break;
            case 3:   // 已收货
                $hander->where("ship_status", Order::SHIP_STATUS_RECEIVED);
                break;
        }

        $orders = $hander->orderBy('created_at', 'desc')->paginate();
        // 未支付订单数量
        $pendingPayCount = Order::where('user_id', $userId)->whereNull("paid_at")->count();
        // 已发货订单数量
        $backrdersCount = Order::where('user_id', $userId)->where("ship_status", Order::SHIP_STATUS_DELIVERED)->count();
        // 已收货订单数量
        $shippedCount =  Order::where('user_id', $userId)->where("ship_status", Order::SHIP_STATUS_RECEIVED)->count();

        return $this->response->paginator($orders, OrderTransformer::class)
            ->addMeta("pending_pay_count", $pendingPayCount)
            ->addMeta("backrders_count", $backrdersCount)
            ->addMeta("shipped_count", $shippedCount);
    }


    /**
     * 订单详情
     *
     * @param string $orderNo 订单编号ßß
     */
    public function detail(string $orderNo)
    {
        $order = $this->checkOrder($orderNo);

        return $this->response->item(
            $order->load('items.product', 'items.productSku'),
            OrderTransformer::class
        );
    }

    /**
     * 保存订单
     *
     * @param OrderRequest $request
     * @param OrderService $orderService
     * @return \Dingo\Api\Http\Response
     * @throws CouponCodeUnavailableException
     */
    public function store(OrderService $orderService)
    {
        $request = $this->checkRequest();

        $expressId = $request->express_id;
        $address = UserAddress::find($request->address_id);
        $coupon = null;

        // 如果用户提交了优惠码
        if ($code = $request->coupon_code) {
            $coupon = Coupon::where('code', $code)->first();
            if (!$coupon) {
                $this->errorInternal("149003");
            }
        }
        $items = [];
        $productSkuIds = $request->product_sku_ids;
        // 购物城商品
        $cartItem = CartItem::where("user_id", $this->user()->id)->whereIn("product_sku_id", explode(",", $productSkuIds))->get();
        if ($cartItem) {
            foreach ($cartItem as $item) {
                $temp['sku_id'] =  $item->product_sku_id;
                $temp['amount'] =  $item->amount;
                $items[] = $temp;
            }
        }
        // 保存订单
        $order = $orderService->store(
            $this->user(),
            $address,
            $request->remark,
            $items,
            $coupon,
            $expressId
        );
        // 显示订单
        return $this->response->item($order, OrderTransformer::class);
    }


    /**
     * 取消订单
     *
     * @param string $orderNo 订单编号
     * @return \Dingo\Api\Http\Response|void
     * @throws InvalidRequestException
     */
    public function cancel(string $orderNo)
    {
        // 检查订单
        $order = $this->checkOrder($orderNo);
        // 判断订单的发货状态是否为已发货
        if ($order->paid_at != "") {
            $this->errorInternal("149006");
        }
        // 更新发货状态为已收到
        $order->update(['status' => Order::ORDER_STATUS_CANCEL]);
        // 返回成功
        return $this->response->noContent();
    }

    /**
     * 申请退款
     *
     * @param string $orderNo
     * @param ApplyRefundRequest $request
     * @throws InvalidRequestException
     */
    public function applyRefund(string $orderNo)
    {
        // 检查请求
        $request = $this->checkRequest();

        // 检查订单
        $order = $this->checkOrder($orderNo);

        // 判断订单是否已付款
        if (!$order->paid_at) {
            $this->errorInternal("149007");
        }
        // 众筹订单不允许申请退款
        if ($order->type === Order::TYPE_CROWDFUNDING) {
            $this->errorInternal("149008");
        }
        // 判断订单退款状态是否正确
        if ($order->refund_status !== Order::REFUND_STATUS_PENDING) {
            $this->errorInternal("149009");
        }

        // 将用户输入的退款理由放到订单的 extra 字段中
        $extra = $order->extra ?: [];
        $extra['refund_reason'] = $request->reason;
        // 将订单退款状态改为已申请退款
        $order->update([
            'refund_status' => Order::REFUND_STATUS_APPLIED,
            'extra' => $extra,
        ]);
        // 返回成功
        return $this->response->created();
    }


    /**
     * 确认收货
     *
     * @param string $orderNo
     * @return \Dingo\Api\Http\Response|void
     * @throws InvalidRequestException
     */
    public function received(string $orderNo)
    {
        // 检查订单
        $order = $this->checkOrder($orderNo);
        // 判断订单的发货状态是否为已发货
        if ($order->ship_status !== Order::SHIP_STATUS_DELIVERED) {
            $this->errorInternal("149010");
        }
        // 更新发货状态为已收到
        $order->update(['ship_status' => Order::SHIP_STATUS_RECEIVED]);
        // 返回成功
        return $this->response->created();
    }


    /**
     * checkOrder 检测订单
     *
     * @param  mixed $orderNo
     * @return Order
     */
    public function checkOrder($orderNo): Order
    {
        // 获得订单
        $order = Order::where(["no" => $orderNo])->first();
        // 订单不存在
        if (!$order) {
            $this->errorInternal("149001");
        }
        // 订单无权查看
        if ($order->user_id != $this->user()->id) {
            $this->errorInternal("149002");
        }
        return $order;
    }


    /**
     * getOrder 获得订单
     *
     * @param  mixed $orderNo
     * @return Order
     */
    public function getOrder($orderNo): Order
    {
        // 获得订单
        $order = Order::where(["no" => $orderNo])->first();
        // 订单不存在
        if (!$order) {
            $this->errorInternal("149001");
        }
        return $order;
    }
}
