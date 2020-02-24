<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2019-11-22
 * Time: 17:37
 */

namespace Touge\AdminExamination\Providers;


use Illuminate\Support\ServiceProvider;
use Touge\AdminExamination\Services\PaperService;

class PaperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->singleton('paper', function($app){
            return new PaperService($app);
        });
    }
}