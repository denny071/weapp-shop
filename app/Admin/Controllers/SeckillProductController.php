<?php

namespace App\Admin\Controllers;

use App\Models\Product;
use App\Models\ProductSku;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Illuminate\Support\Facades\Redis;

/**
 * SeckillProductController 秒杀商品
 */
class SeckillProductController extends CommonProductController
{
    /**
     * 获得商品类型
     *
     * @return string
     */
    public function getProductType()
    {
        return Product::TYPE_SECKILL;
    }

    /**
     * 自定义表格
     *
     * @param Grid $grid
     */
    public function customGrid(Grid $grid)
    {
        $grid->id('ID')->sortable();
        $grid->title('商品名称');
        $grid->on_sale('已上架')->display(function ($value) {
            return $value ? '是':'否';
        });
        $grid->price('价格');
        $grid->column('seckill.start_at','开始时间');
        $grid->column('seckill.end_at','结束时间');
        $grid->column('sold_count');
    }

    /**
     * 自定义表单
     *
     * @param Form $form
     */
    protected function customForm(Form $form)
    {
        // 秒杀相关字段
        $form->datetime('seckill.start_at')->rules('required|date');
        $form->datetime('seckill.end_at')->rules('required|date');

        // 当商品表单保存完毕时触发
        $form->saved(function (Form $form) {
            $product =  $form->repository()->eloquent();
            // 商品重新加载秒杀字段
            $product->load(['seckill']);
            // 获取当前时间与秒杀结束时间的差值

            $diff = $product->seckill->end_at->getTimestamp() - time();
            // 遍历商品 SKU
            $product->skus->each(function (ProductSku $sku) use ($diff, $product) {
                // 如果秒杀商品是上架并且尚未到结束时间
                if ($product->on_sale && $diff > 0) {
                    // 将商誉商品写入刀片redis 中，并设置该过期时间为秒杀时间
                    Redis::setex('seckill_sku_'.$sku->id, $diff, $sku->stock);
                } else {
                    // 否则将该 SKU 的库存值从 Redis 中删除
                    Redis::del('seckill_sku_'.$sku->id);
                }
            });

        });
    }
}
