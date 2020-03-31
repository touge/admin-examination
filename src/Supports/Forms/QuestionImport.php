<?php

namespace Touge\AdminExamination\Supports\Forms;

use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;

class QuestionImport extends Form
{
    public $title= 'example';

    /**
     * Handle the form request.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request)
    {
        //dump($request->all());

        admin_success('Processed successfully.');

        return back();
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->text('name')->rules('required');
        $this->email('email')->rules('email');
        $this->file('export-file')->move('$dir', '$name')->help('支持cvs,text文档导入。请提前下载cvs及txt文档。');
        $this->datetime('created_at');
    }

    /**
     * The data of the form.
     *
     * @return array $data
     */
    public function data()
    {
        return [
            'name'       => 'John Doe',
            'email'      => 'John.Doe@gmail.com',
            'created_at' => now(),
        ];
    }
}
