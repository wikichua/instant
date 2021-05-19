<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth_admin', 'can:access-admin-panel']], function () {
    Route::group(['prefix' => 'cronjob'], function () {
        Route::match(['get', 'head'], '', [config('instant.Controllers.Cronjob'),'index'])->name('cronjob');
        Route::match(['get', 'head'], '{cronjob}/read', [config('instant.Controllers.Cronjob'),'show'])->name('cronjob.show');
        Route::match(['get', 'head'], 'create', [config('instant.Controllers.Cronjob'),'create'])->name('cronjob.create');
        Route::match(['post'], 'create', [config('instant.Controllers.Cronjob'),'store'])->name('cronjob.store');
        Route::match(['get', 'head'], '{cronjob}/edit', [config('instant.Controllers.Cronjob'),'edit'])->name('cronjob.edit');
        Route::match(['put', 'patch'], '{cronjob}/edit', [config('instant.Controllers.Cronjob'),'update'])->name('cronjob.update');
        Route::match(['delete'], '{cronjob}/delete', [config('instant.Controllers.Cronjob'),'destroy'])->name('cronjob.destroy');
    });
});
