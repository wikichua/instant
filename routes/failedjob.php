<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth_admin', 'can:access-admin-panel']], function () {
    Route::group(['prefix' => 'failed_job'], function () {
        Route::match(['get', 'head'], '', [config('instant.Controllers.FailedJob'),'index'])->name('failedjob');
        Route::match(['get', 'head'], '{failedjob}/read', [config('instant.Controllers.FailedJob'),'show'])->name('failedjob.show');
        Route::match(['post'], '{failedjob}/retry', [config('instant.Controllers.FailedJob'),'retry'])->name('failedjob.retry');
    });
});
