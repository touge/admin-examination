<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2019-11-22
 * Time: 17:37
 */

namespace Touge\AdminExamination\Services;


use Encore\Admin\Facades\Admin;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use mysql_xdevapi\Exception;
use Touge\AdminExamination\Models\Paper;
use Touge\AdminExamination\Models\PaperCategory;
use Touge\AdminExamination\Models\PaperExams;
use Touge\AdminExamination\Models\PaperQuestion;
use Touge\AdminExamination\Types\GradationType;

/**
 * 后台使用，试卷服务类
 *
 * Class PaperService
 * @package Touge\AdminExamination\Services
 */
class PaperService extends BaseService
{
    /**
     * 暂保留两个版本，之后删除
     * admin.examination
     *
     * 存入试卷试题
     *
     * @param array $questions
     * @param Paper $paper
     * @return bool
     * @throws \Touge\AdminExamination\Exceptions\ResponseFailedException
     */
    protected function save_paper_questions(array $questions, Paper $paper){

        $paper_question_map = [];
        $paper_score_total= 0;
        foreach($questions as $index=>$question) {

            /**
             * 判定试题是否重复
             */
            if (isset($paper_question_map[$question['question_id']])) {
                $this->throw_error('第 ' . ($index + 1) . ' 题 与第 ' . $paper_question_map[$question['question_id']] . ' 题重复');
            }


            /**
             * 判定试题是否存在
             */
            if (empty($question['question_id'])) {
                $this->throw_error('第'. ($index+1) . '题不存在');
            }

            /**
             * 判定试题分值是否存在
             * 是否为数值
             * 是否<=0
             */
            if (empty($question['score']) || ($question_score = intval($question['score'])) <=0 ) {
                $this->throw_error('第'. ($index+1) . '题分值设置错误');
            }
            $paper_score_total+= $question_score;
            $paper_question_map[$question['question_id']]= $index+1;
        }

        /**
         * 判定试题分值相加是否为试卷总分相等
         */
        if ($paper_score_total!=$paper['total_score'])
        {
            $this->throw_error('总分设置' . $paper['total_score'] . '和计算' . $paper_score_total . '不相等' );
        }

        foreach($questions as $index=>$question){
            $row= [
                'paper_id'=>$paper->id,
                'question_id'=>$question['question_id'],
                'score'=> $question['score']
            ];
            $res= PaperQuestion::create($row);
            if(!$res){
                return false;
            }
        }
        return true;
    }


    /**
     * admin.examination
     *
     * @param $params
     * @return array
     * @throws \Touge\AdminExamination\Exceptions\ResponseFailedException
     */
    protected function get_paper_options($params){
        $paper= $params->paper;
        $options= [
            'category_id'=> $paper['category_id'],
            'alias'=> $paper['alias'],
            'title'=> $paper['title'],
            'is_public'=> $paper['is_public'],
            'time_limit_enable'=> $paper['time_limit_enable'],
            'time_limit_value'=> $paper['time_limit_value'],
            'pass_score'=> $paper['pass_score'],
            'total_score'=> $paper['total_score'],
            'expired_at'=> $paper['expired_at'],
        ];

        if(!$options['title']){
            $this->throw_error(__('admin-examination::paper.name-null'));
        }
        if(!$options['category_id']){
            $this->throw_error(__('admin-examination::paper.category-null'));
        }
        if (!$options['total_score'] || intval($options['total_score']) <= 0){
            $this->throw_error(__('admin-examination::paper.score-error'));
        }
        if(!$options['pass_score']|| intval($options['pass_score']) <= 0){
            $this->throw_error(__('admin-examination::paper.pass-score-error'));
        }
        return $options;
    }

    /**
     * admin.examination
     *
     * 编辑表单数据
     * @param $params
     * @return array
     */
    protected function edit_data($params): array
    {
        $paper= Paper::findOrFail($params['id']);

        $data= $this->create_data($params['customer_school_id']);

        $data['form']= [
            'category_id'=> $paper->category_id,
            'customer_school_id'=> $params['customer_school_id'],
            'alias'=> $paper->alias,
            'title'=> $paper->title,
            'is_public'=> $paper->is_public,
            'time_limit_enable'=> $paper->time_limit_enable,
            'time_limit_value'=> $paper->time_limit_value,
            'pass_score'=> $paper->pass_score,
            'total_score'=> $paper->total_score,
            'expired_at'=> $paper->expired_at,
        ];

        $data['paper_questions']= [];
        foreach ($paper->questions as $question){
            array_push($data['paper_questions'], ['question_id'=>$question->question_id, 'score'=>$question->score]);
        }
        return $data;
    }


    /**
     * admin.examination
     *
     * 创建新表单时数据
     * @param $customer_school_id 学校客户id
     *
     * @return array
     * @throws \Exception
     */
    protected function create_data($customer_school_id): array
    {
        $data['form']= [
            'category_id'=> 0,
            'customer_school_id'=> $customer_school_id,
            'alias'=> strtoupper(Str::random(12)),
            'title'=> '',
            'is_public'=> 0,
            'time_limit_enable'=> 0,
            'time_limit_value'=> 90,
            'pass_score'=> 60,
            'total_score'=> 100,
            'expired_at'=> (new Carbon())->addWeek(),
        ];
        $data['questions']= [];
        $data['categories']= $this->categories($customer_school_id)->toArray();

        return $data;
    }


