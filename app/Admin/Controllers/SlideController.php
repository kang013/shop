<?php

namespace App\Admin\Controllers;

use App\Models\Slide;
use App\Models\SlideCategory;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class SlideController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Slide';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Slide());

        $grid->column('id', 'ID');
        $grid->column('category.name', '分类');
        $grid->column('name', '名称');
        $grid->column('image', '图片');
        $grid->column('url', '链接');
        $grid->status('是否显示')->display(function ($value) {
            return $value ? '是' : '否';
        });
        $grid->column('order', '排序');

        return $grid;
    }


    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Slide());

        $form->text('name', '名称')->rules('required');

        $category = [];
        $collection = SlideCategory::get(['id','name'])->toArray();
        foreach ($collection as $value){
            $category[$value['id']] = $value['name'];
        }
        $form->select('category_id', '类目')->options($category);
        $form->image('image', '图片')->rules('required|image');;
        $form->url('url', '链接');
        $form->switch('status', '是否显示');
        $form->text('description', '描述');
        $form->textarea('content', '内容');
        $form->number('order','排序');

        return $form;
    }
}
