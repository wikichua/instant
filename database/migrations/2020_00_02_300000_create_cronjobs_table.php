<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCronjobsTable extends Migration
{
    public function up()
    {
        cache()->tags(['fillable', 'cronjobs'])->flush();
        Schema::create('cronjobs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable()->default('');
            $table->string('mode', 4)->nullable()->default('art'); // [art/exec]
            $table->string('timezone')->nullable()->default('UTC');
            $table->string('command')->nullable();
            $table->string('frequency')->nullable()->default('everyMinute');
            $table->string('status', 1)->nullable()->default('I');
            $table->integer('created_by')->nullable()->default(1);
            $table->integer('updated_by')->nullable()->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
        app(config('instant.Models.Permission'))->createGroup('Cronjobs', ['create-cronjobs', 'read-cronjobs', 'update-cronjobs', 'delete-cronjobs'], 1);
        app(config('instant.Models.Setting'))->create(['created_by' => 1, 'updated_by' => 1, 'key' => 'cronjob_status', 'value' => ['A' => 'Active', 'I' => 'Inactive']]);
        app(config('instant.Models.Cronjob'))->query()->create([
            'name' => 'Inspire',
            'command' => 'inspire',
            'status' => 'A',
        ]);
    }

    public function down()
    {
        app(config('instant.Models.Permission'))->whereIn('group', [
            'Cronjobs',
        ])->delete();
        app(config('instant.Models.Setting'))->whereIn('key', [
            'cronjob_status',
        ])->delete();
        Schema::dropIfExists('cronjobs');
    }
}
