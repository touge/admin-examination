<?php

namespace Touge\AdminExamination\Http\Controllers\Admin;

use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Touge\AdminExamination\Http\Controllers\BaseController;
use Touge\AdminExamination\Models\QuestionTag;
use Touge\AdminExamination\Models\QuestionTagGroup;


use Touge\AdminOverwrite\Grid\Grid;
use Touge\AdminOverwrite\Grid\Displayers\Actions;

class TagController extends BaseController
{
    use HasResourceActions;

    public function __construct()
    {
        $this->push_breadcrumb(['text'=> trans("admin-examination::question.tag.module-name"), 'url'=> route('exams.tag.index')]);
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
            ->header(__('admin-examination::question.tag.module-name'))
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
            ->header(__('admin-examination::question.tag.module-name'))
            ->description('description')
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
            ->header(__('admin-examination::question.tag.module-name'))
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
            ->header(__('admin-examination::question.tag.module-name'))
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
        $grid = new Grid(new QuestionTag());

        $grid->id('Id');
        $grid->column( 'group.title', __('admin-examination::question.tag.group-name'));
        $grid->column( 'title', __('admin-examination::question.tag.title'));
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
        $show = new Show(QuestionTag::findOrFail($id));

        $show->id('Id');
        $show->group_id('Group id');
        $show->title('Title');
        $show->cover('Cover');
        $show->description('Description');
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
        $form = new Form(new QuestionTag());

        $tag_group_options= QuestionTagGroup::all()->pluck('title','id');
        $form->select('group_id', __('admin-examination::question.tag.group-name'))->options($tag_group_options);


        $form->text('title', __('admin-examination::question.tag.title'));
        $form->textarea('description', __('admin.description'));

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
