<?php

namespace Wikichua\Instant\Http\Traits;

use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Stringable;

trait ArtisanTrait
{
    public function disableCommands()
    {
        $this->mustDisableCommands(['breeze:install','ui:auth','ui:controllers']);
        if (isset($this->commands_disabled)) {
            $envs = array_keys($this->commands_disabled);
            $app_env = app()->environment();
            // $allCommands = array_keys($this->getArtisan()->all());
            if (in_array($app_env, $envs)) {
                $commands = $this->commands_disabled[$app_env];
                foreach ($commands as $command) {
                    $this->command($command, function () use ($app_env) {
                        $this->comment('You are not allowed to do this in '.$app_env.'!');
                    })->describe('This command has been disabled in '.$app_env.'.')->setHidden(true);
                }
            }
        }
    }

    private function mustDisableCommands(array $commands)
    {
        foreach ($commands as $command) {
            $this->command($command, function () {
                $this->comment('You are not allowed to do this!');
            })->describe('This command has been disabled.')->setHidden(true);
        }
    }

    public function runCronjobs(Schedule $schedule)
    {
        $cronjobs = cache()->tags('cronjob')->rememberForever('cronjobs', function () {
            return app(config('instant.Models.Cronjob'))->whereStatus('A')->get();
        });
        foreach ($cronjobs as $cronjob) {
            $frequency = $cronjob->frequency;
            $cron = app(config('instant.Models.Cronjob'))->find($cronjob->id);
            $time = Carbon::now()->timezone($cron->timezone)->toDateTimeString();
            $outputed = is_array($cron->output) ? $cron->output : [];
            if ('art' == $cronjob->mode) {
                $schedule->command($cronjob->command)->{$frequency}()
                    ->timezone($cronjob->timezone)
                    ->onSuccess(function (Stringable $output) use ($cron, $time, $outputed) {
                        $cron->output = array_merge([$time => $output], $outputed);
                        $cron->save();
                    })
                    ->onFailure(function (Stringable $output) use ($cron, $time, $outputed) {
                        $cron->output = array_merge([$time => $output], $outputed);
                        $cron->save();
                    });
            } else {
                $schedule->exec($cronjob->command);
            }
        }
    }
}
