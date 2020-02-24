<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2020-01-06
 * Time: 15:58
 */

namespace Touge\AdminExamination\Facades;


use Illuminate\Support\Facades\Facade;


/**
 * Class GroupPaper
 * @package Touge\AdminExamination\Facades
 *
 * @method static \Touge\AdminExamination\Services\GroupPaperService  group_papers($group_id)
 * @method static \Touge\AdminExamination\Services\GroupPaperService  groups_papers(array $group_ids)
 * @method static \Touge\AdminExamination\Services\GroupPaperService  group_members($group_id)
 * @method static \Touge\AdminExamination\Services\GroupPaperService  groups_members(array $group_ids)
 */
class GroupPaper extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'group_paper';
    }
}