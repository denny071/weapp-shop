<?php

namespace App\Admin\Controllers;


use App\Models\Product;
use App\Models\CrowdfundingProduct;
use Carbon\Carbon;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;


/**
 *
 * CrowdfundingProductController 众筹商品
 */
class CrowdfundingProductController extends CommonProductController
{

    /**
     * 获得商品类型
     *
     * @return string
     */
    public function getProductType()
    {
        return Product::TYPE_CROWDFUNDING;
    }

    /**
     * 众筹商品表格
     *
     * @param Grid $grid
     */
    protected function customGrid(Grid $grid)
    {
        $grid->id('ID')->sortable();
        $grid->column('title');
        $grid->column('on_sale')->display(function ($value) {
            return $value ? '是' : '否';
        });
        $grid->column('price');
        $grid->column('crowdfunding.target_amount');
        $grid->column('crowdfunding.end_at')->display(function ($value) {
            return Carbon::parse($value)->format("Y-m-d H:i:s");
        });
        $grid->column('crowdfunding.total_amount');

        $grid->column('crowdfunding.status')->display(function ($value) {
            return CrowdfundingProduct::$statusMap[$value];
        });
    }

    /**
     * 众筹商品表单
     *
     * @param Form $form
     */
    protected function customForm(Form $form)
    {
        // 众筹相关字段
        $form->text('crowdfunding.target_amount')->rules('required|numeric|min:0.01');
        $form->datetime('crowdfunding.end_at')->rules('required|date');
    }
}
