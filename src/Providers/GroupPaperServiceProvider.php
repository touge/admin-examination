<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2020-01-06
 * Time: 15:58
 */

namespace Touge\AdminExamination\Providers;


use Illuminate\Support\ServiceProvider;
use Touge\AdminExamination\Services\GroupPaperService;

class GroupPaperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->singleton('group_paper', function($app){
            return new GroupPaperService($app);
        });
    }
}