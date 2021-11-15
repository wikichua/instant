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
            if (in_array(app()->environment(), $envs)) {
                $commands = $this->getIncludeWildcardDisabledCommands();
                foreach ($commands as $command) {
                    $this->command($command, function () use ($command) {
                        $this->comment('You are not allowed to "'.$command.'" in '.app()->environment().'!');
                    })->describe('This command has been disabled in '.app()->environment().'.')->setHidden(true);
                }
            }
        }
    }

    private function getIncludeWildcardDisabledCommands()
    {
        $cloned_artisan = clone $this;
        $all_commands = array_keys($cloned_artisan->getArtisan()->all());
        $disabled_commands = $this->commands_disabled[app()->environment()];
        $wildcard_commands = [];
        foreach ($disabled_commands as $i => $disabled_command) {
            if (\Str::endsWith($disabled_command,'*')) {
                $wildcard_commands[] = rtrim($disabled_command,'*');
                unset($disabled_commands[$i]);
            }
        }
        foreach ($all_commands as $command) {
            if (\Str::startsWith($command, $wildcard_commands)) {
                $disabled_commands[] = $command;
            }
        }
        return $disabled_commands;
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
