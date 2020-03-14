<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2020-02-25
 * Time: 10:40
 */

namespace Touge\AdminExamination\Services\Api;


use Touge\AdminExamination\Models\Paper;
use Touge\AdminExamination\Models\PaperCategory;
use Touge\AdminExamination\Models\PaperQuestion;
use Touge\AdminExamination\Services\BaseService;
use Touge\AdminExamination\Types\QuestionType;

class PaperService extends BaseService
{

    /**
     * @param array $params
     * @return mixed
     */
    public function fetch_list(Array $params)
    {
        $paper_list= Paper::where(['customer_school_id' =>$params['customer_school_id']])
            ->select(['id','alias','category_id','title','question_number','total_score','time_limit_value','created_at'])
            ->orderBy('id' ,'desc')
            ->get();

        return $paper_list;
    }

    /**
     * 当前请求的试卷分类
     *
     * @param array $params
     * @return mixed
     */
    public function categories(Array $params){
        $categories= PaperCategory::where(['customer_school_id'=>$params['customer_school_id']])
            ->select(['id', 'name', 'created_at'])
            ->get();

        return $categories;
    }


    /**
     * 试卷查询信息
     * @return array
     */
    protected function paper_preview_filed()
    {
        return [
            'alias as uuid',
            'title',
            'category_id',
            'customer_school_id',
            'is_public',
            'time_limit_enable',
            'time_limit_value',
            'pass_score',
            'total_score',
            'question_number'
        ];
    }

    /**
     * 查询单个试卷信息
     *
     * @param string $key
     * @param string $type
     * @param bool $includeAnswer
     * @return array
     * @throws \Touge\AdminExamination\Exceptions\ResponseFailedException
     */
    public function uuid(string $key, $type='alias', $includeAnswer= false): array
    {
        /**
         * 查询试卷模板信息
         */
        $filed= $this->paper_preview_filed();
        array_push($filed, 'id');

        $paper= Paper::select(...$filed)->where([$type=>$key])->first();

        if(!$paper) $this->throw_error(__('admin-examination::paper.paper-data-null'));

        return $paper->toArray();
    }

    /**
     * 试卷关联的试题详细信息
     *
     * @param $paper_id
     * @param $includeAnswer
     * @return array
     */
    public function paper_questions($paper_id, $includeAnswer= false)
    {
        $questions= [];

        $paper_questions= PaperQuestion::where(['paper_id'=> $paper_id])->get();

        foreach($paper_questions as $key=>$paper_question)
        {
            $rel_question= $paper_question->question;
            $row= [
                'id'=> $paper_question->question_id,
                'score'=> $paper_question->score,
                'question'=> $rel_question->question,
                'type'=> $rel_question->type,
            ];
            switch ($rel_question->type){
                case QuestionType::SINGLE_CHOICE:
                case QuestionType::MULTI_CHOICES:
                case QuestionType::TRUE_FALSE:
                    $select= $includeAnswer? ['id', 'option', 'is_answer'] : ['id', 'option'];
                    $options= $rel_question->options()->get($select);
                    $row['options']= $options->toArray();
                    break;
                case QuestionType::FILL:
                case QuestionType::TEXT:
                    $select= $includeAnswer? ['id', 'answer'] : ['id'];
                    $answers= $rel_question->answers()->get($select);
                    $row['answers']= $answers->toArray();
                    break;
            }
            array_push($questions, $row);

        }
        return $questions;
    }
}