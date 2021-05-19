<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth_admin', 'can:access-admin-panel']], function () {
    Route::group(['prefix' => 'nav'], function () {
        Route::match(['get','post'], '', [config('instant.Controllers.Nav'),'index'])->name('nav');
        Route::match(['get'], '{nav}/read', [config('instant.Controllers.Nav'),'show'])->name('nav.show');
        Route::match(['get'], 'create', [config('instant.Controllers.Nav'),'create'])->name('nav.create');
        Route::match(['post'], 'create', [config('instant.Controllers.Nav'),'store'])->name('nav.store');
        Route::match(['get'], '{nav}/edit', [config('instant.Controllers.Nav'),'edit'])->name('nav.edit');
        Route::match(['put', 'patch'], '{nav}/edit', [config('instant.Controllers.Nav'),'update'])->name('nav.update');
        Route::match(['delete'], '{nav}/delete', [config('instant.Controllers.Nav'),'destroy'])->name('nav.destroy');
        Route::match(['get'], '{brand_id}/pages', [config('instant.Controllers.Nav'),'pages'])->name('nav.pages');
        Route::match(['post'], '{nav}/replicate', [config('instant.Controllers.Nav'),'replicate'])->name('nav.replicate');
        Route::match(['get'], 'orderable/{groupValue?}/{brand_id?}', [config('instant.Controllers.Nav'),'orderable'])->name('nav.orderable');
        Route::match(['post'], 'orderable/{groupValue?}/{brand_id?}', [config('instant.Controllers.Nav'),'orderableUpdate'])->name('nav.orderableUpdate');
        Route::match(['get'], '{nav}/migration', [config('instant.Controllers.Nav'),'migration'])->name('nav.migration');
    });
});
