<?php

namespace Touge\AdminExamination\Models;


use Illuminate\Database\Eloquent\Relations\HasOne;

class PaperExamQuestions extends BaseModel
{
    //
    protected $guarded= ['id'];

    /**
     * 关联试题详情
     *
     * @return HasOne
     */
    public function question(): HasOne
    {
        return $this->hasOne(Question::class, 'id', 'question_id');
    }
}
