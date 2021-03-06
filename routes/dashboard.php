<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth_admin', 'can:access-admin-panel']], function () {
    if (class_exists('\UniSharp\LaravelFilemanager\Lfm')) {
        Route::group(['prefix' => 'laravel-filemanager'], function () {
            \UniSharp\LaravelFilemanager\Lfm::routes();
        });
    }

    Route::group(['prefix' => ''], function () {
        Route::match(['get', 'post'], '/', [config('instant.Controllers.Dashboard'),'index'])->name('dashboard');
        Route::match(['get'], '/lfm', [config('instant.Controllers.Dashboard'),'lfm'])->name('lfm.home');
        Route::match(['get'], '/seo', [config('instant.Controllers.Dashboard'),'seo'])->name('seo.home');
        Route::match(['get'], '/opcache', [config('instant.Controllers.Dashboard'),'opcache'])->name('opcache.home');
        Route::match(['get'], '/wiki/{file?}', [config('instant.Controllers.Dashboard'),'wiki'])->name('wiki.home');
    });
});
