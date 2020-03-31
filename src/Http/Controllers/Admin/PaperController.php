<?php

namespace Touge\AdminExamination\Http\Controllers\Admin;

use Encore\Admin\Form;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
use Touge\AdminExamination\Facades\Paper;
use Touge\AdminExamination\Http\Controllers\BaseController;
use Touge\AdminExamination\Models\Paper as PaperModel;
use Touge\AdminExamination\Models\PaperExams;
use Touge\AdminOverwrite\Grid\Displayers\Actions;
use Touge\AdminOverwrite\Grid\Grid;

/**
 * 试卷管理
 *
 * Class PaperController
 * @package Touge\AdminExamination\Http\Controllers\Admin
 */
class PaperController extends BaseController
{
    /**
     * QuestionController constructor.
     */
    public function __construct()
    {
        $this->push_breadcrumb(
            [
                'text'=> trans("admin-examination::paper.module-name"),
                'url'=> route('exams.paper.index')
            ]
        );
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
            ->header(__('admin-examination::paper.module-name'))
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
    public function show($gradation, $id, Content $content)
    {
        return '';
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
            ->header(__('admin-examination::paper.module-name'))
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
        $this->push_breadcrumb(['text'=> trans("admin-examination::paper.paper-create")])
            ->set_breadcrumb($content);

        return $content
            ->header(__('admin-examination::paper.paper-create'))
            ->description(__('admin.create'))
            ->body($this->form(0));
    }



    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new PaperModel);
        $modal= $grid->model();

        $modal->where(['customer_school_id'=> $this->customer_school_id()])
            ->orderByDesc('expired_at')
            ->orderByDesc('id');


        $grid->id('ID');
        $grid->title(__('admin-examination::paper.title'));

        $grid->time_limit_enable(__('admin-examination::paper.time-limit-enable'))->states();
        $grid->pass_score(__('admin-examination::paper.pass-score'));
        $grid->total_score(__('admin-examination::paper.total-score'));
        $grid->expired_at(__('admin-examination::paper.expired-at'));

        $grid->disableRowSelector()
            ->disableColumnSelector()
            ->disableFilter()
            ->disableExport();
        $grid->actions(function(Actions $actions){

//            $exam_total= PaperExams::where(['paper_id'=> $actions->row->id])->count();
//            dump($exam_total);
//            if($exam_total > 0){
//                $actions->disableEdit();
//            }
           $actions->disableView();
        });

        return $grid;
    }

    /**
     * Make a form builder.
     * @param int $id
     * @return Form
     */
    protected function form($id)
    {
        $options= ['id'=>$id, 'customer_school_id'=> $this->customer_school_id()];
        $data= Paper::get_form_data($options);

        $is_used= 0;
        if($id > 0){
            $is_used= PaperExams::where(['paper_id'=> $id])->count() >0 ?1 :0;
        }
        return view('admin-examination::paper.form', compact('data', 'is_used'));
    }



    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $question= Paper::store($request);
        if(!$question){
            return response()->json([
                'status'=> 'failed',
                'message'=> '数据录入失败',
            ]);
        }
        return response()->json([
            'status'=> 'successful',
            'question'=> $question->toArray()
        ]);
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id,Request $request)
    {
        $question= Paper::update($request ,$id);

        if(!$question){
            return response()->json([
                'status'=> 'failed',
                'message'=> '数据录入失败',
            ]);
        }
        return response()->json([
            'status'=> 'successful',
            'question'=> $question->toArray()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        /**
         * 检测关联数据
         */
        $paper_exams= PaperExams::where(['paper_id'=> $id])->count();

        if($paper_exams>0){
            $response = [
                'status'  => false,
                'message' => "试卷已经被使用，不允许删除！<br>请编辑截止日期使其失效，在前台隐藏。",
            ];
            return response()->json($response);
        }


        $form= new Form(new PaperModel);
        if ($form->destroy($id)) {
            $data = [
                'status'  => true,
                'message' => trans('admin.delete_succeeded'),
            ];
        } else {
            $data = [
                'status'  => false,
                'message' => trans('admin.delete_failed'),
            ];
        }

        return response()->json($data);
    }
}
