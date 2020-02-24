<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2019-11-21
 * Time: 14:57
 */

return [
    'module-name'=> '试卷管理',
    'alias'=> '序号',
    'title'=> '试卷名称',
    'is-public'=> '是否公开',
    'time-limit-enable'=> '时间限制',
    'time-limit-value'=> '限制时间',
    'pass-score'=> '及格分',
    'total-score'=> '总分',
    'question-score'=> '题目总数',
    'question-list'=> '试题列表',
    'paper-create'=> '手动组卷',
    'categories'=> [
        'module-name'=> '试卷分类',
        'name'=> '名称',
        'sort-order'=> '排序',
    ],

    'question-null'=> '试题不能为空',
    'data-null'=> '试卷数据不存在',
    'name-null'=> '试卷名称不能为空',
    'category-null'=> '试卷分类不能为空',
    'gradation-null'=> '阶段不能为空',
    'score-error'=> '试卷总分未设置或者设置错误',
    'pass-score-error'=> '及格分数未设置或者设置错误',


    'group'=> [
        'module-name'=> '试卷组',

        'basic-info'=> '基本信息',
        'relation-member'=> '关联人员',
        'relation-paper'=> '关联试卷',

        'teacher'=> '老师',
        'student'=> '学生',

    ],
];