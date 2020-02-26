<?php

namespace Touge\AdminExamination\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class QuestionTag extends Model
{
    protected $table= 'touge_question_tags';
    public function group(): HasOne
    {
        return $this->hasOne(QuestionTagGroup::class, 'id', 'group_id');
    }
}