<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth_admin', 'can:access-admin-panel']], function () {
    Route::group(['prefix' => 'cache'], function () {
        Route::match(['get'], '', [config('instant.Controllers.Cache'),'index'])->name('cache');
        Route::match(['get'], '{cache}/read', [config('instant.Controllers.Cache'),'show'])->name('cache.show');
        Route::match(['put', 'patch'], '{cache}/revert', [config('instant.Controllers.Cache'),'revert'])->name('cache.revert');
        Route::match(['delete'], '{cache}/delete', [config('instant.Controllers.Cache'),'destroy'])->name('cache.destroy');
    });
});
