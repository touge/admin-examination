<?php

namespace Touge\AdminExamination\Http\Controllers\Admin;

use Encore\Admin\Layout\Content;

use Encore\Admin\Show;
use Illuminate\Http\Request;

use Illuminate\Support\Str;
use Touge\AdminExamination\Facades\Question as QuestionService;
use Touge\AdminExamination\Models\Question as QuestionModal;

use Touge\AdminExamination\Http\Controllers\Admin\Traits\QuestionResourceActions;
use Touge\AdminExamination\Http\Controllers\BaseController;
use Touge\AdminExamination\Supports\Shows\Questions\Analyses;
use Touge\AdminExamination\Supports\Shows\Questions\Answers;
use Touge\AdminExamination\Supports\Shows\Questions\Options;
use Touge\AdminExamination\Types\QuestionType;

use Touge\AdminOverwrite\Grid\Displayers\Actions;
use Touge\AdminOverwrite\Grid\Grid;

class QuestionController extends BaseController
{
    use QuestionResourceActions;

    /**
     * QuestionController constructor.
     */
    public function __construct()
    {
        $this->push_breadcrumb(['text'=> trans("admin-examination::question.module-name"), 'url'=> route('exams.question.index')]);
        parent::__construct();
    }


    /**
     * 提供给生成试卷的接口，弹窗搜索试题
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function search(Request $request)
    {
        $search_page_limit= 8;

        /**
         * 窗口
         */
        if($request->isMethod('get'))
        {
            $question_types= QuestionType::getList();
            $data['question_types']= $question_types;

            $group_tags = QuestionService::question_group_tags();
            $data['group_tags']= $group_tags;
            $data['paginate']['limit']= $search_page_limit;
            return view('admin-examination::question.modal.search' ,compact('data'));
        }

        $options= [
            'type'=> $request->get('type'),
            'question'=> $request->get('question'),
            'paginate'=> $request->get('paginate', ['current'=> 1, 'limit'=> $search_page_limit]),
        ];

        $questions= QuestionService::search($options);
        return response()->json($questions);
    }

    /**
     * 试题预览，提供给生成试卷的接口
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function previews(Request $request)
    {
        $ids= $request->get('ids', []);
        $questions= QuestionService::paper_view_questions($ids);

        return response()->json([
            'status'=> 'successful',
            'questions'=> $questions
        ]);
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
            ->header(__('admin-examination::module.name'))
            ->description(__('admin.list'))
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {

        $grid = new Grid(new QuestionModal());
        $model= $grid->model();

        $model->where(['customer_school_id'=> $this->customer_school_id()])->orderBy('id','desc');

        $grid->id(__('admin-examination::question.id'));
        $grid->alias( __('admin-examination::question.alias'));
        $grid->question(__('admin-examination::question.title'))->display(function($val){
            return Str::limit($val,30, '...');
        });
        $grid->type(__('admin-examination::question.type'))->display(function($row){
            return QuestionType::text($row);
        });
        $grid->column('updated_at', __('admin.updated_at'));

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
            ->header(__('admin-examination::question.module-name'))
            ->description(__('admin.edit'))
            ->body($this->form($id));
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
            ->header(__('admin-examination::module.name'))
            ->description(__('admin.create'))
            ->body($this->form());
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
        $this->push_breadcrumb(['text'=> trans("admin.show")])
            ->set_breadcrumb($content);
        return $content
            ->header(__('admin-examination::module.name'))
            ->description(__('admin.show'))
            ->body($this->detail($id));
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(QuestionModal::findOrFail($id));
        $show->alias(__('admin-examination::question.alias'));
        $show->type(__('admin-examination::question.type'))->as(function ($value){
            return QuestionType::text($value);
        })->label();
        $show->question(__('admin-examination::question.title'))->label('primary');

        /**
         * 试题项目
         */
        $show->divider();
        $question_type= $show->getModel()->type;
        switch ($question_type){
            case QuestionType::SINGLE_CHOICE:
            case QuestionType::MULTI_CHOICES:
            case QuestionType::TRUE_FALSE:
                Show::extend('question_options', Options::class);
                $show->field('QUESTION-OPTIONS', __('admin-examination::question.item'))->question_options($show->getModel());
                break;
            case QuestionType::FILL:
            case QuestionType::TEXT:
                Show::extend('question_answers', Answers::class);
                $show->field('QUESTION-ANSWERS', __('admin-examination::question.item'))->question_answers($show->getModel());
                break;
        }

        /**
         * 试题解析关联
         */
        $show->divider();
        Show::extend('question_analyses', Analyses::class);
        $show->field('QUESTION-ANALYSES',__('admin-examination::question.analysis'))->question_analyses($id)->label();


        $show->updated_at(__('admin.updated_at'));

        $show->panel()->tools(function ($tools) {
            $tools->disableDelete();
        });
        return $show;
    }

    /**
     * 表单-创建
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function form($id=0)
    {
        $data= QuestionService::get_form_data($id);
        $group_tags = QuestionService::question_group_tags();

        return view('admin-examination::question.form', compact('data', 'group_tags', 'id'));
    }
}
