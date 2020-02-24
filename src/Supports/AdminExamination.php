<?php

namespace Touge\AdminExamination\Supports;

use Encore\Admin\Extension;

class AdminExamination extends Extension
{
    public $name = 'admin-examination';

    public $views = __DIR__ . '/../../resources/views';

    public $assets = __DIR__ . '/../../resources/assets';

    public $menu = [
        'title' => 'AdminExamination',
        'path'  => 'admin-examination',
        'icon'  => 'fa-gears',
    ];
}