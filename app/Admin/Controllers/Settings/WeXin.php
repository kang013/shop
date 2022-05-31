<?php


namespace App\Admin\Controllers\Settings;

use App\Models\Settings;
use Encore\Admin\Widgets\Form;


class WeXin extends Form
{

    public $title = '微信配置';
    public $description = '表单';

    protected function form()
    {
        $this->text('appid','appid')->rules('required');
        $this->action('/admin/storeEmail');
    }
}

