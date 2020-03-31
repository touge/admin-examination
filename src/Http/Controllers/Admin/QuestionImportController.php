<?php


namespace Touge\AdminExamination\Http\Controllers\Admin;


use Encore\Admin\Layout\Content;
use Touge\AdminExamination\Http\Controllers\BaseController;
use Touge\AdminExamination\Supports\Forms\QuestionImport;

class QuestionImportController extends BaseController
{
    /**
     *
     */
    public function import(Content $content){
        return $content->title('setting')
            ->body(new QuestionImport());
    }
}
