<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2020-01-03
 * Time: 09:29
 */

namespace Touge\AdminExamination\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Member extends Model
{
    protected $guarded= ['id'];

    /**
     * 试卷组表
     * @var string
     */
    protected $paper_group_table= 'school_customer_paper_groups';

    /**
     * 用户与试卷组关联表
     * @var string
     */
    protected $paper_group_member_table= 'school_customer_paper_group_members';

    public function __construct(array $attributes = [])
    {
        /**
         * 依赖laravel-jwt-auth配置
         */
        $this->setTable(config('laravel-jwt-auth.database.auth_table'));
        parent::__construct($attributes);
    }


    /**
     *
     * 获得指定用户的试题组信息
     *
     * @param $user_id
     * @return \Illuminate\Support\Collection
     */
    public function paper_groups($user_id)
    {
        return DB::table("$this->paper_group_member_table AS gm")
            ->leftJoin("$this->paper_group_table AS g", 'g.id','=','gm.group_id')
            ->where(['gm.member_id'=> $user_id])->get(['g.id','g.name','g.description']);
    }
}