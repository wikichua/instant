<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SeedCachePermissions extends Migration
{
    public function up()
    {
        app(config('dashing.Models.Permission'))->createGroup('Caches', ['Read Caches', 'Delete Caches'], 1);
    }

    public function down()
    {
        app(config('dashing.Models.Permission'))->whereIn('group', [
            'Versionizers',
        ])->delete();
    }
}
