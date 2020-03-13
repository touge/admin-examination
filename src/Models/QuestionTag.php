<?php

namespace Touge\AdminExamination\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;

class QuestionTag extends BaseModel
{
    protected $table= 'touge_question_tags';
    public function group(): HasOne
    {
        return $this->hasOne(QuestionTagGroup::class, 'id', 'group_id');
    }
}
