<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2019-12-05
 * Time: 17:00
 */

namespace Touge\AdminExamination\Facades\Api;

use Illuminate\Support\Facades\Facade;

/**
 * 我的考试
 * Class QuestionFacade.
 *
 * @method static \Touge\AdminExamination\Services\Api\MyService store(int $paper_id)
 * @method static \Touge\AdminExamination\Services\Api\MyService save_questions(array $params)
 * @method static \Touge\AdminExamination\Services\Api\MyService  fetch_list(array $params)
 */
class My extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'api_my';
    }

}