    /**
     * admin.examination
     *
     * 为用户创建一个试卷，如果用户已经存在试卷，且没有超过提交时间，则重新给用户，否则给用户提示已经考试过了
     *
     * @param $params
     *
     * @return array
     */
    public function make_paper(array $params)
    {
        $paper_exam = PaperExams::where([
            'user_id'=>$params['user_id'],
            'paper_id'=>$params['paper_id']
        ])->first();

        if($paper_exam){
            return ['state'=>'paper_exam_exist', 'paper_exam'=>$paper_exam];
        }
        $options= array_merge($params, [
            'start_time'=> Carbon::now(),
            'status'=> 1
        ]);
        $paper_exam= PaperExams::create($options);
        return ['state'=>'created_successful', 'paper_exam'=> $paper_exam];

    }

    /**
     *
     * admin.examination
     * 题库数据入库
     *
     * @param Request $request
     * @return bool
     * @throws \Touge\AdminExamination\Exceptions\ResponseFailedException
     */
    public function store(Request $request)
    {
        $paper_options= $this->get_paper_options($request);

        $paper_options['customer_school_id']= Admin::user()->customer_school_id;
        $paper_question_options= $request->get('paper_questions', []);

        /**
         * 试卷试题输入检测
         */
        $check_paper_questions= $this->check_paper_questions($paper_question_options, $paper_options['total_score']);
        if ($check_paper_questions){
            $this->throw_error($check_paper_questions);
        }

        DB::beginTransaction();
        try{
            /**
             * 试卷主题
             */
            $options['question_number']= count($paper_question_options);
            $paper= Paper::create($paper_options);

            foreach($paper_question_options as $index=>$question){
                $row= [
                    'paper_id'=>$paper->id,
                    'question_id'=>$question['question_id'],
                    'score'=> $question['score']
                ];
                $res= PaperQuestion::create($row);
                if(!$res){
                    return false;
                }
            }
            DB::commit();
            return $paper;
        }catch (\Exception $exception){
            DB::rollBack();
            return false;
        }
    }



    /**
     * admin.examination
     *
     * 更新试卷
     *
     * @param Request $request
     * @param $id
     * @return mixed
     * @throws \Touge\AdminExamination\Exceptions\ResponseFailedException
     */
    public function update(Request $request, $id)
    {
        $paper_options= $this->get_paper_options($request);
        $paper_question_options= $request->get('paper_questions', []);

        $check_paper_questions= $this->check_paper_questions($paper_question_options, $paper_options['total_score']);
        if ($check_paper_questions){
            $this->throw_error($check_paper_questions);
        }

        /**
         * 数据更新
         */
        $paper= Paper::findOrFail($id);

        DB::beginTransaction();
        try{
            $paper['question_number']= count($paper_question_options);
            $paper->update($paper_options);

            /**
             * 删除解析，添加解析
             */
            $paper->questions()->delete();

            foreach($paper_question_options as $index=>$question){
                $row= [
                    'paper_id'=>$paper->id,
                    'question_id'=>$question['question_id'],
                    'score'=> $question['score']
                ];
                PaperQuestion::create($row);
            }
            DB::commit();
            return $paper;
        }catch (\Exception $exception){
            DB::rollBack();
            return false;
        }

    }

    /**
     * 检测录入的试题
     *
     * @param array $questions
     * @param $paper_score_total
     * @return bool|string
     */
    protected function check_paper_questions(array $questions, $paper_score_total){
        $error_message= '';
        $error= false;

        if(!$questions){
            $error_message= __('admin-examination::paper.question-null');
            return $error_message;
        }


        $paper_question_map = [];
        $question_score_total= 0;

        foreach($questions as $index=>$question) {
            /**
             * 判定试题是否重复
             */
            if (isset($paper_question_map[$question['question_id']])) {
                $error_message= '第 ' . ($index + 1) . ' 题 与第 ' . $paper_question_map[$question['question_id']] . ' 题重复';
                $error= true;
                break;
            }


            /**
             * 判定试题是否存在
             */
            if (empty($question['question_id'])) {
                $error_message= '第'. ($index+1) . '题不存在';
                $error= true;
                break;
            }

            /**
             * 判定试题分值是否存在
             * 是否为数值
             * 是否<=0
             */
            if (empty($question['score']) || ($question_score = intval($question['score'])) <=0 ) {
                $error= true;
                $error_message= '第'. ($index+1) . '题分值设置错误';
                break;
            }
            $question_score_total+= $question_score;
            $paper_question_map[$question['question_id']]= $index+1;
        }

        if($error){
            return $error_message;
        }

        /**
         * 判定试题分值相加是否为试卷总分相等
         */
        if ($paper_score_total != $question_score_total)
        {
            $error_message= '总分设置' . $paper_score_total . '和计算' . $question_score_total . '不相等';
            return $error_message;
        }
        return $error;
    }

    /**
     * admin.examination
     * 题库表单内容收集
     *
     * @param $id
     * @param $gradation
     *
     * @return array
     */
    public function get_form_data(array $params): array
    {
        $data= [];

        if (!$params['id']){
            $data= $this->create_data($params['customer_school_id']);
        }else{
            $data= $this->edit_data($params);
        }
        $data['id']= $params['id'];

        return $data;
    }


    /**
     * admin.examination
     * 试卷分类
     *
     * @param $customer_school_id 学院客户id
     * @return Collection
     */
    public function categories($customer_school_id): Collection
    {
        $query= PaperCategory::select('id','name');
        $query->where(['customer_school_id'=> $customer_school_id]);
        return $query->get();
    }
}
