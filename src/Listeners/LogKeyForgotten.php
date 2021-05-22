<?php

namespace Wikichua\Instant\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogKeyForgotten
{
    public function __construct()
    {
        //
    }
    public function handle($event)
    {
        if ($event->key != 'LogKeys') {
            $key = cache()->get('LogKeys');
            unset($key[$event->key]);
            cache()->forever('LogKeys', $key);
        }
    }
}
