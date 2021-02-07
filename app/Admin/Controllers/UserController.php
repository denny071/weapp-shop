<?php

namespace App\Admin\Controllers;

use App\Models\User;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

/**
 * UserController 用户控制器
 */
class UserController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new User(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('name');
            $grid->column('mobile')->display(function ($value) {
                return $value??"未设置";
            });
            $grid->column('email')->display(function ($value) {
                return $value??"未设置";
            });

            $grid->column('gender')->display(function ($value) {
                return $value==1?"男":"女";
            });
            $grid->column('info')->display(function ()  {
                if ($this->country){
                    return $this->country." ".$this->province." ".$this->city;
                } else {
                    return "未获取";
                }

            });
            $grid->column('last_login_at',"最近登录时间");
            $grid->column('created_at',"注册时间");

            $grid->actions(function ($actions) {

                $actions->disableEdit();

                $actions->disableDelete();
            });
            $grid->filter(function (Grid\Filter $filter) {
                $filter->like('mobile');
                $filter->like('email');
            });
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new User(), function (Show $show) {
            $show->html(function () {
                return view("admin.user.show",["user" => $this]);
            });
        });
    }

}
