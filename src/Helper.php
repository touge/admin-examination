<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2020-03-18
 * Time: 17:30
 */

$answer_null ="无答案";

if(!function_exists('format_single_choice_view')){
    /**
     * 格式化输出到模板中的题目答案
     * 单、复选(type:1,2)
     *
     * @param array $answers
     * @return array
     */
    function format_single_choice_view(Array $answers)
    {
        //__('admin-examination::paper.correction.marking')

        if(count($answers) ==0 ) return __("admin-examination::paper.answer_null");
        dd($answers);
        $input_val=[];
        foreach($answers as $answer){
            array_push($input_val, formatNumberToLetter($answer));
        }
        return implode(',', $input_val);
    }
}

if(!function_exists('format_true_false_view')){
    /**
     * 格式化输出到模板中的题目答案
     * 判断题(type:3)
     * @param $answers
     * @return array|string|null
     */
    function format_true_false_view(Array $answers)
    {
        if(count($answers) == 0) return __("admin-examination::paper.answer_null");
        return formatNumberToLetter($answers[0]);
    }
}

if(!function_exists('format_fill_view')){
    /**
     * 格式化输出到模板中的题目答案
     * 填空题(type:4)
     *
     * @param $answer
     * @return array|string|null
     */
    function format_fill_view($answer)
    {
        if($answer==null) return __("admin-examination::paper.answer_null");
        return $answer;
    }
}

if(!function_exists('format_text_view')){
    /**
     * 格式化输出到模板中的题目答案
     * 问答题(type:5)
     *
     * @param array $answers
     * @return array|string|null
     */
    function format_text_view($answers)
    {
        if(!$answers[0] ) return __("admin-examination::paper.answer_null");
        return $answers[0];
    }
}


if(!function_exists('formatNumberToLetter')){
    /**
     * 数字转字母
     * @param $number
     * @return string
     */
    function formatNumberToLetter($number)
    {
        return chr(ord('A')+$number);
    }
}