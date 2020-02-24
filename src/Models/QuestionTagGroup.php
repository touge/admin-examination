<?php

namespace Touge\AdminExamination\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuestionTagGroup extends Model
{

    /**
     * @return HasMany
     */
    public function tags(): HasMany
    {
        return $this->hasMany(QuestionTag::class, 'group_id', 'id');
    }
}
