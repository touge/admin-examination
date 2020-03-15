<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2019-12-10
 * Time: 19:18
 */

namespace Touge\AdminExamination\Services\Api;

use Touge\AdminExamination\Models\PaperExams;
use Touge\AdminExamination\Services\BaseService;

/**
 * 学生试卷操作
 *
 * Class PaperExamService
 * @package App\Modules\Exams\Services
 */
class CorrectionService extends BaseService
{
    /**
     *
     * 当前所属用户的已答题的试卷列表
     * @param array $params
     * @return array
     */
    public function paper_exam_list(array $params): array
    {
        $paginate= $params['paginate'];

        $paper_exams = new PaperExams();
        $exam_list= $paper_exams->correction_paper_exams($params);

        $data= [
            'exam_list'=> $exam_list->items(),
            'paginate'=> [
                'current'=> $exam_list->currentPage(),
                'page_total'=> ceil($exam_list->total() / $paginate['limit'])
            ],
        ];

        return $data;
    }
}