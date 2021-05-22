<?php

namespace Wikichua\Instant\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogKeyWritten
{
    public function __construct()
    {
        //
    }
    public function handle($event)
    {
        if ($event->key != 'LogKeys') {
            $key = cache()->get('LogKeys');
            $key[$event->key] = json_encode($event);
            cache()->forever('LogKeys', $key);
        }
    }
}
