<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2019-12-10
 * Time: 19:18
 */

namespace Touge\AdminExamination\Services\Api;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Touge\AdminExamination\Facades\CustomerMember;
use Touge\AdminExamination\Facades\GroupPaper;
use Touge\AdminExamination\Services\BaseService;

/**
 * 学生试卷操作
 *
 * Class PaperExamService
 * @package App\Modules\Exams\Services
 */
class CorrectionService extends BaseService
{
    /**
     *
     * @param array $params
     * @return array
     */
    public function paper_exam_list(array $params)
    {
        $user= $params['user'];

        /**
         * 一个用户拥有的组
         */
        $user_groups= CustomerMember::member_groups($user->id);

        /**
         * 一个组拥有的所有的人
         */
        $group_users= GroupPaper::group_members(2);

        /**
         * 几个组拥有的人
         */
        $group_users= GroupPaper::groups_members([1,2,3]);

        /**
         * 指定组别的试卷列表
         */
        $group_papers= GroupPaper::group_papers(3);
        return $group_papers;


        /**
         * X个组别的试卷列表
         */
        $groups_papers= GroupPaper::groups_papers([1,2,3]);
//        return $groups_papers;





        $filter= $params['filter'];
        $paginate= $params['paginate'];


        $selectFields= [
            'pe.id' ,'pe.paper_id' ,'pe.user_id' ,'pe.user_name' ,'pe.marker_id' ,'pe.marker_name',
            'pe.is_judge' ,'pe.updated_at as market_time' ,'pe.score',

            'p.title as paper_title' , 'p.category_id' ,'pc.name as category_name',
        ];

        if($filter=='all'){
            $queryBuild= $this->filterPaperAllQueryBuild($user);
            $queryBuild->leftJoin('papers as p', 'p.id', '=', 'pe.paper_id');
            $queryBuild->leftJoin('paper_categories as pc', 'pc.id', '=', 'p.category_id');
        } else {
            $queryBuild= $this->filterPaperQueryBuild($filter);
            $queryBuild->leftJoin('papers as p', 'p.id', '=', 'pe.paper_id');
            $queryBuild->leftJoin('paper_categories as pc', 'pc.id', '=', 'p.category_id');
        }
        $queryBuild->select($selectFields);
        $queryBuild->orderBy('pe.id','desc');
        $exam_list= $queryBuild->paginate($paginate['limit'],['pe.id'], null, $paginate['current']);

        $data= [
            'paper_exams'=> $exam_list->items(),
            'paginate'=> [
                'current'=> $exam_list->currentPage(),
                'total'=> $exam_list->total(),
                'limit'=> $paginate['limit']
            ],
        ];

        return $data;
    }

    protected function filterPaperQueryBuild($filter)
    {
        $query= DB::table('paper_exams as pe')
            ->whereIn('user_id', function(Builder $query) use($filter){
                $query->from('school_customer_paper_group_members')
                    ->select(['member_id'])
                    ->where(['group_id'=> $filter]);
            });

        return $query;
    }

    /**
     * 当前用户所属组别的所有用户试卷列表
     *
     * @param $user
     * @return Builder
     */
    protected function filterPaperAllQueryBuild($user): Builder
    {
        $query= DB::table('paper_exams as pe')->whereIn( 'user_id',function(Builder $query)use ($user){
            $query->from('school_customer_paper_group_members')
                ->select(['member_id'])
                ->distinct('member_id')
                ->whereIn('group_id' ,function(Builder $query) use($user){
                    $query->select(['group_id'])->from('school_customer_paper_group_members')
                        ->where(['member_id'=> $user->id]);
                });
        });
        return $query;
    }

