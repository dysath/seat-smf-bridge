<?php

namespace Denngarr\Seat\SmfBridge;

use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application;
use Denngarr\Seat\SmfBridge\Commands\SmfBridgeUserUpdate;

class SmfBridgeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

        $this->addCommands();
        $this->addRoutes();
        $this->addViews();
//        $this->addPublications();
//        $this->addTranslations();
//        $this->registerServices();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
	$this->mergeConfigFrom(
            __DIR__ . '/Config/smfbridge.database.php', 'database.connections');

	$this->mergeConfigFrom(
            __DIR__ . '/Config/smfbridge.permissions.php', 'web.permissions');

        $this->mergeConfigFrom(
            __DIR__ . '/Config/smfbridge.sidebar.php', 'package.sidebar');
    }

    private function addRoutes()
    {
        if (!$this->app->routesAreCached()) {
            include __DIR__ . '/Http/routes.php';
        }
    }

    private function addViews()
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'smfbridge');
    }

    private function addCommands()
    {
        $this->commands([
            SmfBridgeUserUpdate::class
        ]);
    }

}

