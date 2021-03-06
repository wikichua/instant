<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PageTable extends Migration
{
    public function up()
    {
        cache()->tags(['fillable'])->flush();
        Schema::create('pages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('brand_id')->nullable()->default(0);
            $table->string('locale', 2)->nullable()->default('en');
            $table->string('name')->nullable()->default('');
            $table->string('template')->nullable()->default('layouts.main');
            $table->text('slug')->nullable();
            $table->longText('blade')->nullable();
            $table->json('styles')->nullable();
            $table->json('scripts')->nullable();
            $table->date('published_at')->nullable();
            $table->date('expired_at')->nullable();
            $table->string('status', 1)->nullable()->default('');
            $table->integer('created_by')->nullable()->default(0);
            $table->integer('updated_by')->nullable()->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pages');
    }
}
