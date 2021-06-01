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
            $data = cache()->get('LogKeys');
            unset($data[md5($event->key)]);
            cache()->forever('LogKeys', $data);
        }
    }
}
