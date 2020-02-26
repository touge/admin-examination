<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2020-02-25
 * Time: 10:27
 */

namespace Touge\AdminExamination\Http\Controllers\Api;
use Illuminate\Http\Request;
use Touge\AdminExamination\Facades\Api\Paper;
use Touge\AdminExamination\Http\Controllers\BaseApiController;


class PaperController extends BaseApiController
{
    /**
     * 试卷列表
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request){
        $options= $this->options($request);
        $data= Paper::fetch_list($options);

        return $this->success($data);
    }


    /**
     * 产生一个试卷
     *
     * @param $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function uuid($uuid)
    {
        $paper_questions= [];
        $paper= Paper::uuid($uuid, 'alias');
        if($paper)
        {
            $paper_questions= Paper::paper_questions($paper['id'], false);
        }

        $data= ['paper'=>$paper, 'questions'=>$paper_questions];
        return $this->success($data);
    }

    /**
     * @param Request $request
     * @return array [user_id,custom_school_id:当前放入到院校订单中的学校ID，gradation_id: 当前学校的阶段,school_id: custom_school_id的原始数据源学校]
     */
    protected function options(Request $request){
        return [
            'user_id'=> $this->user()->id,
            'custom_school_id'=> $this->user()->customer_school_id,
            'gradation_id'=> $request->get('gradation_id'),
            'school_id'=> $request->get('school_id'),
        ];
    }
}