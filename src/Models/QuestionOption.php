<?php

namespace Touge\AdminExamination\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionOption extends Model
{
    protected $table= 'touge_question_options';
    //
    protected $guarded= ['id'];
}
