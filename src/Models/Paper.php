<?php

namespace Touge\AdminExamination\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Paper extends BaseModel
{
    protected $table= 'touge_papers';
    //
    protected $guarded= ['id'];

    /**
     * 试卷试题
     * @return HasMany
     */
    public function questions() : HasMany
    {
        return $this->hasMany(PaperQuestion::class, 'paper_id', 'id');
    }

    /**
     * 试卷分类
     * @return HasOne
     */
    public function category() : HasOne
    {
        return $this->hasOne(PaperCategory::class, 'id', 'category_id');
    }
}
