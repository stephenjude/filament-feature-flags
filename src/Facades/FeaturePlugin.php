<?php

namespace Stephenjude\FeaturePlugin\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Stephenjude\FeaturePlugin\FeaturePlugin
 */
class FeaturePlugin extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Stephenjude\FeaturePlugin\FeaturePlugin::class;
    }
}
