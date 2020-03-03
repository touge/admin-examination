<?php

namespace Touge\AdminExamination\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * 考试考卷
 *
 * Class ExamPaperExams
 * @package App\Modules\Exams\Models
 */
class PaperExams extends BaseModel
{
    protected $table= 'touge_paper_exams';

    protected $guarded= ['id'];

    /**
     * 考试试卷列表的试卷关联数据
     *
     * @return HasOne
     */
    public function paper(): HasOne
    {
        return $this->hasOne(Paper::class, 'id', 'paper_id');
    }

    /**
     * 单张考卷信息下面的答题数据
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questions(): HasMany
    {
        return $this->hasMany(PaperExamQuestions::class, 'exam_id', 'id');
    }
}
