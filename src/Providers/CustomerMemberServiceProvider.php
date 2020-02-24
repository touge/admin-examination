<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2020-01-06
 * Time: 15:01
 */

namespace Touge\AdminExamination\Providers;


use Illuminate\Support\ServiceProvider;
use Touge\AdminExamination\Services\CustomerMemberService;

class CustomerMemberServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('customer_member', function($app){
            return new CustomerMemberService($app);
        });
    }
}