<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2020-01-06
 * Time: 15:57
 */

namespace Touge\AdminExamination\Services;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class GroupPaperService
 * @package Touge\AdminExamination\Services
 */
class GroupPaperService extends BaseService
{
    /**
     * 组与人之间的数据预编译
     *
     * @return \Illuminate\Database\Query\Builder
     */
    private function build_member_query():Builder
    {
        $query= DB::table('school_customer_paper_group_members as pgm');
        $query->leftJoin('school_customer_members as m', 'm.id', '=', 'pgm.member_id');
        $query->select(['m.id','m.name']);
        return $query;
    }

    /**
     * 组与试卷的关联预编译
     *
     * @return Builder
     */
    private function build_paper_query(): Builder
    {
        $selectFields= [
            'p.id', 'p.alias', 'p.is_public', 'p.gradation_id', 'p.time_limit_enable' ,'p.time_limit_value',
            'p.pass_score', 'p.total_score', 'p.question_number',
            'p.category_id', 'pc.name as category_name'
        ];

        $query= DB::table('school_customer_paper_group_papers as pgp');
        $query->leftJoin('papers as p' ,'p.id', '=', 'pgp.paper_id');
        $query->leftJoin('paper_categories as pc', 'pc.id', '=', 'p.category_id');

        $query->select($selectFields);
        return $query;
    }

    /**
     * X个组别的试卷列表
     *
     * @param array $group_ids
     * @return Collection
     */
    public function groups_papers(array $group_ids)
    {
        $query= $this->build_paper_query();
        $query->whereIn('pgp.group_id', $group_ids);
        $query->distinct('pgp.paper_id');
        return $groups_papers= $query->get();
    }

    /**
     * 指定组别的试卷列表
     *
     * @param $group_id
     * @return Collection
     */
    public function group_papers($group_id): Collection
    {
        $query= $this->build_paper_query();
        $query->where(['pgp.group_id'=> $group_id]);
        $group_papers= $query->get();
        return $group_papers;
    }

    /**
     * 一个组拥有的人
     *
     * @param $group_id
     * @return Collection
     */
    public function group_members($group_id): Collection
    {
        $query= $this->build_member_query();
        $query->where(['pgm.group_id'=> $group_id]);
        $group_members = $query->get();

        return $group_members;
    }

    /**
     * 几个组拥有的人
     * @param array $group_ids [group_id1,group_id2...]
     * @return Collection
     */
    public function groups_members(array $group_ids): Collection
    {
        $query= $this->build_member_query();
        $query->whereIn('pgm.group_id', $group_ids);
        $query->distinct('m.id');
        $groups_members = $query->get();

        return $groups_members;
    }
}