<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth_admin', 'can:access-admin-panel']], function () {
    Route::group(['prefix' => 'component'], function () {
        Route::match(['get', 'post'], '', [config('instant.Controllers.Component'),'index'])->name('component');
        Route::match(['get'], '{model}/read', [config('instant.Controllers.Component'),'show'])->name('component.show');
        Route::match(['post'], '{model}/try', [config('instant.Controllers.Component'),'try'])->name('component.try');
    });
});
