<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2019-12-10
 * Time: 19:18
 */

namespace Touge\AdminExamination\Services\Api;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
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
     * 当前所属用户的已答题的试卷列表
     *
     * @param array $params
     * @return Collection
     */
    public function paper_exam_list(array $params)
    {

        $paginate= $params['paginate'];

        $selectFields= [
            'pe.id' ,'pe.paper_id' ,'pe.user_id' ,'pe.user_name' ,'pe.marker_id' ,'pe.marker_name',
            'pe.is_judge' ,'pe.updated_at as market_time' ,'pe.score',

            'p.title as paper_title' , 'p.category_id', 'p.alias as paper_alias', 'p.question_number', 'p.total_score' ,'p.time_limit_value'
        ];

        $query= DB::table('touge_paper_exams as pe');
        $query->leftJoin('touge_papers as p', 'p.id' ,'=', 'pe.paper_id');
        $query->select($selectFields);

        $query->where(['pe.customer_school_id'=> $params['customer_school_id']]);


        $query->orderBy('pe.id' ,'DESC');

        $exam_list= $query->paginate($paginate['limit'], null, null, $paginate['current']);

        $data= [
            'exam_list'=> $exam_list->items(),
            'paginate'=> [
                'current'=> $exam_list->currentPage(),
                'page_total'=> ceil($exam_list->total() / $paginate['limit'])
            ],
        ];

        return $data;
    }
}