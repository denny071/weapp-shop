<?php

namespace App\Admin\Controllers;


use App\Models\Product;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;


/**
 * ProductController 普通商品
 */
class ProductController extends CommonProductController
{
    /**
     * 获得商品类型
     *
     * @return string
     */
    public function getProductType()
    {
        return Product::TYPE_NORMAL;
    }

    /**
     * 商品表格
     *
     * @param Grid $grid
     */
    protected function customGrid(Grid $grid)
    {
        $grid->model()->with(['catalog']);
        $grid->id( )->sortable();
        $grid->column('title');
        $grid->column('catalog.name');
        $grid->column('on_sale')->display(function ($value) {
            return $value ? '是' : '否';
        });
        $grid->column('price');
        $grid->column('rating');
        $grid->column('sold_count');
        $grid->column('review_count');
    }

    /**
     * 自定义表单
     *
     * @param Form $form
     */
    protected function customForm(Form $form)
    {
        // 普通商品没有额外的字段，因此这里不需要写任何代码
    }
}
