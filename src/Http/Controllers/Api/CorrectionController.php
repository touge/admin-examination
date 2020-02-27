<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2019-12-10
 * Time: 17:55
 */

namespace Touge\AdminExamination\Http\Controllers\Api;

use Illuminate\Http\Request;
use Touge\AdminExamination\Facades\Api\Correction;
use Touge\AdminExamination\Facades\Api\PaperExam;
use Touge\AdminExamination\Http\Controllers\BaseApiController;

/**
 * 批改试卷
 *
 * Class CorrectionController
 * @package Touge\AdminExamination\Http\Controllers\Api
 */
class CorrectionController extends BaseApiController
{
    /**
     * @param Request $request
     * @return array [user_id,custom_school_id:当前放入到院校订单中的学校ID，gradation_id: 当前学校的阶段,school_id: custom_school_id的原始数据源学校]
     */
    protected function options(Request $request){
        return [
            'user_id'=> $this->user()->id,
            'customer_school_id'=> $this->user()->customer_school_id,
            'gradation_id'=> $request->get('gradation_id'),
            'school_id'=> $request->get('school_id'),
        ];
    }

    /**
     * 判断是否为老师
     * @throws \Touge\AdminExamination\Exceptions\ResponseFailedException
     */
    protected function check_identity()
    {
        if($this->user()->identity==0) {
            $this->failed('对不起，您无权限执行此操作');
        }
    }


    /**
     * 我能批阅的试卷列表
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Touge\AdminExamination\Exceptions\ResponseFailedException
     */
    public function fetch_list(Request $request)
    {
        $this->check_identity();

        $options= $this->options($request);
        $options['paginate']= $request->get('paginate', ['current'=> 1, 'limit'=> 5]);


        $data= Correction::paper_exam_list($options);
        return $this->success($data);
    }


    /**
     * 获得一个考卷信息
     *
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Touge\AdminExamination\Exceptions\ResponseFailedException
     */
    public function fetch_one($id, Request $request)
    {
        $this->check_identity();
        $options= $request->get('user', ['user'=> 0, 'name'=> null]);
        $data= PaperExam::fetch_one($id, $options);
        return $this->success($data);
    }


    /**
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Touge\AdminExamination\Exceptions\ResponseFailedException
     */
    public function update(Request $request){
        $this->check_identity();
        $options= [
            'got_score'=> $request->get('got_score'),
            'questions'=> $request->get('exam_questions'),
            'user'=> $request->get('user')
        ];
        $data= PaperExam::update($request->get('exam_id'), $options);

        return $this->success($data);
    }
}