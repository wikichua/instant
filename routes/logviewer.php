<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth_admin', 'can:access-admin-panel']], function () {
    Route::group(['prefix' => 'logviewer'], function () {
        Route::match(['get', 'post'], '', [config('instant.Controllers.LogViewer'),'index'])->name('logviewer');
    });
});
