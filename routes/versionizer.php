<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth_admin', 'can:access-admin-panel']], function () {
    Route::group(['prefix' => 'versionizer'], function () {
        Route::match(['get', 'post'], '', [config('instant.Controllers.Versionizer'),'index'])->name('versionizer');
        Route::match(['get'], '{versionizer}/read', [config('instant.Controllers.Versionizer'),'show'])->name('versionizer.show');
        Route::match(['put', 'patch'], '{versionizer}/revert', [config('instant.Controllers.Versionizer'),'revert'])->name('versionizer.revert');
        Route::match(['delete'], '{versionizer}/delete', [config('instant.Controllers.Versionizer'),'destroy'])->name('versionizer.destroy');
    });
});
