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
use Touge\AdminExamination\Services\BaseService;

/**
 * 我的考试
 * Class MyService
 * @package Touge\AdminExamination\Services
 */
class MyService extends BaseService
{
    /**
     * 获得当前用户所有的试卷列表
     * @return \Illuminate\Database\Query\Builder
     */
    protected function filterMyPaperAllQueryBuild(): Builder
    {
        $query= DB::table('paper_exams as pe')
            ->leftJoin('papers as p', 'p.id' ,'=' ,'pe.paper_id')
            ->leftJoin('paper_categories as pc' ,'pc.id' ,'=' ,'p.category_id');
        return $query;
    }

    /**
     * @param $filter
     * @return Builder
     */
    protected function filterMyPaperQueryBuild(): Builder
    {
        $query= DB::table('school_customer_paper_group_members as pgm')
            ->leftJoin('school_customer_paper_group_papers as pgp' ,'pgp.group_id' ,'=' ,'pgm.group_id')
            ->leftJoin('paper_exams as pe', 'pe.paper_id' ,'=' ,'pgp.paper_id')
            ->leftJoin('papers as p', 'p.id' ,'=' ,'pe.paper_id')
            ->leftJoin('paper_categories as pc' ,'p.category_id' ,'=' ,'pc.id');
        return $query;
    }
    /**
     * @param array $params
     * @return array
     */
    public function fetch_list(array $params)
    {
        $paginate= $params['paginate'];
        $user= $params['user'];
        $filter= $params['filter'];

        $selectFields= [
            'pe.id' ,'pe.paper_id' ,'pe.user_id' ,'pe.user_name' ,'pe.marker_id' ,'pe.marker_name',
            'pe.is_judge' ,'pe.updated_at as market_time' ,'pe.score',

            'p.title as paper_title' , 'p.category_id' ,'pc.name as category_name',
        ];

        if ($filter=='all'){
            $queryBuild= $this->filterMyPaperAllQueryBuild();
        }else{
            $queryBuild= $this->filterMyPaperQueryBuild();
            $queryBuild->distinct('pgp.paper_id');
            $queryBuild->where(['pgp.group_id'=>$filter]);
        }
        $queryBuild->select($selectFields);
        $queryBuild->where(['pe.user_id'=> $user->id]);
        $queryBuild->orderBy('pe.id' ,'desc');

        $paper_exams= $queryBuild->paginate($paginate['limit'],['pe.id'], null, $paginate['current']);

        $data= [
            'paper_exams'=> $paper_exams->items(),
            'paginate'=> [
                'current'=> $paper_exams->currentPage(),
                'total'=> $paper_exams->total()
            ],
        ];

        return $data;
    }
}