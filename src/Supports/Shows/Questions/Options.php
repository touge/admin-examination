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

class Options extends AbstractField
{
    public $escape = false;
    public $border = true;

    /**
     * @param Question|null $question
     * @return Table|mixed|string
     */
    public function render(Question $question=null)
    {
        return $this->table($question);
    }


    /**
     * @param Question $question
     * @return string
     */
    protected function table(Question $question)
    {
        $headers = ['序号', '选项'];
        $rows = [];
        $answer_rows= [];

        if ($question->options){
            foreach ($question->options as $key=> $option)
            {
                if($option->is_answer==1){
                    array_push($answer_rows, chr(ord('A') + $key));
                }
                array_push($rows, [chr(ord('A') + $key), $option->option]);
            }
            array_push($rows, ['答案：<span style="color:red">'. implode(',', $answer_rows) . ' </span>', '','']);
        }


        $table= new Table($headers, $rows);
        $box = new Box(QuestionType::text($question->type),$table->render());
        return $box;
    }

}