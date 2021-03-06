<?php

namespace App\Jobs;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;

/**
 * 关闭订单任务
 *
 * Class CloseOrder
 * @package App\Jobs
 */
class CloseOrder extends Job
{

    protected $order;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order, $delay)
    {
        $this->order = $order;
        // 设置延迟的时间，delay（） 方法的参数代表多少秒之后执行
        $this->delay($delay);
    }

    /**
     * 定义这个任务类具体的执行逻辑
     * 当队列处理器从队列中取出任务时，会调用handle（） 方法
     *
     * @return void
     */
    public function handle()
    {
        // 判断对应的订单是否已被支付
        // 如果已经支付则不需要关闭订单，直接退出
        if ($this->order->paid_at) {
            return;
        }
        // 通过事务执行 sql
        \DB::transaction(function () {
            // 将订单的 closed 字段标记为 true，即关闭订单
            $this->order->update(['is_closed' => true,"closed_at" => Carbon::now()->format("Y-m-d H:i:s")]);
            // 循环遍历订单中的上坡 SKU, 将订单中的数量加回到 SKU 的库存中去
            foreach ($this->order->items as $item) {
                $item->productSku->addStock($item->amount);
                // 当前订单类型是秒杀订单，并且对应商品是上架且尚未到截止时间
                if ($item->order->type === Order::TYPE_SECKILL
                    && $item->product->on_sale
                    && !$item->product->seckill->is_after_end) {
                    // 将 Redis 中的库存 +1
                    Redis::incr('seckill_sku_'.$item->productSku->id);
                }
            }
            if ($this->order->coupon) {
                $this->order->coupon->changeUsed(false);
            }
        });

    }
}
