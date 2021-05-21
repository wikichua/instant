<?php

 namespace Wikichua\Instant\Console\Commands;

use Illuminate\Console\Command;

class Indexing extends Command
{
    protected $signature = 'instant:run:indexing {chunk=1000}';
    protected $description = 'Indexing searchable';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $chunk = $this->argument('chunk');
        $this->info('Indexing to Searchable');
        $models = getModelsList();
        app(config('instant.Models.Searchable'))->truncate();
        $searchable = app(config('instant.Models.Searchable'))->query();
        foreach ($models as $model) {
            if (count(app($model)->toSearchableArray()) && $count = app($model)->count()) {
                $this->info("\n".$model);
                $bar = $this->output->createProgressBar($count);
                $bar->start();
                app($model)->query()->orderBy('id')->chunk($chunk, function ($results) use ($searchable, $bar) {
                    foreach ($results as $result) {
                        $searchable->create([
                            'model' => $result->searchableAs(),
                            'model_id' => $result->id,
                            'tags' => $result->toSearchableArray(),
                            'brand_id' => $result->brand_id ?? 0,
                        ]);
                        $bar->advance();
                    }
                });
                $bar->finish();
            }
        }
        $this->info("\nIndexing Completed");
        cache()->flush();
    }
}
