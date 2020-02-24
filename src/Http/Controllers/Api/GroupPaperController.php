<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2020-01-06
 * Time: 15:56
 */

namespace Touge\AdminExamination\Http\Controllers\Api;

use Touge\AdminExamination\Facades\GroupPaper;
use Touge\AdminExamination\Http\Controllers\BaseApiController;

/**
 * 试卷组别接口
 *
 * Class GroupPaperController
 * @package Touge\AdminExamination\Http\Controllers\Api
 */
class GroupPaperController extends BaseApiController
{
    /**
     * 指定组别试卷列表
     *
     * @param $group_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function papers($group_id)
    {
        $data= GroupPaper::group_papers($group_id);

        return $this->success($data);
    }
}