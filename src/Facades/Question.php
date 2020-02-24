<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2019-11-20
 * Time: 09:08
 */

namespace Touge\AdminExamination\Facades;


use Illuminate\Support\Facades\Facade;

/**
 * Class QuestionFacade.
 *
 * @method static \Touge\AdminExamination\Services\QuestionService get_form_data($id=0)
 * @method static \Touge\AdminExamination\Services\QuestionService question_group_tags()
 * @method static \Touge\AdminExamination\Services\QuestionService store(array $params)
 * @method static \Touge\AdminExamination\Services\QuestionService update(array $params, int $id)
 */
class Question extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'question';
    }
}