<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth_admin', 'can:access-admin-panel']], function () {
    Route::group(['prefix' => 'brand'], function () {
        Route::match(['get', 'post'], '', [config('instant.Controllers.Brand'),'index'])->name('brand');
        Route::match(['get'], '{model}/read', [config('instant.Controllers.Brand'),'show'])->name('brand.show');
        Route::match(['get'], '{model}/edit', [config('instant.Controllers.Brand'),'edit'])->name('brand.edit');
        Route::match(['put', 'patch'], '{model}/edit', [config('instant.Controllers.Brand'),'update'])->name('brand.update');
    });
});
