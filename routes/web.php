<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix'        => 'examination',
    'namespace'     => '\Touge\AdminExamination\Http\Controllers',
    'middleware'    => config('admin.route.middleware'),
    'as'=> 'exams.'
],function(){
    Route::get("question/search" , 'Admin\QuestionController@search')->name('question.search');
    Route::post("question/search" , 'Admin\QuestionController@search')->name('question.search');


    Route::match(['get', 'post'], 'question/search', 'Admin\QuestionController@search')->name('question.search');
    Route::post("question/previews" , 'Admin\QuestionController@previews')->name('question.previews');

    Route::resource("question" , 'Admin\QuestionController');
    Route::resource("tag" , 'Admin\TagController');
    Route::resource("tag-group" , 'Admin\TagGroupController');

    Route::resource("{gradation}/paper-category" , 'Admin\PaperCategoryController');

    Route::resource("{gradation}/paper-group", 'Admin\PaperGroupController');
    Route::resource("{gradation}/paper" , 'Admin\PaperController');


    Route::resource('member/teacher', 'Admin\Member\TeacherController');
    Route::resource('member/student', 'Admin\Member\StudentController');
});


