<?php

namespace Jacotheron\ForgeMonitor;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Jacotheron\ForgeMonitor\Commands\ForgeMonitorCommand;

class ForgeMonitorServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('forge-monitor')
            ->hasConfigFile()
            ->hasViews()
            //->hasMigration('create_forge_monitor_table')
            ->hasCommand(ForgeMonitorCommand::class);
    }
}
