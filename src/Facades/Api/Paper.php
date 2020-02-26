<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2020-02-25
 * Time: 10:40
 */

namespace Touge\AdminExamination\Facades\Api;



use Illuminate\Support\Facades\Facade;

/**
 * Class QuestionFacade.
 *
 * @method static \Touge\AdminExamination\Services\Api\PaperService fetch_list($options)
 * @method static \Touge\AdminExamination\Services\Api\PaperService categories($options)
 * @method static \Touge\AdminExamination\Services\Api\PaperService uuid(string $key, $type='alias', $includeAnswer= false)
 */
class Paper extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'api_paper';
    }
}