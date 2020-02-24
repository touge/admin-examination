<?php

namespace Touge\AdminExamination\Providers;

use Illuminate\Support\ServiceProvider;
use Touge\AdminExamination\Services\QuestionService;

class QuestionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->singleton('question', function($app){
            return new QuestionService($app);
        });
    }
}
