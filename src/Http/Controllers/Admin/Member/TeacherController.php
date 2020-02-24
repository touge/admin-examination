<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2020-01-02
 * Time: 15:40
 */

namespace Touge\AdminExamination\Http\Controllers\Admin\Member;

use Touge\AdminExamination\Http\Controllers\BaseController;


/**
 * 教师管理
 *
 * Class TeacherController
 * @package Touge\AdminExamination\Http\Controllers\Admin\Member
 */
class TeacherController extends BaseController
{
    use GlobalTrait;

    /**
     * StudentController constructor.
     */
    public function __construct()
    {
        $this->moduleName= __("admin-examination::member.teacher-admin");
        $this->push_breadcrumb(['text'=> $this->moduleName, 'url'=> route('exams.student.index')]);

        $this->identity= 1;

    }
}