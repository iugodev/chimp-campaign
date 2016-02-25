<?php namespace ChimpCampaigns;


use Illuminate\Support\ServiceProvider;
use ChimpCampaigns\Commands\MigrationCommand;


class ChimpCampaignServiceProvider extends ServiceProvider {


    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // Publish config files
        $this->publishes([
            __DIR__.'/Config/config.php' => config_path('chimpCampaigns.php'),
        ]);
        // Register commands
        $this->commands('command.chimpCampaigns.migration');

        $this->app->router->group(['namespace' => 'ChimpCampaigns\Controllers'],
            function(){
                if (! $this->app->routesAreCached()) {
                    require __DIR__.'/routes.php';
                }
            });

    }
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        $this->registerCommands();

        $this->registerChimpCampaigns();
    }

    private function registerChimpCampaigns()
    {
        $this->app->bind('ChimpCampaigns', function ($app) {
            return new ChimpCampaigns($app);
        });

        $this->app->alias('ChimpCampaigns', 'ChimpCampaigns\ChimpCampaigns');
    }


    /**
     * Register the artisan commands.
     *
     * @return void
     */
    private function registerCommands()
    {
        $this->app->singleton('command.chimpCampaigns.migration', function ($app) {
            return new MigrationCommand();
        });
    }

    /**
     * Get the services provided.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'command.chimpCampaigns.migration'
        ];
    }
}