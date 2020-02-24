<?php

namespace Touge\AdminExamination\Http\Controllers\Admin;

use Encore\Admin\Form;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
use Touge\AdminExamination\Facades\Paper;
use Touge\AdminExamination\Http\Controllers\BaseController;
use Touge\AdminExamination\Models\Paper as PaperModel;
use Touge\AdminExamination\Types\GradationType;
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
        $gradation= request('gradation', 'all');
        $this->push_breadcrumb(
            [
                'text'=> trans("admin-examination::paper.module-name"),
                'url'=> route('exams.paper.index', ['gradation'=> $gradation])
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
    public function index($gradation, Content $content)
    {
        $this->set_breadcrumb($content);
        return $content
            ->header(__('admin-examination::paper.module-name'))
            ->description(__('admin.list'))
            ->body($this->grid($gradation));
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
    public function edit($gradation, $id, Content $content)
    {
        $this->push_breadcrumb(['text'=> trans("admin.edit")])
            ->set_breadcrumb($content);
        return $content
            ->header(__('admin-examination::paper.module-name'))
            ->description(__('admin.edit'))
            ->body($this->form($gradation, $id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create($gradation, Content $content)
    {
        $this->push_breadcrumb(['text'=> trans("admin-examination::paper.paper-create")])
            ->set_breadcrumb($content);

        return $content
            ->header(__('admin-examination::paper.paper-create'))
            ->description(__('admin.create'))
            ->body($this->form($gradation, 0));
    }



    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid($gradation)
    {
        $grid = new Grid(new PaperModel);
        $modal= $grid->model();

        $gradation_id= GradationType::idx($gradation);
        if($gradation_id>0){
            $modal->where(['gradation_id'=>$gradation_id]);
        }
        $modal->orderBy('id', 'DESC');


        $grid->id('ID');
        $grid->title(__('admin-examination::paper.title'));

        $grid->time_limit_enable(__('admin-examination::paper.time-limit-enable'))->states();
        $grid->pass_score(__('admin-examination::paper.pass-score'));
        $grid->total_score(__('admin-examination::paper.total-score'));

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
     * @param int $id
     * @return Form
     */
    protected function form($gradation, $id)
    {
        $options= ['id'=>$id, 'gradation'=>$gradation];
        $data= Paper::get_form_data($options);
        return view('admin-examination::paper.form', compact('data'));
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
    public function update($gradation, $id,Request $request)
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
    public function destroy($gradation, $id)
    {
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
