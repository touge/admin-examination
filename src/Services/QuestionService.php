<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2019-11-20
 * Time: 09:06
 */

namespace Touge\AdminExamination\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Touge\AdminExamination\Models\QuestionAnswer;
use Touge\AdminExamination\Models\QuestionOption;
use Touge\AdminExamination\Models\QuestionTagGroup;
use Touge\AdminExamination\Supports\TagHelper;
use Touge\AdminExamination\Types\QuestionType;

use Touge\AdminExamination\Models\Question as QuestionModal;
use Touge\AdminExamination\Models\QuestionAnalysis;

class QuestionService
{

    /**
     *
     * admin.examination
     *
     * 根据条件进行试题搜索，并提供分页配置
     * @param array $params
     * @return array
     */
    public function search(array $params): array
    {
        $query= QuestionModal::select('id', 'question', 'type');

        if(array_key_exists('type', $params) && $params['type']!=0 ){
            $query->where(['type'=> $params['type']]);
        }
        if (array_key_exists('question', $params) && $params['question']!='' )
        {
            $query->where('question', 'like', '%' . $params['question'] .'%');
        }

        $paginate= $params['paginate'];
        $questions= $query->paginate($paginate['limit'], null, null, $paginate['current']);
        return $questions->toArray();
    }

    /**
     *
     * 用于试题预览的试题列表
     *
     * @param $question_ids
     * @return array
     */
    public function paper_view_questions($question_ids): array
    {
        $default_score= 5;

        $questions= QuestionModal::select('id','type','question')->whereIn('id', $question_ids)->get();
        $list= [];
        foreach($questions as $question)
        {
            $row= [
                'id'=> $question->id,
                'question'=>$question->question,
                'type'=>$question->type,
                'type_text'=> QuestionType::text($question->type),
                'score'=> $default_score,
            ];
            $row['items'] = $this->paper_view_question_item($question);
            $row['analyses']= $this->paper_view_question_analysis($question);
            array_push($list, $row);
        }

        return $list;
    }

    /**
     * 试题分析
     *
     * @param $question
     * @return array
     */
    protected function paper_view_question_analysis($question):array
    {
        return ['id'=> $question->analyses->id, 'analysis'=> $question->analyses->analysis];
    }

    /**
     * 试题项目
     *
     * @param $question
     * @return array
     */
    protected function paper_view_question_item($question): array
    {
        $items= [];
        $answers= [];

        switch ($question->type){
            case QuestionType::SINGLE_CHOICE:
            case QuestionType::MULTI_CHOICES:
            case QuestionType::TRUE_FALSE:
                foreach($question->options as $key=>$option)
                {
                    $key_ord = chr(ord('A') + $key);
                    $row= [
                        'id'=>$option->id,
                        'key_ord'=> $key_ord,
                        'is_answer'=> $option->is_answer,
                        'option'=> $option->option,
                    ];
                    array_push($items, $row);

                    if($option->is_answer==1)
                    {
                        $answers[]= $key_ord;
                    }
                }
                $answer_string= implode(",", $answers);
                return ['options'=>$items, 'answers'=> $answer_string];
            break;
            case QuestionType::FILL:
            case QuestionType::TEXT:
                foreach($question->answers as $answer)
                {
                    $row= [
                        'id'=>$answer->id,
                        'answer'=> $answer->answer,
                    ];
                    array_push($items, $row);
                }
                return ['answers'=> $items];
                break;
        }
        return [];
    }

    /**
     * 题库表单内容收集
     *
     * @param $id
     * @return array
     */
    public function get_form_data($id=0): array
    {
        if ($id==0) return $this->create_data();
        return $this->edit_data($id);
    }


    /**
     * 题库标签组处理
     * @return array
     */
    public function question_group_tags()
    {
        $groups = QuestionTagGroup::all();
        $rows= [];
        foreach($groups as $key=>$group)
        {

            if( $group->tags->count() > 0 )
            {
                $row= ['id'=> $group->id, 'title'=>$group->title];
                $rows[$key]= $row;

                $tags= [];
                foreach($group->tags as $sKey=>$tag)
                {
                    $tags[$sKey]= [
                        'id'=> $tag->id,
                        'title'=> $tag->title,
                    ];
                }
                $rows[$key]['tags']= $tags;

            }
        }
        return $rows;
    }

