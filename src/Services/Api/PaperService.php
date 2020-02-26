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

//"id": 69,
//"category_id": 1,
//"alias": "YKCGA1GI0VIH",
//"title": "考试科目3",
//"is_public": 0,
//"gradation_id": 1,
//"time_limit_enable": 0,
//"time_limit_value": 90,
//"pass_score": 5,
//"total_score": 5,
//"question_number": 1,
//"created_at": "2019-12-28 13:52:08",
//"updated_at": "2020-02-25 09:51:55"

    public function fetch_list(Array $params)
    {
        $paper_list= Paper::where(['gradation_id' =>$params['gradation_id']])
            ->select(['id','alias','category_id','title','question_number','total_score','time_limit_value','created_at'])
            ->orderBy('id' ,'desc')
            ->get();

        return $paper_list;
    }

//"id": 1,
//"parent_id": 0,
//"gradation_id": 1,
//"name": "企业培训",
//"sort_order": 50,
//"created_at": "2019-11-22 17:26:57",
//"updated_at": "2020-02-24 19:29:31"
    /**
     * 当前请求的试卷分类
     *
     * @param array $params
     * @return mixed
     */
    public function categories(Array $params){
        $categories= PaperCategory::where(['gradation_id'=>$params['gradation_id']])
            ->select(['id', 'gradation_id', 'name', 'created_at'])
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
            'gradation_id',
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