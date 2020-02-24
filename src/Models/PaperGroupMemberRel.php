<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2020-01-05
 * Time: 12:33
 */

namespace Touge\AdminExamination\Models;


use Illuminate\Database\Eloquent\Model;

class PaperGroupMemberRel extends Model
{
    protected $table= 'school_customer_paper_group_members';

    /**
     * 用户信息
     *
     * @return mixed
     */
    public function member()
    {
        return $this->hasOne(Member::class, 'member_id', 'id');
    }

    /**
     *
     * 用户拥有的组
     * @return mixed
     */
    public function groups()
    {
        return $this->hasMany(PaperGroup::class, 'group_id', 'id');
    }
}