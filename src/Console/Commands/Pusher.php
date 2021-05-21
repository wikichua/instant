<?php

 namespace Wikichua\Instant\Console\Commands;

use Illuminate\Console\Command;

class Pusher extends Command
{
    protected $signature = 'instant:run:pusher {--brand=}';
    protected $description = 'Generating Report';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $now = \Carbon\Carbon::now();
        $pushers = app(config('instant.Models.Pusher'))->query()->where('status', 'A')->whereBetween('scheduled_at', [$now, $now->addMinute()]);
        $brand = $this->option('brand');
        if ('' != $brand) {
            $brand = app(config('instant.Models.Brand'))->query()->where('name', $brand)->where('status', 'A')->first();
            if ($brand) {
                $pushers->where('brand_id', $brand->id);
            } else {
                $this->error($brand.' does not activated or not existed!');

                return;
            }
        } else {
            $pushers->whereNull('brand_id');
        }
        $pushers = $pushers->get();
        foreach ($pushers as $pusher) {
            $channel = '';
            if ($brand) {
                $channel = strtolower($brand->name);
            }
            pushered($pusher->toArray(), $channel, $pusher->event, $pusher->locale);
            $pusher->status = 'S';
            $pusher->save();
        }
        $this->line('Process Completed');
        cache()->flush();
    }
}
