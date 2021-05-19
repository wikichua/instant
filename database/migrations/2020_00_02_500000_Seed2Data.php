<?php

use Illuminate\Database\Migrations\Migration;

class Seed2Data extends Migration
{
    public function up()
    {
        $admin_id = 1;
        app(config('instant.Models.Permission'))->createGroup('Wiki Docs', ['Read Wiki Docs'], $admin_id);
        app(config('instant.Models.Permission'))->createGroup('SEO Manager', ['Manage SEO'], $admin_id);
    }

    public function down()
    {
        app(config('instant.Models.Permission'))->whereIn('group', ['Wiki Docs', 'SEO Manager'])->delete();
    }
}
