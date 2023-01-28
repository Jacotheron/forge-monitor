<?php

namespace Jacotheron\ForgeMonitor;

use Illuminate\Support\ServiceProvider;
use Jacotheron\ForgeMonitor\Console\MonitorCommand;

class ForgeMonitorServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if($this->app->runningInConsole()){
            $this->publishes([
                __DIR__.'/../config/forge-monitor.php' => config_path('forge-monitor.php')
            ], 'forge-monitor-config');
            $this->publishes([
                __DIR__.'/../views' => resource_path('views/vendor/forge-monitor')
            ], 'forge-monitor-views');

            $this->commands([MonitorCommand::class]);
        }

        $this->loadViewsFrom(__DIR__.'/../views/', 'forge-monitor');
    }

    public function register():void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/forge-monitor.php', 'forge-monitor');
    }
}