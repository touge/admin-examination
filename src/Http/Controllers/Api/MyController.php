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
use Touge\AdminExamination\Facades\Api\My;
use Touge\AdminExamination\Http\Controllers\BaseApiController;

class MyController extends BaseApiController
{
    /**
    /**
     * 我的考试数据
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $options= $this->options($request);

        $data= My::fetch_list($options);

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
            'paginate'=> $request->get('paginate', ['current'=> 1, 'limit'=> 5]),
        ];
    }

}