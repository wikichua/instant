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
            $data = cache()->get('LogKeys');
            $data[md5($event->key)] = json_encode($event);
            cache()->forever('LogKeys', $data);
        }
    }
}
