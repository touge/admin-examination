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
use Touge\AdminExamination\Facades\My;
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
        $options= [
            'paginate'=> $request->get('paginate', ['current'=> 1, 'limit'=> 5]),
            'user'=> $this->user(),
            'gradation'=> $request->get('gradation'),
            'filter'=> $request->get('filter', 'all')
        ];
        $data= My::fetch_list($options);
        return $this->success($data);
    }

}