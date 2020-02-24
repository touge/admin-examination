<?php

namespace Touge\AdminExamination\Types;


class QuestionType implements BaseType
{
    const SINGLE_CHOICE = 1;
    const MULTI_CHOICES = 2;
    const TRUE_FALSE = 3;
    const FILL = 4;
    const TEXT = 5;
    const GROUP = 6;

    const TYPE_ENUM= [
        self::SINGLE_CHOICE => '单选题',
        self::MULTI_CHOICES => '多选题目',
        self::TRUE_FALSE => '判断题',
        self::FILL => '填空题',
        self::TEXT => '问答题',
//            self::GROUP => '多题目',
    ];

    /**
     * @return array
     */
    public static function getList(): array
    {
        return static::TYPE_ENUM;
    }

    /**
     * @param $type
     * @return string
     */
    public static function text($type): string
    {
        return static::TYPE_ENUM[$type];
    }

    /**
     * @param $text
     * @return int|string
     */
    public static function type($text)
    {
        $type_enum = static::TYPE_ENUM;
        foreach($type_enum as $key=>$item)
        {
            if($text==$item)
            {
                return $key;
            }
        }
        return 0;
    }
}