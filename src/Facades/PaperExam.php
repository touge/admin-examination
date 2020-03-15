<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2019-12-10
 * Time: 19:18
 */

namespace Touge\AdminExamination\Facades;


use Illuminate\Support\Facades\Facade;

/**
 * Class PaperExam
 * @package App\Modules\Exams\Facades
 *
 * @method static \Touge\AdminExamination\Services\PaperExamService fetch_one($exam_id)
 * @method static \Touge\AdminExamination\Services\PaperExamService update($id, array $params)
 *
 */
class PaperExam extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'admin_paper_exam';
    }
}