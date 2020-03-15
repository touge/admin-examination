<?php

namespace Touge\AdminExamination\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * 考试考卷
 *
 * Class ExamPaperExams
 * @package App\Modules\Exams\Models
 */
class PaperExams extends BaseModel
{
    protected $table= 'touge_paper_exams';

    protected $paper_table= 'touge_papers';

    protected $guarded= ['id'];

    /**
     * 考试试卷列表的试卷关联数据
     *
     * @return HasOne
     */
    public function paper(): HasOne
    {
        return $this->hasOne(Paper::class, 'id', 'paper_id');
    }

    /**
     * 单张考卷信息下面的答题数据
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questions(): HasMany
    {
        return $this->hasMany(PaperExamQuestions::class, 'exam_id', 'id');
    }

    /**
     * 用户试卷列表
     *
     * @param array $params
     * @return mixed
     */
    public function my_paper_exams(Array $params)
    {
        $selectFields= [
            'pe.id' ,'pe.paper_id' ,'pe.user_id' ,'pe.user_name' ,'pe.marker_id' ,'pe.marker_name',
            'pe.is_judge' ,'pe.updated_at as market_time' ,'pe.score',

            'p.alias as paper_alias', 'p.title as paper_title' , 'p.category_id' ,'p.time_limit_value', 'p.question_number', 'p.total_score'
        ];

        return $this->setTable( $this->table . ' as pe')
            ->select($selectFields)
            ->leftJoin( $this->paper_table . ' as p', 'p.id', '=', 'pe.paper_id')
            ->where(['pe.user_id'=> $params['user_id']])
            ->orderBy('pe.id','desc')
            ->get();
    }


    /**
     * 老师批改的试卷列表
     *
     * @param $params
     * @return mixed
     */
    public function correction_paper_exams($params){
        $paginate= $params['paginate'];

        $selectFields= [
            'pe.id' ,'pe.paper_id' ,'pe.user_id' ,'pe.user_name' ,'pe.marker_id' ,'pe.marker_name',
            'pe.is_judge' ,'pe.updated_at as market_time' ,'pe.score',

            'p.title as paper_title' , 'p.category_id', 'p.alias as paper_alias', 'p.question_number', 'p.total_score' ,'p.time_limit_value'
        ];



        $exam_list= $this->setTable($this->table . ' as pe')
            ->leftJoin($this->paper_table . ' as p', 'p.id', '=', 'pe.paper_id')
            ->select($selectFields)
            ->where(['pe.customer_school_id'=> $params['customer_school_id']])
            ->orderBy('pe.id','desc')

            ->paginate($paginate['limit'], null, null, $paginate['current']);
        return $exam_list;

    }
}
