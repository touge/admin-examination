<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2019-12-10
 * Time: 19:18
 */

namespace Touge\AdminExamination\Facades\Api;


use Illuminate\Support\Facades\Facade;

/**
 * Class PaperExam
 * @package App\Modules\Exams\Facades
 *
 * @method static \Touge\AdminExamination\Services\Api\PaperExamService fetch_list(array $params)
 * @method static \Touge\AdminExamination\Services\Api\PaperExamService fetch_one($exam_id)
 * @method static \Touge\AdminExamination\Services\Api\PaperExamService update($id, array $params)
 * @method static \Touge\AdminExamination\Services\Api\PaperExamService destroy($id){
 *
 */
class PaperExam extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'api_paper_exam';
    }
}