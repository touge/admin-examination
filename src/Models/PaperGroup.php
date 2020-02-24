<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2020-01-05
 * Time: 11:52
 */

namespace Touge\AdminExamination\Models;


use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PaperGroup extends BaseModel
{
    protected $table= 'school_customer_paper_groups';

    /**
     *
     * 用户组别与试卷关联
     *
     * @return mixed
     */
    public function papers(): BelongsToMany
    {
        return $this->belongsToMany(Paper::class, 'school_customer_paper_group_papers', 'group_id', 'paper_id');
    }

    /**
     * 老师
     *
     * @return BelongsToMany
     */
    public function teachers(): BelongsToMany
    {
        return $this->relation_members();
    }

    /**
     *
     * 学生
     * @return BelongsToMany
     */
    public function students()
    {
        return $this->relation_members();
    }

    /**
     * 用户组别与用户关联
     * @return BelongsToMany
     */
    public function relation_members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'school_customer_paper_group_members', 'group_id', 'member_id');
    }
}