    /**
     * 获得一张考卷信息
     *
     * @param $exam_id
     * @param array $params
     * @return array
     */
    public function fetch_one($exam_id, array $params)
    {
        $user= $params;


        /**
         * 用户试卷
         */
        $paper_exam= ExamPaperExams::select(['id', 'user_id', 'paper_id', ])->findOrFail($exam_id);

        /**
         * 试卷模板
         */
        $paper= ExamPaper::select(['id', 'alias', 'category_id', 'title', 'is_public', 'time_limit_enable', 'time_limit_value', 'pass_score', 'total_score'])->findOrFail($paper_exam->paper_id);

        /**
         * 试卷模板各题目分值
         */
        $paper_question_score= ExamPaperQuestion::where(['paper_id'=>$paper_exam->paper_id])->get(['paper_id', 'question_id', 'score'])->pluck('score', 'question_id');

        /**
         * 用户试卷试题
         */
        $paper_exam_questions= ExamPaperExamQuestions::where(['exam_id'=> $paper_exam->id])->get(['id', 'question_id', 'answer', 'is_judge', 'score']);
        $paper_exam_questions= $paper_exam_questions->map(function($item, $index) use($paper_question_score) {

            /**
             * 当前题库中的试题信息
             */
            $question= ExamQuestion::select('question as title', 'type')->findOrFail($item->question_id);

            /**
             * 试卷中的问题ID exam_paper_question.id
             */
            $question->id= $item->question_id;

            /**
             * 当前试卷模板中的题目分值
             */
            $question->score= $paper_question_score[$item->question_id];

            /**
             * 考试试卷中的填写的问题
             */
            $exam= [
                'score'=> $item->score,
                'is_judge'=> $item->is_judge,
                'question_id'=> $item->id,
                'answer'=> json_decode($item->answer, TRUE)
            ];
            $question->paper_exam= collect($exam);


            /**
             * 试卷中问题中的选项
             */
            switch ($question->type){
                case QuestionType::SINGLE_CHOICE:
                case QuestionType::MULTI_CHOICES:
                case QuestionType::TRUE_FALSE:
                    $question->options= ExamQuestionOption::where(['question_id'=> $item->question_id])->get(['id','is_answer','option']);
                    $question_exam_answer= $question->options->filter(function($item){
                        if($item->is_answer==1){
                            return $item->is_answer;
                        }
                    })->toArray();
                    $question->answer= array_keys($question_exam_answer);

                    break;
                case QuestionType::FILL:
                case QuestionType::TEXT:
                    $question->answers= ExamQuestionAnswer::where(['question_id'=> $item->question_id])->get(['id', 'answer']);
                    $question->answer= $question->answers->pluck('answer');
                    break;
            }

            $analyses= ExamQuestionAnalysis::where(['question_id'=> $item->question_id])->first(['id', 'analysis']);

            $question->analyses= $analyses;
            return $question;
        });

        $data= [
            'paper_id'=> $paper->id,
            'paper_alias'=> $paper->alias,
            'paper_category'=> $paper->category_id,
            'paper_title'=> $paper->title,
            'paper_is_public'=> $paper->is_public,
            'paper_time_limit_enable'=> $paper->time_limit_enable,
            'paper_time_limit_value'=> $paper->time_limit_value,
            'paper_pass_score'=> $paper->pass_score,
            'paper_total_score'=> $paper->total_score,
            'exam_id'=> $paper_exam->id,
            'user_id'=> $paper_exam->user_id,
            'questions'=> $paper_exam_questions
        ];

        return $data;
    }

    /**
     * @param $id
     * @param array $params
     * @return array
     * @throws \App\Modules\Common\ResponseFailedException
     */
    public function update($id, array $params)
    {
        $paper_exam= ExamPaperExams::findOrFail($id);

        $marker= $params['user'];

        $questions= $params['questions'];
        $collect_questions= collect($questions);

        $collect_questions= $collect_questions->map(function($item, $index) use($id){
            return [
                'exam_id'=> $id,
                'question_id'=> $item['id'],
                'score'=> $item['score'],
                'is_judge'=> 1,

            ];
        });


        DB::beginTransaction();
        $paper_exam_question_updated= $this->judge_update_batch($collect_questions);
        $paper_exam_updated= $paper_exam->update([
            'is_judge'=> 1,
            'score'=> $params['got_score'],
            'marker_id'=> $marker['id'],
            'marker_name'=> $marker['name'],
        ]);

        if($paper_exam_question_updated && $paper_exam_updated){
            DB::commit();
        }else{
            DB::rollBack();
            $this->throw_error('数据提交失败，请联系系统管理员！');
        }

        return $params;
    }

    /**
     *
     * 批量批改试卷
     * 更新试卷试题数据操作
     * @param $params
     * @return bool
     */
    protected function judge_update_batch(Collection $params): bool
    {
        foreach($params as $param) {
            $update= ExamPaperExamQuestions::where([
                'exam_id'=> $param['exam_id'],
                'question_id'=> $param['question_id']
            ])
                ->first()
                ->update([
                    'is_judge'=>1,
                    'score'=>$param['score']
                ]);
            if(!$update){
                return false;
            }
        }
        return true;
    }
}