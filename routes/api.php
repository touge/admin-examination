<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2019-12-18
 * Time: 17:00
 */

use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Router;

//Route::get('gradation', 'GradationController@index')->name('gradation.index');
//Route::get('group/{group}/papers', 'GroupPaperController@papers')->name('group.papers');

/**
 * 试卷分类及分组
 */
Route::post('category', 'CategoryController@index')->name('category.index');


/**
 * 试卷列表
 */
Route::group([
    'prefix'=>'paper',
    'as'=>'paper.'
],function(Router $router){
    Route::post('', 'PaperController@index')->name('list');

    /**
     * 通过alias获得试卷信息
     */
    $router->get('{uuid}','PaperController@uuid')->name('uuid');
});



/**
 * 考试操作
 */
$router->group([
    'prefix'=> 'exam',
    'as'=> 'exam.',
], function(Router $router){
    /**
     * 当前用户的试卷列表
     */
    $router->post('fetch_list','ExamController@fetch_list')->name('fetch_list');

    /**
     * 保存考试信息
     */
    $router->post('store', 'ExamController@store')->name('store');

    /**
     * 考试试题保存
     */
    $router->post('save_questions', 'ExamController@save_questions')->name('save');

});

/**
 * 我的考试
 */
$router->group([
    'prefix'=> 'my',
    'as'=> 'my.',
], function(Router $router){
    $router->post('', 'MyController@index')->name('index');
});

/**
 * 批改试卷
 */
$router->group([
    'prefix'=> 'correction',
    'as'=> 'correction.',
], function(Router $router){
    /**
     * 获得考试列表
     */
    $router->post('fetch_list', 'CorrectionController@fetch_list')->name('fetch_list');

    /**
     * 试卷保存
     */
    $router->post('update', 'CorrectionController@update')->name('update');

    /**
     * 获得一场考试信息
     */
    $router->post('fetch_one/{id}', 'CorrectionController@fetch_one')->name('show');
});
