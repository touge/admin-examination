<?php
/**
 *
 * 考试中心分组及分类交互API
 *
 * Created by PhpStorm.
 * User: nick
 * Date: 2019-12-31
 * Time: 19:50
 */

namespace Touge\AdminExamination\Http\Controllers\Api;


use Illuminate\Http\Request;
use Touge\AdminExamination\Http\Controllers\BaseApiController;


use Touge\AdminExamination\Facades\Api\Paper;

class CategoryController extends BaseApiController
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $options= $this->options($request);
        $data= Paper::categories($options)->pluck('name' ,'id');
        return $this->success($data);

    }

    /**
     * @param Request $request
     * @return array [user_id,custom_school_id:当前放入到院校订单中的学校ID，gradation_id: 当前学校的阶段,school_id: custom_school_id的原始数据源学校]
     */
    protected function options(Request $request){
        return [
            'user_id'=> $this->user()->id,
            'customer_school_id'=> $this->user()->customer_school_id,
//            'custom_school_id'=> $this->user()->customer_school_id,
//            'school_id'=> $request->get('school_id'),
        ];
    }
}