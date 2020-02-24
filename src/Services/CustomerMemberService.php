<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2020-01-06
 * Time: 15:02
 */

namespace Touge\AdminExamination\Services;


use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Touge\AdminExamination\Models\Member;

class CustomerMemberService extends BaseService
{
    /**
     * 获得当前用户所在的试卷组列表
     *
     * @param $user_id
     * @return Collection
     */
    public function paper_groups($user_id): Collection
    {
        $customerMemberModel= new Member;
        $customer_member= $customerMemberModel->paper_groups($user_id);
        return $customer_member;
    }


    /**
     * 一个用户拥有的所有组别
     *
     * @return Collection
     */
    public function member_groups($user_id)
    {
        $query = DB::table('school_customer_paper_group_members as pgm');
        $query->leftJoin('school_customer_paper_groups as pg', 'pg.id', '=', 'pgm.group_id');
        $query->where(['pgm.member_id'=> $user_id]);
        $query->select(['pg.id', 'pg.name']);

        $user_groups= $query->get();
        return $user_groups;
    }
}