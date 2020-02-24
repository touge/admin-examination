<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2020-01-05
 * Time: 12:15
 */

namespace Touge\AdminExamination\Http\Controllers\Api;


use Touge\AdminExamination\Facades\CustomerMember;
use Touge\AdminExamination\Http\Controllers\BaseApiController;

/**
 * 当前用户试卷组别信息
 *
 * Class PaperGroupController
 * @package Touge\AdminExamination\Http\Controllers\Api
 */
class MemberPaperGroupController extends BaseApiController
{
    public function index()
    {
        $paper_groups= CustomerMember::paper_groups($this->user()->id)->toArray();
        return $this->success($paper_groups);
    }
}