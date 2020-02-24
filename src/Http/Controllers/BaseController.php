<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2019-12-18
 * Time: 17:18
 */
namespace Touge\AdminExamination\Http\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Layout\Content;
use Touge\AdminExamination\Types\GradationType;

class BaseController extends Controller
{
    /**
     * 当前系统中的学校id
     * @var
     */
    protected $customer_school_id;

    /**
     * 面包屑
     * @var array
     */
    protected $breadcrumb= [];

    public function __construct()
    {
        $this->customer_school_id= env('ADMIN_CUSTOMER_SCHOOL_ID');
    }

    /**
     * 插入面条屑
     *
     * @param $breadcrumb
     * @return $this
     */
    protected function push_breadcrumb($breadcrumb)
    {
        array_push($this->breadcrumb, $breadcrumb);
        return $this;
    }

    /**
     * 设置面包屑
     * @param Content $content
     *
     * @return $this
     */
    protected function set_breadcrumb(Content $content)
    {
        $content->breadcrumb(...$this->breadcrumb);
        return $this;
    }

    /**
     * 是否显示阶段
     *
     * @return string
     */
    protected function gradation_header($type)
    {
        if (env("HIDDEN_HEADER_GRADATION")==true)
        {
            return '';
        }

        $idx= GradationType::idx($type);
        if($idx==0){
            return '';
        }

        $gradation= GradationType::TYPE_ENUM[$idx];
        $gradation['id']= $idx;

        return $gradation['text'] .' - ';
    }
}