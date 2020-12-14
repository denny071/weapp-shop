<?php


namespace App\Admin\Controllers;


use App\Models\Product;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Http\Controllers\HasResourceActions;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Content;

/**
 * CommonProductController 商品基类
 */
abstract class CommonProductController extends AdminController
{
    use HasResourceActions;

    // 定义一个抽象方法，返回当前管理的商品类型
    abstract public function getProductType();

    /**
     * 商品列表
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header(Product::$typeMap[$this->getProductType()].'列表')
            ->body($this->grid());
    }

    // 定义一个抽象方法，各个类型 的控制器将实现本方法来定义列表应该展示的哪些字段
    abstract protected function customGrid(Grid $grid);
    /**
     * 商品表格
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(Product::with(['crowdfunding','seckill']), function (Grid $grid) {

            // 筛选出当前类型的商品，默认ID 倒序排序
            $grid->model()->where('type', $this->getProductType())->orderBy('id','desc');
            // 调用自定义方法
            $this->customGrid($grid);
            $grid->actions(function ($actions) {
                $actions->disableView();
                $actions->disableDelete();
            });
            $grid->tools(function ($tools) {
                $tools->batch(function ($batch){
                    $batch->disableDelete();
                });
            });

        });
    }


    /**
     * 创建商品
     *
     * @param Content $content 上下文
     * @return Content 上下文
     */
    public function create(Content $content)
    {
        return $content
            ->header('创建'.Product::$typeMap[$this->getProductType()])
            ->body($this->form());
    }

    /**
     * 编辑商品
     *
     * @param integer $id 商品ID
     * @param Content $content 上下文
     * @return Content 上下文
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('编辑'.Product::$typeMap[$this->getProductType()])
            ->body($this->form()->edit($id));
    }


    // 定义一个抽象方法，各个类型 的控制器将实现本方法来定义列表应该展示的哪些字段
    abstract protected function customForm(Form $form);

    /**
     * 商品表单
     *
     * @return Form
     */
    protected function form()
    {

        return  Form::make(\App\Models\Product::with("skus","properties","crowdfunding","seckill"), function (Form $form) {
            $form->hidden('type')->value($this->getProductType());
            $form->text('title')->rules('required');
            $form->text('long_title')->rules('required');
            $form->select('catalog_id')->options(function ($id) {
                $catalog = \App\Models\Catalog::find($id);
                if ($catalog) {
                    return [$catalog->id => $catalog->full_name];
                }
            })->ajax('/api/catalog?is_directory=0');

            $form->image('cover_image')->rules('required|image')->autoUpload();
            $form->multipleImage('album_image')->rules('required|image')->saving(function ($v) {
                return implode(',', $v);})->autoUpload();
            $form->multipleImage('content_image')->rules('required|image')->saving(function ($v) {
                return implode(',', $v);})->autoUpload();

            $form->radio('on_sale')->options(["1" => '下架',"9" => '上架'])->default("1");
            // 调用自定义方法
            $this->customForm($form);

            $form->hasMany('skus','商品 SKU', function (Form\NestedForm $form) {
                $form->text('title')->rules('required');
                $form->text('description')->rules('required');
                $form->text('price')->rules('required|numeric|min:0.01');
                $form->text('stock')->rules('required|integer|min:0');
            });

            $form->hasMany('properties', '商品属性', function (Form\NestedForm $form) {
                $form->text('name')->rules('required');
                $form->text('value')->rules('required');
            });

            $form->saved(function (Form $form) {
                $model =  $form->repository()->eloquent();

                $model->update(['price' => collect($form->input('skus'))->min('price')?:0]);

//                $this->dispatch(new SyncOneProductToES($model));
            });

        });


    }

}
