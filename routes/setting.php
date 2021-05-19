<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth_admin', 'can:access-admin-panel']], function () {
    Route::group(['prefix' => 'setting'], function () {
        Route::match(['get', 'head'], '', [config('instant.Controllers.Setting'),'index'])->name('setting');
        Route::match(['get', 'head'], '{setting}/read', [config('instant.Controllers.Setting'),'show'])->name('setting.show');
        Route::match(['get', 'head'], 'create', [config('instant.Controllers.Setting'),'create'])->name('setting.create');
        Route::match(['post'], 'create', [config('instant.Controllers.Setting'),'store'])->name('setting.store');
        Route::match(['get', 'head'], '{setting}/edit', [config('instant.Controllers.Setting'),'edit'])->name('setting.edit');
        Route::match(['put', 'patch'], '{setting}/edit', [config('instant.Controllers.Setting'),'update'])->name('setting.update');
        Route::match(['delete'], '{setting}/delete', [config('instant.Controllers.Setting'),'destroy'])->name('setting.destroy');
    });
});