    /**
     * 题库数据入库
     * @param array $params
     *
     * @return maxid
     */
    public function store(array $params)
    {
        DB::beginTransaction();
        $options= $this->get_question_options($params);
        $question= QuestionModal::create($options);

        /**
         * 题库解析
         */
        $options= $params['analysis'];
        $question_analysis= $this->save_question_analysis($options, $question->id);


        $question_relation= false;
        switch ($params['type']){
            case QuestionType::SINGLE_CHOICE:
            case QuestionType::MULTI_CHOICES:
            case QuestionType::TRUE_FALSE:
                $options= $this->remove_free($params['options'], 'option');
                $question_relation= $this->save_question_option($options, $question->id);
                break;
            case QuestionType::FILL:
            case QuestionType::TEXT:
                $options= $this->remove_free($params['options'], 'answer');
                $question_relation= $this->save_question_answer($options, $question->id);
                break;
        }

//        if($question->id && $question_analysis &&$question_courses && $question_relation){
        if($question->id && $question_analysis && $question_relation){
            DB::commit();
            return $question;
        }
        DB::rollBack();

        return false;
    }

    /**
     * 试题内容更新
     * @param array $params
     * @param int $id
     * @return mixed
     */
    public function update(array $params, int $id){
        $question= QuestionModal::findOrFail($id);

        DB::beginTransaction();
        $options= $this->get_question_options($params);

        /**
         * 更新试题
         */
        $updated= $question->update($options);

        /**
         * 删除解析，添加解析
         */
        $question->analyses()->delete();
        $options= $params['analysis'];
        $question_analysis= $this->save_question_analysis($options, $question->id);

        $question_courses= true;


        /**
         * 删除question_option,question_answer,question_course
         * 宁可错杀，不可漏网
         */
        $question->options()->delete();
        $question->answers()->delete();


        $question_relation= false;
        switch ($params['type']){
            case QuestionType::SINGLE_CHOICE:
            case QuestionType::MULTI_CHOICES:
            case QuestionType::TRUE_FALSE:
                $options= $this->remove_free($params['options'], 'option');
                $question_relation= $this->save_question_option($options, $question->id);
                break;
            case QuestionType::FILL:
            case QuestionType::TEXT:
                $options= $this->remove_free($params['options'], 'answer');
                $question_relation= $this->save_question_answer($options, $question->id);
                break;
        }

        if($updated && $question_analysis && $question_courses && $question_relation){
            DB::commit();
            return $question;
        }
        DB::rollBack();
    }

    /**
     * 获得试题的关键信息
     *
     * @param array $params
     * @return array
     */
    protected function get_question_options(array $params)
    {
        $options= [
            'question'=> $params['question'],
            'alias'=> $params['alias'],
            'type'=> $params['type'],
            'tags'=> $params['tags'],
            'customer_school_id'=> $params['customer_school_id'],
        ];
        return $options;
    }


    /**
     * 存入试题解析关联
     * @param $analysis
     * @param $qid
     * @return bool
     */
    protected function save_question_analysis($analysis, $qid){
        return QuestionAnalysis::create(['question_id'=> $qid, 'analysis'=> $analysis]) ? true : false;
    }


    /**
     * 存入 question_answer 关联数据
     * @param $options
     * @param $qid
     * @return bool
     */
    protected function save_question_answer($options, $qid)
    {
        foreach($options as $option){
            $row= ['question_id'=>$qid, 'answer'=>$option['answer']];
            $res= QuestionAnswer::create($row);
            if(!$res){
                return false;
            }
        }
        return true;
    }

