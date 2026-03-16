<?php

namespace Jacotheron\ForgeMonitor\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Jacotheron\ForgeMonitor\ForgeMonitor
 */
class ForgeMonitor extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Jacotheron\ForgeMonitor\ForgeMonitor::class;
    }
}
