<?php

namespace Wikichua\Instant\Facades;

use Illuminate\Support\Facades\Facade;

class Instant extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'instant';
    }
}
