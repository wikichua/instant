<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth_admin', 'can:access-admin-panel']], function () {
    Route::group(['prefix' => 'logviewer'], function () {
        Route::match(['get', 'post'], '{folder?}/{file?}', [config('instant.Controllers.LogViewer'),'index'])->name('logviewer');
        Route::match(['post'], 'download/{folder?}/{file?}', [config('instant.Controllers.LogViewer'),'download'])->name('logviewer');
        Route::match(['delete'], 'delete/{folder?}/{file?}', [config('instant.Controllers.LogViewer'),'destroy'])->name('logviewer');
    });
});
