<?php

namespace Wikichua\Instant\Facades;

use Illuminate\Support\Facades\Facade;

class Help extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Wikichua\Instant\Repos\Help::class;
    }
}
