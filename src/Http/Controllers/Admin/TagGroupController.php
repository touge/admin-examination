<?php

namespace Touge\AdminExamination\Http\Controllers\Admin;

use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Touge\AdminExamination\Http\Controllers\BaseController;
use Touge\AdminExamination\Models\QuestionTagGroup;
use Touge\AdminOverwrite\Grid\Displayers\Actions;
use Touge\AdminOverwrite\Grid\Grid;

class TagGroupController extends BaseController
{
    use HasResourceActions;


    public function __construct()
    {
        $this->push_breadcrumb(['text'=> trans("admin-examination::question.tag-group.module-name"), 'url'=> route('exams.tag-group.index')]);
        parent::__construct();
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
            ->header(__('admin-examination::question.tag-group.module-name'))
            ->description(__('admin.list'))
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header(__('admin-examination::question.tag-group.module-name'))
            ->description(__('admin.show'))
            ->body($this->detail($id));
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
            ->header(__('admin-examination::question.tag-group.module-name'))
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
            ->header(__('admin-examination::question.tag-group.module-name'))
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
        $grid = new Grid(new QuestionTagGroup());
        $grid->model()->orderBy('sort_order', 'ASC');
        $grid->model()->orderBy('id', 'DESC');

        $grid->id('Id');
        $grid->title(__('admin-examination::question.tag-group.title'));
        $grid->sort_order(__('admin-examination::question.tag-group.sort-order'));
        $grid->updated_at(__('admin.updated_at'));

        $grid->disableFilter()
            ->disableColumnSelector()
            ->disableExport()
            ->disableRowSelector();
        $grid->actions(function (Actions $actions){
            $actions->disableView();
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(QuestionTagGroup::findOrFail($id));

        $show->id('Id');
        $show->title('Title');
        $show->parent_id('Parent id');
        $show->sort('Sort');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new QuestionTagGroup());

        $form->number('sort_order', __('admin-examination::question.tag-group.sort-order'))->default(50);
        $form->text('title', __('admin-examination::question.tag-group.title'));

        $form->tools(function(Form\Tools $tools){
            $tools->disableView()
                ->disableDelete();
        });
        $form->disableCreatingCheck()
            ->disableEditingCheck()
            ->disableViewCheck();
        return $form;
    }
}
