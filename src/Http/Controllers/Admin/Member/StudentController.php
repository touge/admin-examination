<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2020-01-02
 * Time: 15:41
 */

namespace Touge\AdminExamination\Http\Controllers\Admin\Member;


use Touge\AdminExamination\Http\Controllers\BaseController;
use Touge\AdminExamination\Models\Member;

/**
 * 学生管理
 *
 * Class StudentController
 * @package Touge\AdminExamination\Http\Controllers\Admin\Member
 */
class StudentController extends BaseController
{
    use GlobalTrait;

    /**
     * StudentController constructor.
     */
    public function __construct()
    {
        $this->moduleName= __("admin-examination::member.student-admin");
        $this->push_breadcrumb(['text'=> $this->moduleName, 'url'=> route('exams.student.index')]);

        $this->identity= 0;

    }
}