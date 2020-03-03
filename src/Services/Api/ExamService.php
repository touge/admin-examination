<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2019-11-22
 * Time: 17:37
 */


namespace Touge\AdminExamination\Services\Api;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

use Touge\AdminExamination\Models\Paper;
use Touge\AdminExamination\Models\PaperExamQuestions;
use Touge\AdminExamination\Models\PaperExams;
use Touge\AdminExamination\Models\PaperGroupMemberRel;
use Touge\AdminExamination\Models\PaperQuestion;
use Touge\AdminExamination\Services\BaseService;

use Touge\AdminExamination\Types\PaperExamStatus;
use Touge\AdminExamination\Types\QuestionType;

class ExamService extends BaseService
{

    /**
     * 学生考卷入库
     *
     * @param array $params
     * @return mixed
     * @throws \Exception
     */
    protected function create_paper_exam(array $params)
    {
        DB::beginTransaction();
        try{
            /**
             * 试卷入库
             */
            $options= array_merge($params, [
                'start_time'=> Carbon::now(),
                'status'=> 1
            ]);

            $exam= PaperExams::create($options)->toArray();
            $options= ['exam_id'=> $exam['id'], 'paper_id'=> $options['paper_id']];

            if($this->create_or_update_paper_exam_questions($options)==false){
                $this->throw_error('试题数据录入失败');
            }

            DB::commit();
        }catch (\Exception $exception){
            DB::rollBack();
            throw $exception;
        }

        return $exam;
    }


    /**
     * 学生考卷试题入库
     *
     * @param array $params
     * @return boolean
     */
    protected function create_or_update_paper_exam_questions(array $params)
    {
        $exam_id= $params['exam_id'];
        $paper_id= $params['paper_id'];

        /**
         * 模板试卷的试题列表
         */
        $options= ['paper_id'=>$paper_id];
        $selects= ['question_id', 'score'];
        $paper_questions= PaperQuestion::where($options)->get($selects);

        /**
         * 数据更新
         */
        if(array_key_exists('questions', $params))
        {
            $questions= $params['questions'];

            foreach($paper_questions as $key=>$paper_question){
                $row= [
                    'exam_id'=> $exam_id,
                    'question_id'=>$paper_question->question_id,
                    'answer'=> '[]',
                    'score'=>$paper_question->score
                ];
                foreach($questions as $sKey=>$question){
                    if($question['question_id'] == $paper_question->question_id){
                        $row['answer'] = json_encode($question['answers'], JSON_UNESCAPED_UNICODE);
                    }
                }
                $exam_paper_exam_question= PaperExamQuestions::where(['exam_id'=>$exam_id, 'question_id'=> $paper_question->question_id])->first();

                $ret= true;
                if($exam_paper_exam_question){
                    $update_row= ['answer'=>$row['answer']];
                    $_updated= $exam_paper_exam_question->update($update_row);
                    if(!$_updated){
                       return false;
                    }
                }else{
                    if(!PaperExamQuestions::create($row)){
                        return false;
                    }
                }
                if($ret==false){
                    return false;
                }
            }
            return true;
        }


        /**
         * 数据新增
         */
        foreach($paper_questions as $key=>$paper_question) {
            $row= [
                'exam_id'=> $exam_id,
                'question_id'=>$paper_question->question_id,
                'answer'=> '[]',
                'score'=>$paper_question->score
            ];
            $ret= PaperExamQuestions::create($row);
            if(!$ret){
                return false;
            }
        }
        return true;
    }

