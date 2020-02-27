<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2019-11-22
 * Time: 17:37
 */


namespace Touge\AdminExamination\Services\Api;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Touge\AdminExamination\Models\PaperExams;
use Touge\AdminExamination\Services\BaseService;

/**
 * 我的考试
 * Class MyService
 * @package Touge\AdminExamination\Services
 */
class MyService extends BaseService
{
    /**
     * @param array $params
     * @return array
     */
    public function fetch_list(array $params)
    {
        $selectFields= [
            'pe.id' ,'pe.paper_id' ,'pe.user_id' ,'pe.user_name' ,'pe.marker_id' ,'pe.marker_name',
            'pe.is_judge' ,'pe.updated_at as market_time' ,'pe.score',

            'p.alias as paper_alias', 'p.title as paper_title' , 'p.category_id' ,'p.time_limit_value', 'p.question_number', 'p.total_score'
        ];

        $query= DB::table('touge_paper_exams as pe');
        $query->leftJoin('touge_papers as p','p.id','=','pe.paper_id');
        $query->where(['user_id'=>$params['user_id']]);
        $query->select($selectFields);
        $query->orderBy('pe.id' ,'desc');

        return $query->get();
    }
}