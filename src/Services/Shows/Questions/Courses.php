<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2019-11-20
 * Time: 16:04
 */

namespace Touge\AdminExamination\Services\Shows\Questions;


use App\Modules\Exams\Models\ExamQuestion;
use App\Modules\Exams\Models\ExamQuestionAnalysis;
use Encore\Admin\Show\AbstractField;
use Encore\Admin\Widgets\Table;

class Courses extends AbstractField
{
    public $escape = false;
    public $border = true;


    /**
     * @param ExamQuestion|null $question
     * @return mixed|string
     */
    public function render(ExamQuestion $question=null)
    {
        return $this->table($question);
    }

    /**
     *
     * @param ExamQuestion $question
     * @return string
     */
    protected function table(ExamQuestion $question)
    {
        $rows= [];

        $string= '';
        foreach($question->courses as $course){
//            $string.= '<span class="lable label-success">' . $course->course->name . '</span> &nbsp;';
            array_push($rows, $course->course->name);
        }
//        return $string;
        return implode('&nbsp;,&nbsp;', $rows);
    }

}