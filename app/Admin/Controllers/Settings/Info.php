<?php


namespace App\Admin\Controllers\Settings;

use App\Models\Settings;
use Encore\Admin\Widgets\Form;


class Info extends Form
{

    public $title = '基本配置';
    public $description = '表单';

    protected function form()
    {
        $this->text('email','邮箱')->rules('required');
        $this->action('/admin/storeEmail');
    }
}
