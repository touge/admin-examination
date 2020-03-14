<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2019-11-22
 * Time: 17:37
 */


namespace Touge\AdminExamination\Services\Api;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Touge\AdminExamination\Models\PaperExams;
use Touge\AdminExamination\Services\BaseService;

/**
 * 我的考试
 * Class MyService
 * @package Touge\AdminExamination\Services
 */
class MyService extends BaseService
{
    /**
     * @param array $params
     * @return Collection
     */
    public function fetch_list(array $params): Collection
    {
        return (new PaperExams)->my_paper_exams($params);
    }
}