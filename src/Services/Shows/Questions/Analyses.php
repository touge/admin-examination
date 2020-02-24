<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2019-11-20
 * Time: 16:04
 */

namespace Touge\AdminExamination\Services\Shows\Questions;


use Encore\Admin\Show\AbstractField;
use Encore\Admin\Widgets\Table;
use Touge\AdminExamination\Models\QuestionAnalysis;

class Analyses extends AbstractField
{
    public $escape = false;
    public $border = true;

    /**
     * @param string $qid
     * @return Table|mixed
     */
    public function render($qid='')
    {
        return $this->table($qid);
    }

    /**
     *
     * @param $question_id
     * @return string
     */
    protected function table($question_id): string
    {
        $analyses= QuestionAnalysis::select('id','analysis')
            ->where(['question_id'=> $question_id])
            ->first();
        return $analyses->analysis;
    }

}