    /**
     * 判断当前试卷是否已经过期
     * @param array $params
     * @return bool
     */
    protected function check_exam_expire(array $params): bool
    {
        if ($params['time_limit_enable']) {
            $time_left_second = $params['time_limit_value'] * 60 - (time() - strtotime($params['start_time']));
            if ($time_left_second <= 0) {

                return false;
            }
        }
        return true;
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
    public function paper(string $key, $type='alias', $includeAnswer= false): array
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
    public function paper_questions($paper_id, $includeAnswer= false): array
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

    /**
     * 为用户创建一个试卷，如果用户已经存在试卷，且没有超过提交时间，则重新给用户，否则给用户提示已经考试过了
     *
     * @param int $params
     * @return array
     * @throws \Exception
     */
    public function store($paper_id)
    {
        $user= auth()->user();
        $paper_exam = PaperExams::where([
            'paper_id'=> $paper_id,
            'user_id'=> $user->id
        ])->first();

        /**
         * 如果当前模板的学生试卷试题已经存在
         */
        if($paper_exam){
            $paper = $this->paper($paper_exam['paper_id'], 'id', true);
            $options= [
                'time_limit_enable'=> $paper['time_limit_enable'],
                'time_limit_value'=> $paper['time_limit_value'],
                'start_time'=> $paper_exam['start_time']
            ];

            /**
             * 对接口提供多个状态
             * 默认试卷已经存在，但未过期
             */
            $state= 'paper_exam_exist';

            /**
             * 试卷已经提交
             */
            if ($paper_exam->status==PaperExamStatus::SUBMITTED){
                $state= 'paper_exam_posted';
            }

            /**
             * 试卷已经过期，提示并修改状态
             */
            if(!$this->check_exam_expire($options))
            {
                $paper_exam->update(['status'=> PaperExamStatus::SUBMITTED]);
                $state= 'paper_exam_timeout';
            }

            $exam_paper_exam_questions= PaperExamQuestions::where([
                'exam_id'=> $paper_exam['id'],
            ])->get(['question_id', 'answer']);

            /**
             * 对answer字段进行解json操作
             */
            $epeq= $exam_paper_exam_questions->each(function($item, $key){
                return $item['answer']= json_decode($item['answer'], TRUE);
            });
            /**
             * 以question_id为key,answer为value返回数组
             */
            $epeq= $epeq->pluck('answer','question_id');

            /**
             * 返回数据信息
             */
            return [
                'state'=>$state,
                'paper_exam'=>$paper_exam,
                'history_questions'=> $epeq
            ];
        }


        /**
         * 为当前用户考试试卷模板进行入库操作
         */
        $options= [
            'paper_id'=> $paper_id,
            'user_id'=> $user->id,
            'user_name'=> $user->name,
            'customer_school_id'=> $user->customer_school_id,
        ];
        $paper_exam= $this->create_paper_exam($options);

        return [
            'state'=> 'created_successful',
            'paper_exam'=> $paper_exam,
            'history_questions'=> ''
        ];
    }

    /**
     * @param array $params
     * @return mixed
     * @throws \Exception
     */
    public function save_questions(array $params)
    {
        DB::beginTransaction();
        try{
            /**
             * 是否为人工主动提交
             */
            $is_submit= $params['is_submit'];

            /**
             * 查找试卷模板
             */
            $paper = $this->paper($params['paper_uuid'], 'alias', true);

            /**
             * 当前用户试卷数据
             */
            $options= [
                'user_id'=> $params['user_id'],
                'paper_id'=> $paper['id'],
            ];
            $paper_exam= PaperExams::where($options)->first();


            /**
             * 基础信息判定
             */
            !$paper_exam && $this->throw_error('试卷信息数据不存在');
            $paper_exam->status != PaperExamStatus::DOING && $this->throw_error('试卷已经提交');


            $options= [
                'time_limit_enable'=> $paper['time_limit_enable'],
                'time_limit_value'=> $paper['time_limit_value'],
                'start_time'=> $paper_exam['start_time']
            ];

            if(!$this->check_exam_expire($options))
            {
                $paper_exam->update([
                    'status'=> PaperExamStatus::SUBMITTED,
                    'end_time'=> Carbon::now(),
                ]);
                $this->throw_error('考试时间已过，试卷已经自动提交');
            }
            $options= [
                'exam_id'=> $paper_exam['id'],
                'paper_id'=> $paper['id'],
                'questions'=> $params['questions']
            ];


            $create_or_update_ret= $this->create_or_update_paper_exam_questions($options);
            if($create_or_update_ret != false){

                /**
                 * 如果为人工主动提交，则更新
                 */
                if ($is_submit)
                {
                    $paper_exam->update([
                        'status'=> PaperExamStatus::SUBMITTED,
                        'end_time'=> Carbon::now(),
                    ]);
                }
                DB::commit();
                return $paper_exam;
            }
            $this->throw_error('试卷提交失败，请联系管理员');
        }catch (\Exception $exception){
            DB::rollBack();
            throw $exception;
        }

    }

    /**
     * 试卷查询信息
     * @return array
     */
    protected function paper_preview_filed(): array
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
     * 用户拥有的试卷列表
     *
     * @param array $params
     * @return mixed
     */
    public function fetch_list(array $params)
    {
        $viewFields= [
            'p.id',
            'p.alias',
            'p.title',
            'p.category_id',
            'p.is_public',
            'p.gradation_id',
            'p.time_limit_enable',
            'p.time_limit_value',
            'p.pass_score',
            'p.total_score',
            'p.question_number',
            'p.updated_at'
        ];

        $paginate= $params['paginate'];
        $filter= $params['filter'];

        $user_papers_query= DB::table('school_customer_paper_group_papers as pgp')
            ->leftJoin('papers as p', 'p.id', '=', 'pgp.paper_id')
            ->select($viewFields);

        if($filter=='all'){
            $user_groups= PaperGroupMemberRel::where(['member_id'=>$params['user']->id])->get()->pluck('group_id');
            $user_papers_query->distinct('pgp.paper_id')
                ->whereIn('pgp.group_id', $user_groups);


        }else{
            $user_papers_query->where(['pgp.group_id'=>$filter]);
        }
        $user_papers_query->orderBy('p.id','desc');
        $user_papers= $user_papers_query->paginate($paginate['limit'],['p.id'], null, $paginate['current']);

        $data['paginate']= [
            'current'=> $user_papers->currentPage(),
            'total'=> $user_papers->total()
        ];
        $data['items']= $user_papers->items();
        return $data;

    }

}