<?php

namespace Touge\AdminExamination\Providers;

use Encore\Admin\Admin;
use Illuminate\Support\ServiceProvider;
use Touge\AdminExamination\Providers\Api\CorrectionServiceProvider;
use Touge\AdminExamination\Providers\Api\ExamServiceProvider;
use Touge\AdminExamination\Providers\Api\MyServiceProvider;
use Touge\AdminExamination\Supports\AdminExamination;
use Illuminate\Support\Facades\Route;

class ModuleServiceProvider extends ServiceProvider
{
    protected $config_file= 'touge-admin-examination.php';

    /**
     * {@inheritdoc}
     */
    public function boot(AdminExamination $extension)
    {
        if (! AdminExamination::boot()) {
            return ;
        }


        if( !file_exists(config_path($this->config_file))){
            $this->loadConfig();
        }

        if ($views = $extension->views()) {
            $this->loadViewsFrom($views, 'admin-examination');
        }

        if ($this->app->runningInConsole() && $assets = $extension->assets()) {
            $this->publishes([__DIR__.'/../../config' => config_path()], 'touge-admin-examination-config');
            $this->publishes([__DIR__.'/../../resources/assets' => public_path('vendor/touge/admin-examination')], 'touge-admin-examination-assets');
        }

        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'admin-examination');
        $this->app->booted(function () {

            Admin::js('vendor/touge/admin-examination/vue.min.js');
            Admin::js('vendor/touge/admin-examination/vue.directive.js');
            AdminExamination::routes(__DIR__ . '/../../routes/web.php');
            static::api_routes(__DIR__ . '/../../routes/api.php');
        });
    }


    /**
     * Set routes for this extension.
     *
     * @param $callback
     */
    public static function api_routes($callback)
    {
        $attributes = array_merge(
            [
                'prefix'=> 'api/examination',
                'namespace'     => '\Touge\AdminExamination\Http\Controllers\Api',
                'as'=> 'api.examination.',
                'middleware'=> ['api', 'jwt.auth:api'],
            ],
            AdminExamination::config('route', [])
        );

        Route::group($attributes, $callback);
    }

    /**
     * Register the module services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(QuestionServiceProvider::class);
        $this->app->register(PaperServiceProvider::class);

        /**
         * api
         */
        $this->app->register(ExamServiceProvider::class);
        $this->app->register(MyServiceProvider::class);
        $this->app->register(CorrectionServiceProvider::class);

        $this->app->register(CustomerMemberServiceProvider::class);
        $this->app->register(GroupPaperServiceProvider::class);
    }

    /**
     * load config file
     * @param $file
     */
    protected function loadConfig(){
        $key = substr($this->config_file, 0, -4);
        $full_path= __DIR__ . '/../../config/' . $this->config_file;
        $this->app['config']->set($key, array_merge_recursive(config($key, []), require $full_path));
    }
}