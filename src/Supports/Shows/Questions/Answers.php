<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2019-11-20
 * Time: 16:03
 */

namespace Touge\AdminExamination\Supports\Shows\Questions;

use Encore\Admin\Show\AbstractField;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Table;
use Touge\AdminExamination\Models\Question;
use Touge\AdminExamination\Types\QuestionType;

class Answers extends AbstractField
{
    public $escape = false;
    public $border = true;

    /**
     * @param ExamQuestion|null $question
     * @return Table|mixed|string
     */
    public function render(Question $question=null)
    {
        return $this->table($question);
    }


    /**
     * @param ExamQuestion $question
     * @return string
     */
    protected function table(Question $question)
    {
        $headers = ['序号', '选项'];
        $rows = [];

        if ($question->answers){
            foreach ($question->answers as $key=> $answer)
            {
                array_push($rows, [$key + 1 . '.', $answer->answer]);
            }
        }


        $table= new Table($headers, $rows);
        $box = new Box(QuestionType::text($question->type),$table->render());

        return $box;
    }
}