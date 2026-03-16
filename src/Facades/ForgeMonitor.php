<?php

namespace Xilix\ForgeMonitor\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Xilix\ForgeMonitor\ForgeMonitor
 */
class ForgeMonitor extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Xilix\ForgeMonitor\ForgeMonitor::class;
    }
}
