<?php

namespace Touge\AdminExamination\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionAnswer extends Model
{
    //
    protected $table= 'touge_question_answers';
    protected $guarded= ['id'];
}
