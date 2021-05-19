<?php

use Illuminate\Database\Migrations\Migration;

class SeedNavsData extends Migration
{
    public function up()
    {
        app(config('instant.Models.Permission'))->createGroup('Pages', ['Migrate Pages'], 1);
        app(config('instant.Models.Permission'))->createGroup('Navs', ['Migrate-navs'], 1);
    }

    public function down()
    {
    }
}
