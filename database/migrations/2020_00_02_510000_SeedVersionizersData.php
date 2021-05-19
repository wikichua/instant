<?php

use Illuminate\Database\Migrations\Migration;

class SeedVersionizersData extends Migration
{
    public function up()
    {
        app(config('instant.Models.Permission'))->createGroup('Versionizers', ['Read Versionizers', 'Revert Versionizers', 'Delete Versionizers'], 1);
    }

    public function down()
    {
        app(config('instant.Models.Permission'))->whereIn('group', [
            'Versionizers',
        ])->delete();
    }
}
