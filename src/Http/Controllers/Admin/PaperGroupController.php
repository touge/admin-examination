<?php

namespace Touge\AdminExamination\Http\Controllers\Admin;

use Encore\Admin\Admin;
use Touge\AdminExamination\Http\Controllers\Admin\Traits\HasGradationResourceActions;
use Touge\AdminExamination\Http\Controllers\BaseController;
use Touge\AdminExamination\Models\Member;
use Touge\AdminExamination\Models\MemberGroup;
use Encore\Admin\Form;
use Touge\AdminExamination\Models\Paper;
use Touge\AdminExamination\Models\PaperGroup;
use Touge\AdminExamination\Types\GradationType;
use Touge\AdminOverwrite\Grid\Displayers\Actions;
use Touge\AdminOverwrite\Grid\Grid;
use Encore\Admin\Layout\Content;

class PaperGroupController extends BaseController
{
    use HasGradationResourceActions;

    /**
     * @var array|string|null
     */
    protected $moduleName;

    public function __construct()
    {
        $this->moduleName= __("admin-examination::paper.group.module-name");

        $gradation= request('gradation', 'all');
        $this->push_breadcrumb(
            [
                'text'=> $this->moduleName,
                'url'=> route('exams.paper-group.index', ['gradation'=> $gradation])
            ]
        );
    }

    /**
     * Index interface.
     *
     * @param $gradation
     * @param Content $content
     * @return Content
     */
    public function index($gradation, Content $content)
    {
        $this->set_breadcrumb($content);
        return $content
            ->header($this->moduleName)
            ->description(__('admin.list'))
            ->body($this->grid($gradation));
    }

    /**
     * Edit interface.
     *
     * @param $gradation
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($gradation, $id, Content $content)
    {
        $this->push_breadcrumb(['text'=> __("admin.edit")])
            ->set_breadcrumb($content);
        return $content
            ->header($this->moduleName)
            ->description(__('admin.edit'))
            ->body($this->form($gradation)->edit($id));
    }

    /**
     * Create interface.
     *
     * @param $gradation
     * @param Content $content
     * @return Content
     */
    public function create($gradation, Content $content)
    {
        $this->push_breadcrumb(['text'=> __("admin.create")])
            ->set_breadcrumb($content);

        return $content
            ->header($this->moduleName)
            ->description(__('admin.create'))
            ->body($this->form($gradation));
    }

    /**
     * Make a grid builder.
     *
     * @param $gradation
     * @return Grid
     */
    protected function grid($gradation)
    {
        $grid = new Grid(new PaperGroup());
        $modal= $grid->model();

        $gradation_id= GradationType::idx($gradation);
        if($gradation_id>0){
            $modal->where(['gradation_id'=>$gradation_id]);
        }
        $modal->orderBy('id', 'DESC');


        $grid->id('Id');
        $grid->name(__('admin.name'));
        $grid->updated_at(__('admin.updated_at'));

        $grid->disableColumnSelector()
            ->disableRowSelector()
            ->disableExport()
            ->disableFilter();
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
        $form = new Form(new PaperGroup);


        $form->tab(__('admin-examination::paper.group.basic-info'), function ($form) use($gradation){
            $form->text('name', __('admin.name'));

            $gradation_id= GradationType::idx($gradation);
            if($gradation_id==0){
                $gradation_options= $this->gradation_options();
                $form->radio('gradation_id', GradationType::GRADATION_NAME)->options($gradation_options)->default(array_keys($gradation_options)[0]);
            }else{
                $form->hidden('gradation_id')->default($gradation_id);
            }

            $form->textarea('description', __('admin.description'));
        });


        $form->disableCreatingCheck()->disableEditingCheck()->disableViewCheck();
        $form->tools(function(Form\Tools $tools){
            $tools->disableView()->disableDelete();
        });
        $this->relation_members($form)->tab(__('admin-examination::paper.group.relation-paper'), function(Form $form){
            $paper_options= Paper::all()->pluck('title','id');
            $form->listbox('papers', __('admin-examination::paper.group.relation-paper'))->options($paper_options);
        });


        return $form;
    }

    /**
     * 处理老师和学生的组关联
     *
     * @param $form
     * @return mixed
     */
    protected function relation_members($form){
        /**
         * 关联数据
         */
        $form->tab(__('admin-examination::paper.group.relation-member'), function ($form) {
            $teacher_options= Member::where(['identity'=> 1])->get()->pluck('name','id');
            $form->multipleSelect('teachers', __('admin-examination::paper.group.teacher'))->options($teacher_options);

            $student_options= Member::where(['identity'=>0])->get()->pluck('name', 'id');
            $form->listbox('students', __('admin-examination::paper.group.student'))->options($student_options);
        });

        $form->submitted(function(Form $form){
            $form->ignore(['teachers', 'students']);
        });

        $form->saving(function(Form $form)
        {
            $teachers= request('teachers');
            array_pop($teachers);
            $students= request('students');
            array_pop($students);
            $relations= array_merge($teachers,$students);
            $relations[]= null;
            $form->relation_members= $relations;
        });

        $relation_element= 'relation_members';
        $form->listbox($relation_element)->attribute(['id'=>$relation_element]);
        $script= <<<EOF
var relation_element= $("#{$relation_element}").parent().parent()
relation_element.addClass('hidden')
EOF;
        Admin::script($script);

        return $form;
    }
}
