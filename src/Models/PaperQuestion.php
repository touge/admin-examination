<?php

namespace Touge\AdminExamination\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;

class PaperQuestion extends BaseModel
{
    //
    protected $guarded= ['id'];

    /**
     * 试题关联
     * @return HasOne
     */
    public function question(): HasOne
    {
        return $this->HasOne(Question::class, 'id', 'question_id');
    }
}
