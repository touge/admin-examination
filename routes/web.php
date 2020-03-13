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

    Route::resource("paper-category" , 'Admin\PaperCategoryController');

    Route::resource("paper-group", 'Admin\PaperGroupController');
    Route::resource("paper" , 'Admin\PaperController');

});


