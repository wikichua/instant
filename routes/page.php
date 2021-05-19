<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth_admin', 'can:access-admin-panel']], function () {
    Route::group(['prefix' => 'page'], function () {
        Route::match(['get','post'], '', [config('instant.Controllers.Page'),'index'])->name('page');
        Route::match(['get'], '{page}/read', [config('instant.Controllers.Page'),'show'])->name('page.show');
        Route::match(['get'], '{page}/preview', [config('instant.Controllers.Page'),'preview'])->name('page.preview');
        Route::match(['get'], 'create', [config('instant.Controllers.Page'),'create'])->name('page.create');
        Route::match(['post'], 'create', [config('instant.Controllers.Page'),'store'])->name('page.store');
        Route::match(['get'], '{page}/edit', [config('instant.Controllers.Page'),'edit'])->name('page.edit');
        Route::match(['put', 'patch'], '{page}/edit', [config('instant.Controllers.Page'),'update'])->name('page.update');
        Route::match(['delete'], '{page}/delete', [config('instant.Controllers.Page'),'destroy'])->name('page.destroy');
        Route::match(['get'], '{brand_id}/templates', [config('instant.Controllers.Page'),'templates'])->name('page.templates');
        Route::match(['post'], '{page}/replicate', [config('instant.Controllers.Page'),'replicate'])->name('page.replicate');
        Route::match(['get'], '{page}/migration', [config('instant.Controllers.Page'),'migration'])->name('page.migration');
    });
});
