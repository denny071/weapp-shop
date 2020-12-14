<?php

namespace App\Admin\Controllers;

use App\Models\Coupon;
use Carbon\Carbon;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;

/**
 * CouponController 优惠券
 */
class CouponController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Coupon(), function (Grid $grid) {


            $grid->model()->orderBy('created_at','desc');
            $grid->id('ID')->sortable();
            $grid->column('name');
            $grid->column('code');
            $grid->column('description');
            $grid->column('usage')->display(function () {
                return "{$this->used} / {$this->total}";
            });
            $grid->enabled('是否启用')->display(function ($value) {
                return $value ? '是': '否';
            });
            $grid->column('created_at')->display(function ($value) {
               return Carbon::parse($value)->format("Y-m-d H:i:s");
            });
            $grid->actions(function ($actions) {
                $actions->disableView();
            });

        });
    }


    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new Coupon(), function (Form $form) {
            $form->display('id');
            $form->text('name')->rules('required');
            $form->text('code')->rules(function ($form) {
                if ($id = $form->model()->id) {
                    return 'nullable|unique:coupon_codes,code,'.$id.',id';
                } else {
                    return 'nullable|unique:coupon_codes';
                }
            });
            $form->radio('type')->options(Coupon::$typeMap)->rules('required');
            $form->text('value')->rules(function ($form) {
                if ($form->type === Coupon::$typeMap) {
                    return 'required|numeric|between:1,99';
                } else {
                    return 'required|numeric|min:0.01';
                }
            });
            $form->text('total')->rules('required|numeric|min:0');
            $form->text('min_amount')->rules('required|numeric|min:0');
            $form->datetime('not_before');
            $form->datetime('not_after');
            $form->radio('enabled')->options(['1' => '是', '0' => '否']);
            $form->saving(function (Form $form) {
                if (!$form->code) {
                    $form->code = Coupon::findAvailableCode();
                }
            });
        });
    }
}
