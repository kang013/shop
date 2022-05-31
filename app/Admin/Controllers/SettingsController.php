<?php

namespace App\Admin\Controllers;

use App\Admin\Controllers\Settings;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Tab;

class SettingsController extends AdminController
{
    public function index(Content $content)
    {

        $forms = [
            'info'    => Settings\Info::class,
            'wexin'     => Settings\WeXin::class,
        ];

        return $content
            ->title('选项卡表单')
            ->body(Tab::forms($forms));

    }
}
