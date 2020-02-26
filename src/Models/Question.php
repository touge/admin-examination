<?php

namespace Touge\AdminExamination\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Question extends BaseModel
{
    protected $table= 'touge_questions';

    //
    protected $guarded= ['id'];

    /**
     * is_answer,option
     * @return HasMany
     */
    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class, 'question_id', 'id');
    }

    /**
     * answer
     * @return HasMany
     */
    public function answers(): HasMany
    {
        return $this->hasMany(QuestionAnswer::class, 'question_id', 'id');
    }

    /**
     * 试题解析
     * @return HasOne
     */
    public function analyses(): HasOne
    {
        return $this->hasOne(QuestionAnalysis::class, 'question_id', 'id');
    }
}
