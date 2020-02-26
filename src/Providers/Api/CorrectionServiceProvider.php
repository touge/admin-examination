<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2019-12-10
 * Time: 19:20
 */

namespace Touge\AdminExamination\Providers\Api;


use Illuminate\Support\ServiceProvider;
use Touge\AdminExamination\Services\Api\CorrectionService;

/**
 * 学生试卷操作
 * Class CorrectionServiceProvider
 * @package Touge\AdminExamination\Providers
 */
class CorrectionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->singleton('api_correction', function($app){
            return new CorrectionService($app);
        });
    }
}