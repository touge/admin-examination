<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2019-11-22
 * Time: 17:36
 */

namespace Touge\AdminExamination\Facades;


use Illuminate\Support\Facades\Facade;

/**
 * Class QuestionFacade.
 *
 * @method static \Touge\AdminExamination\Services\PaperService gradations()
 * @method static \Touge\AdminExamination\Services\PaperService make_paper(array $params)
 * @method static \Touge\AdminExamination\Services\PaperService store(Request $request)
 * @method static \Touge\AdminExamination\Services\PaperService update(Request $request, $id)
 * @method static \Touge\AdminExamination\Services\PaperService get_form_data(array $params)
 * @method static \Touge\AdminExamination\Services\PaperService categories($gradation_id)
 */
class Paper extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'paper';
    }
}