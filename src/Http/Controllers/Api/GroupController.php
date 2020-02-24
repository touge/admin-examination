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
use Touge\AdminExamination\Facades\CustomerMember;
//use Touge\AdminExamination\Facades\Paper;
use Touge\AdminExamination\Http\Controllers\BaseApiController;
//use Touge\AdminExamination\Types\GradationType;

class GroupController extends BaseApiController
{
    public function user_groups(Request $request)
    {
//        $gradation= $request->get('gradation' ,'all');
//        $gradation_id= GradationType::idx($gradation);

        $paper_groups= CustomerMember::paper_groups($this->user()->id)->toArray();
        return $this->success($paper_groups);

    }
}