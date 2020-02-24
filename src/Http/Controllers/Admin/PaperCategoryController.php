<?php

namespace Touge\AdminExamination\Http\Controllers\Admin;

use Encore\Admin\Form;
use Encore\Admin\Layout\Content;
use Touge\AdminExamination\Http\Controllers\Admin\Traits\HasGradationResourceActions;
use Touge\AdminExamination\Http\Controllers\BaseController;
use Touge\AdminExamination\Models\PaperCategory;
use Touge\AdminExamination\Types\GradationType;
use Touge\AdminOverwrite\Grid\Displayers\Actions;
use Touge\AdminOverwrite\Grid\Grid;

class PaperCategoryController extends BaseController
{
    use HasGradationResourceActions;

    protected $header_text= '';

    public function __construct()
    {
        $gradation= request('gradation', 'all');
        $this->push_breadcrumb(
            [
                'text'=> trans("admin-examination::paper.categories.module-name"),
                'url'=> route('exams.paper-category.index', ['gradation'=> $gradation])
            ]
        );

        $this->header_text= $this->gradation_header($gradation) . __('admin-examination::paper.categories.module-name');
    }

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index($gradation, Content $content)
    {
        $this->set_breadcrumb($content);

        return $content
            ->header($this->header_text)
            ->description(__('admin.list'))
            ->body($this->grid($gradation));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($gradation, $id, Content $content)
    {
        $this->push_breadcrumb(['text'=> trans("admin.edit")])
            ->set_breadcrumb($content);
        return $content
            ->header($this->header_text)
            ->description(__('admin.edit'))
            ->body($this->form($gradation)->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create($gradation, Content $content)
    {
        $this->push_breadcrumb(['text'=> trans("admin.create")])
            ->set_breadcrumb($content);

        return $content
            ->header($this->header_text)
            ->description(__('admin.create'))
            ->body($this->form($gradation));
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid($gradation)
    {
        $grid = new Grid(new PaperCategory());
        $modal= $grid->model();

        $gradation_id= GradationType::idx($gradation);
        if($gradation_id>0){
            $modal->where(['gradation_id'=>$gradation_id]);
        }
        $modal->orderBy('id', 'DESC');


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
    protected function form($gradation)
    {
        $form = new Form(new PaperCategory);

        $form->text('name', __('admin.name'));
        $form->number('sort_order', __('admin.order'))->default(50);

        $gradation_id= GradationType::idx($gradation);
        if($gradation_id==0){
            $gradation_options= $this->gradation_options();
            $form->radio('gradation_id', GradationType::GRADATION_NAME)->options($gradation_options)->default(array_keys($gradation_options)[0]);
        }else{
            $form->hidden('gradation_id')->default($gradation_id);
        }

        $form->disableViewCheck()->disableEditingCheck()->disableCreatingCheck();
        $form->tools(function(Form\Tools $tools){
           $tools->disableView()->disableDelete();//->disableList();
        });

        return $form;
    }

}
