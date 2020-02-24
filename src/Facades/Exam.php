<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2019-12-05
 * Time: 17:00
 */

namespace Touge\AdminExamination\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class QuestionFacade.
 *
 * @method static \Touge\AdminExamination\Services\Api\ExamService store(int $paper_id)
 * @method static \Touge\AdminExamination\Services\Api\ExamService save_questions(array $params)
 * @method static \Touge\AdminExamination\Services\Api\ExamService  fetch_list(array $params)
 * @method static \Touge\AdminExamination\Services\Api\ExamService  fetch_one(string $key, $type='alias', $includeAnswer= false)
 */
class Exam extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'exam';
    }

}