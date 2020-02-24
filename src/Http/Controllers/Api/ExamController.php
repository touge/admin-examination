<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2019-11-26
 * Time: 12:56
 */

namespace Touge\AdminExamination\Http\Controllers\Api;


use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Touge\AdminExamination\Facades\Exam;
use Touge\AdminExamination\Facades\Paper;
use Touge\AdminExamination\Http\Controllers\BaseApiController;

/**
 * 考试中心
 *
 * Class ExamController
 * @package Touge\AdminExamination\Http\Controllers\Api
 */
class ExamController extends BaseApiController
{
    /**
     * 获得一张试卷
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $data= Exam::store($request->get('paper_id'));
        return $this->success($data);
    }

    /**
     * 保存用户提交的考试信息
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function save_questions(Request $request)
    {
        $options= [
            'is_submit'=> $request->get('is_submit')==1? true: false,
            'user_id'=> $this->user()->id,
            'exam_id'=> $request->get('exam_id'),
            'paper_uuid'=> $request->get('paper_uuid'),
            'questions'=> $request->get('questions')
        ];
        $data= Exam::save_questions($options);

        return $this->success($data);
    }

    /**
     * 当前用户能使用的考卷列表
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function fetch_list(Request $request){
        $options= [
            'paginate'=> $request->get('paginate', ['current'=> 1, 'limit'=> 5]),
            'user'=> $this->user(),
            'filter'=> $request->get('filter', 'all')
        ];
        $data= Exam::fetch_list($options);
        return $this->success($data);
    }

    /**
     * 考试
     * @param $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function uuid($uuid): JsonResponse
    {
        $paper_questions= [];
        $paper= Exam::paper($uuid, 'alias');
        if($paper)
        {
            $paper_questions= Exam::paper_questions($paper['id'], false);
        }

        $data= ['paper'=>$paper, 'questions'=>$paper_questions];
        return $this->success($data);
    }
}