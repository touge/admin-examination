<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2019-12-05
 * Time: 11:13
 */

namespace Touge\AdminExamination\Types;


class PaperExamStatus implements BaseType
{
    const DOING = 1;
    const SUBMITTED = 2;

    public static function getList()
    {
        return [
            self::DOING => '答题中',
            self::SUBMITTED => '答题完成',
        ];
    }
}