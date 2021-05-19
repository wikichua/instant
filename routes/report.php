<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth_admin', 'can:access-admin-panel']], function () {
    Route::group(['prefix' => 'report'], function () {
        Route::match(['get', 'post'], '', [config('instant.Controllers.Report'),'index'])->name('report');
        Route::match(['get'], '{report}/read', [config('instant.Controllers.Report'),'show'])->name('report.show');
        Route::match(['post'], '{report}/export', [config('instant.Controllers.Report'),'export'])->name('report.export');
        Route::match(['get'], 'create', [config('instant.Controllers.Report'),'create'])->name('report.create');
        Route::match(['post'], 'create', [config('instant.Controllers.Report'),'store'])->name('report.store');
        Route::match(['get'], '{report}/edit', [config('instant.Controllers.Report'),'edit'])->name('report.edit');
        Route::match(['put', 'patch'], '{report}/edit', [config('instant.Controllers.Report'),'update'])->name('report.update');
        Route::match(['delete'], '{report}/delete', [config('instant.Controllers.Report'),'destroy'])->name('report.destroy');
    });
});
