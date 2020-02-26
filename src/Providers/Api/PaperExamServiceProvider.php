<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2019-12-10
 * Time: 19:20
 */

namespace Touge\AdminExamination\Providers\Api;


use Illuminate\Support\ServiceProvider;
use Touge\AdminExamination\Services\Api\PaperExamService;

class PaperExamServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->singleton('api_paper_exam', function($app){
            return new PaperExamService($app);
        });
    }
}