<?php

namespace App\Admin\Controllers;

use App\Models\Catalog;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Illuminate\Http\Request;

/**
 * CatalogController 类目
 */
class CatalogController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {


        return Grid::make(new Catalog(), function (Grid $grid)  {
            // 设置为正方形

            $grid->column('id')->sortable();
            $grid->column('name');
            $grid->column('level');

            $grid->column('is_directory')->display(function ($value) {
                return $value ? '是':'否';
            });
            $grid->column('path')->display(function ($value)  {
                $show = [];
                foreach (explode("-",substr($value,1,strlen($value) -2 ) ) as $key){
                    if($key) {
                        $show[] = $key;
                    } else {
                        break;
                    }
                }
                return implode(" / ", $show);
            });
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
            });
            // 设置初始排序条件
            $grid->model()->orderBy('id', 'desc');
            // 禁用过滤器按钮
            $grid->disableFilterButton();
            // 禁用刷新按钮
            $grid->disableRefreshButton();

            $grid->actions(function ($actions) {
                // 不展示 Laravel-Admin 默认的查看按钮
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
        return Form::make(new Catalog(), function (Form $form) {
            $form->text('name')->rules("required");
            $form->image('cover_image')->rules('required|image')->autoUpload();
            $form->radio('is_directory')
                ->options(["1" => "是","0" => "否"])
                ->default('0')
                ->rules('required');
            $form->isEditing()?$form->display("parent.name"):$form->select('parent_id')->ajax('/api/catalog');
        });
    }

    public function apiIndex(Request $request)
    {
        return (new Catalog())->getParentList($request->input("q"),$request->input("is_directory",1));
    }
}
