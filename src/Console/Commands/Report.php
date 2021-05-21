<?php

 namespace Wikichua\Instant\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class Report extends Command
{
    protected $signature = 'instant:run:report {name?} {--method=async} {--clearcache}';
    protected $description = 'Generating Report';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $name = $this->argument('name', '');
        $method = $this->option('method');
        $clearcache = $this->option('clearcache');
        $reports = app(config('instant.Models.Report'))->query()->where('status', 'A');
        if ('' != $name) {
            $reports->where('name', $name);
        }
        $reports = $reports->get();
        foreach ($reports as $report) {
            if (null == Cache::get('report-'.str_slug($report->name))) {
                switch ($method) {
                    case 'queue':
                        $this->queue($report, $clearcache);

                        break;

                    default:
                        $this->sync($report, $clearcache);

                        break;
                }
            }
        }
        cache()->flush();
    }

    protected function queue($report, $clearcache)
    {
        // https://laravel.com/docs/8.x/queues#supervisor-configuration
        // art queue:work --queue=report_processing
        if ($clearcache) {
            cache()->forget('report-'.str_slug($report->name));
        }
        dispatch(function () use ($report) {
            cache()->remember(
                'report-'.str_slug($report->name),
                $report->cache_ttl,
                function () use ($report) {
                    $results = [];
                    if (count($report->queries)) {
                        foreach ($report->queries as $key => $sql) {
                            $results[$key]['sql'] = $sql;
                            $results[$key]['data'] = array_map(function ($value) {
                                return (array) $value;
                            }, \DB::select($sql));
                            $columns = array_keys($results[$key]['data'][0]);
                            $results[$key]['columns'] = array_map(function ($value) {
                                return ['title' => $value, 'data' => $value];
                            }, array_keys($results[$key]['data'][0]));
                        }
                    }

                    return $results;
                }
            );
            $report->generated_at = \Carbon\Carbon::now();
            $report->next_generate_at = \Carbon\Carbon::now()->addSeconds($report->cache_ttl);
            $report->saveQuietly();
        })->onQueue('report_processing');
    }

    protected function sync($report, $clearcache)
    {
        if ($clearcache) {
            cache()->forget('report-'.str_slug($report->name));
        }
        cache()->remember(
            'report-'.str_slug($report->name),
            $report->cache_ttl,
            function () use ($report) {
                $results = [];
                if (count($report->queries)) {
                    $bar = $this->output->createProgressBar(count($report->queries));
                    $bar->start();
                    foreach ($report->queries as $key => $sql) {
                        $results[$key]['sql'] = $sql;
                        $results[$key]['data'] = array_map(function ($value) {
                            return (array) $value;
                        }, \DB::select($sql));
                        $columns = array_keys($results[$key]['data'][0]);
                        $results[$key]['columns'] = array_map(function ($value) {
                            return ['title' => $value, 'data' => $value];
                        }, array_keys($results[$key]['data'][0]));
                        $bar->advance();
                    }
                    $bar->finish();
                }

                $report->generated_at = \Carbon\Carbon::now();
                $report->next_generate_at = \Carbon\Carbon::now()->addSeconds($report->cache_ttl);
                $report->saveQuietly();
                return $results;
            }
        );
    }
}
