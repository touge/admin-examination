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


use Touge\AdminExamination\Facades\Paper;
use Touge\AdminExamination\Http\Controllers\BaseApiController;
use Touge\AdminExamination\Types\GradationType;

class CategoryController extends BaseApiController
{
    public function index($gradation)
    {
        $gradation_id= GradationType::idx($gradation);

        $categories= Paper::categories($gradation_id)->pluck('name', 'id');

        return $this->success($categories);
    }
}