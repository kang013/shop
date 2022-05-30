<?php

namespace App\Admin\Controllers;

use App\Models\SlideCategory;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class SlideCategoriesController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'SlideCategory';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new SlideCategory());

        $grid->id('ID')->sortable();
        $grid->name('名称');
        $grid->index_name('分类标识');
        $grid->status('是否显示')->display(function ($value) {
            return $value ? '是' : '否';
        });
        $grid->actions(function ($actions) {
            // 不展示 Laravel-Admin 默认的查看按钮
            $actions->disableView();
        });
        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->like('name', '名称');
        });

        return $grid;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new SlideCategory());

        $form->text('name', '名称')->rules('required');
        $form->text('index_name', '分类标识')->rules('required');
        $form->text('remark', '备注');
        $form->switch('status', '是否显示');

        return $form;
    }
}
