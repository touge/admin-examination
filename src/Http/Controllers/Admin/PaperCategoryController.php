<?php

namespace Touge\AdminExamination\Http\Controllers\Admin;

use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Layout\Content;
use Touge\AdminExamination\Http\Controllers\BaseController;
use Touge\AdminExamination\Models\PaperCategory;
use Touge\AdminOverwrite\Grid\Displayers\Actions;
use Touge\AdminOverwrite\Grid\Grid;

class PaperCategoryController extends BaseController
{
    use HasResourceActions;


    protected $header_text= '';

    public function __construct()
    {
        $this->push_breadcrumb(
            [
                'text'=> trans("admin-examination::paper.categories.module-name"),
                'url'=> route('exams.paper-category.index')
            ]
        );

        $this->header_text=  __('admin-examination::paper.categories.module-name');
    }

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        $this->set_breadcrumb($content);

        return $content
            ->header($this->header_text)
            ->description(__('admin.list'))
            ->body($this->grid());
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        $this->push_breadcrumb(['text'=> trans("admin.edit")])
            ->set_breadcrumb($content);
        return $content
            ->header($this->header_text)
            ->description(__('admin.edit'))
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        $this->push_breadcrumb(['text'=> trans("admin.create")])
            ->set_breadcrumb($content);

        return $content
            ->header($this->header_text)
            ->description(__('admin.create'))
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new PaperCategory());
        $modal= $grid->model();

        $modal->where(['customer_school_id'=> $this->customer_school_id()])->orderBy('id', 'DESC');


        $grid->id('Id');
        $grid->name(__('admin.name'));
        $grid->sort_order(__('admin.order'));
        $grid->updated_at(__('admin.updated_at'));

        $grid->disableRowSelector()
            ->disableColumnSelector()
            ->disableFilter()
            ->disableExport();

        $grid->actions(function(Actions $actions){
           $actions->disableView();
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
        $form = new Form(new PaperCategory);
        $form->hidden('customer_school_id')->default($this->customer_school_id());

        $form->text('name', __('admin.name'));
        $form->number('sort_order', __('admin.order'))->default(50);


        $form->disableViewCheck()->disableEditingCheck()->disableCreatingCheck();
        $form->tools(function(Form\Tools $tools){
           $tools->disableView()->disableDelete();//->disableList();
        });

        return $form;
    }

}
