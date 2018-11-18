<?php

namespace Dazeroit\Theme;

use Dazeroit\Theme\Console\Commands\ThemeClone;
use Dazeroit\Theme\Console\Commands\ThemeList;
use Dazeroit\Theme\Console\Commands\ThemeNew;
use Dazeroit\Theme\Console\Commands\ThemeNpm;
use Dazeroit\Theme\Console\Commands\ThemePublish;
use Dazeroit\Theme\Console\Commands\ThemeRemove;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class ThemeServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        if(config('theme.dev-env')){
            require_once __DIR__.'/Support/helpers.php';
        }
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'dazeroit');
        $this->loadViewsFrom(theme_path(), config('theme.namespace'));
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }else{
            $this->bootForRequest();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/theme.php', 'theme');
        $this->app->singleton('theme', function ($app) {
            return new Theme;
        });

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['theme'];
    }
    
    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/theme.php' => config_path('theme.php'),
        ], 'theme.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/dazeroit'),
        ], 'theme.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/dazeroit'),
        ], 'theme.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/dazeroit'),
        ], 'theme.views');*/

        // Registering package commands.
        $this->commands([
            ThemeNew::class,
            ThemeNpm::class,
            ThemeClone::class,
            ThemeRemove::class,
            ThemeList::class,
            ThemePublish::class,
        ]);
    }
    protected function bootForRequest(){
        $this->bootBladeDirectives();
    }
    protected function bootBladeDirectives(){

        Blade::directive('content',function($expression){
            return '<?php echo $'.ThemeFactory::CONTENT_VAR.'; ?>' ;
        });

        Blade::directive('partial',function($expression){
            $exp = explode(',',$expression,2);
            $exp[0] = "'".\Dazeroit\Theme\Facades\Theme::current()->getPartialNamespace(str_replace(["'",'"'],'',$exp[0]))."'";
            $exp[1] = $exp[1] ?? null;
            if(!$exp[1])unset($exp[1]);
            $expression = implode(',',$exp);
            return "<?php echo \$__env->make($expression, array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>" ;
        });
    }
}