    /**
     * 存入question_option 关联数据
     * @param $options
     * @param $qid
     * @return bool
     */
    protected function save_question_option($options, $qid){
        foreach($options as $option)
        {
            $row= [
                'question_id'=> $qid,
                'is_answer'=> $option['is_answer']=='true'? 1 : 0,
                'option'=>$option['option']
            ];
            $res= QuestionOption::create($row);
            if(!$res){
                return false;
            }
        }
        return true;
    }

    /**
     * 去掉无内容选项
     * @param array $options
     * @param string $type
     * @return array
     */
    protected function remove_free(array $options, $type='option')
    {
        if($type=='option')
        {
            foreach($options as $key=>$option){
                if(!$option['option'])
                {
                    unset($options[$key]);
                }
            }
            return $options;
        }

        foreach($options as $key=>$option)
        {
            if($option['answer']=='')
            {
                unset($options[$key]);
            }
        }

        return $options;
    }

    /**
     * 获得question_answer数据
     * @param $question
     * @return array
     */
    protected function get_question_answer_options($question)
    {
        $question_answers= $question->answers;
        $rows= [];
        foreach($question_answers as $answer)
        {
            $row= ['answer'=> $answer->answer];
            array_push($rows, $row);
        }
        return $rows;
    }

    /**
     * 获得question_option数据
     * @param $question
     * @return array
     */
    protected function get_question_choice_options($question)
    {
        $question_options= $question->options;
        $rows= [];
        foreach($question_options as $option)
        {
            $row= ['is_answer'=> $option->is_answer? true: false, 'option'=> $option->option];
            array_push($rows, $row);
        }
        return $rows;
    }

    /**
     * 创建新表单时数据
     *
     * @return array
     */
    protected function create_data(): array
    {
        /**
         * 单复选
         */
        $choice_option= [
            ['is_answer' => false, 'option' => ''],
            ['is_answer' => false, 'option' => ''],
            ['is_answer' => false, 'option' => ''],
            ['is_answer' => false, 'option' => ''],
        ];

        /**
         * 是非
         */
        $ture_false_option= [
            ['is_answer' => false, 'option' => '正确'],
            ['is_answer' => false, 'option' => '错误'],
        ];

        /**
         * 填空、问答
         */
        $answer_option= [
            ['answer'=> '',],
        ];

        $data = [
            'types'=> QuestionType::getList(),
            'single_choice_option' => $choice_option,
            'multi_choices_option'=> $choice_option,
            'true_false_option' => $ture_false_option,
            'fill_answer'=> $answer_option,
            'text_answer' => $answer_option,
        ];
        $data["QTCLASS"]= QuestionType::class;
        $data['form']= [
            'alias'=> strtoupper(Str::random(12)),
            'question'=> '',
            'type'=>  QuestionType::SINGLE_CHOICE,
            'tags'=> [],
            'analysis'=> '',
            'source'=> '',
            'courses'=>[],
        ];

        return $data;
    }

    /**
     * 编辑表单时数据
     * @param $id
     *
     * @return array
     */
    protected function edit_data($id): array
    {
        $question= QuestionModal::findOrFail($id);

        $data= $this->create_data();

        switch ($question->type){
            case QuestionType::SINGLE_CHOICE:
                $data['single_choice_option']= $this->get_question_choice_options($question);
                break;
            case QuestionType::MULTI_CHOICES:
                $data['multi_choices_option']= $this->get_question_choice_options($question);
                break;
            case QuestionType::TRUE_FALSE:
                $data['true_false_option']= $this->get_question_choice_options($question);
                break;
            case QuestionType::FILL:
                $data['fill_answer']= $this->get_question_answer_options($question);
                break;
            case QuestionType::TEXT:
                $data['text_answer']= $this->get_question_answer_options($question);
                break;
        }

        $analysis= $question->analyses!=null ? $question->analyses->analysis : '';
        $data['form']= [
            'alias'=> $question->alias,
            'question'=> $question->question,
            'type'=>  $question->type,
            'tags'=> TagHelper::string2Array($question->tags),
            'analysis'=> $analysis,
            'source'=> $question->source,
        ];

        return $data;
    }

}