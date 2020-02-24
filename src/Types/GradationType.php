<?php

namespace Touge\AdminExamination\Types;


class GradationType implements BaseType
{
    const GRADATION_NAME= '阶段';

    const ALL=0;
    const SECONDARY = 1;
    const COLLEGE = 2;
    const UNDERGRADUATE = 3;

    const TYPE_ENUM= [
//        self::ALL               => ['type'=> 'all', 'text'=> '全部'],
        self::SECONDARY         => ['type'=> 'secondary', 'text'=>'中专'],
        self::COLLEGE           => ['type'=> 'college', 'text'=> '大专'],
        self::UNDERGRADUATE     => ['type'=> 'undergraduate', 'text'=> '本科'],
    ];


    /**
     * @return array
     */
    public static function getList(): array
    {
        return static::TYPE_ENUM;
    }

    /**
     * 获得数据列表 [[id,slug,name]...]
     * @return array
     */
    public static function gradations(): array
    {
        $items= [];
        foreach(static::TYPE_ENUM as $key=>$item){
            $row= ['id'=>$key, 'slug'=> $item['type'], 'name'=> $item['text']];
            array_push($items, $row);
        }
        return $items;
    }

    /**
     * 获得一个阶段数据信息
     *
     * @param $name
     * @param string $type
     * @return array
     */
    public static function gradation($name, $type='id'): array
    {
        foreach(static::TYPE_ENUM as $key=>$item){
            if($type == 'id' && $name == $key){
                return ['id'=>$key, 'slug'=> $item['type'], 'name'=> $item['text']];
            }
            if($type == 'slug' && $name == $item['type']){
                return ['id'=>$key, 'slug'=> $item['type'], 'name'=> $item['text']];
            }
            if($type == 'name' && $name == $item['text']){
                return ['id'=>$key, 'slug'=> $item['type'], 'name'=> $item['text']];
            }
        }
    }

    /**
     * 获得阶段类型文字
     * @param $key
     * @return string
     */
    public static function text($key): string
    {
        return static::TYPE_ENUM[$key]['text'];
    }

    /**
     * 获得阶段类型
     * @param $key
     * @return int|string
     */
    public static function type($key)
    {
        return static::TYPE_ENUM[$key]['type'];
    }

    /**
     * 获得阶段key
     * @param $type
     * @param int $default 默认返回
     * @return int|string
     */
    public static function idx($type, $default=0){
        foreach(static::TYPE_ENUM as $key=>$item){
            if($type===$item['type']){
                return $key;
            }
        }
        return $default;
    }
}