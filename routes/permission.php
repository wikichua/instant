<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth_admin', 'can:access-admin-panel']], function () {
    Route::group(['prefix' => 'permission'], function () {
        Route::match(['get', 'post', 'head'], '', [config('instant.Controllers.Permission'),'index'])->name('permission');
        Route::match(['get', 'head'], '{permission}/read', [config('instant.Controllers.Permission'),'show'])->name('permission.show');
        Route::match(['get', 'head'], 'create', [config('instant.Controllers.Permission'),'create'])->name('permission.create');
        Route::match(['post'], 'create', [config('instant.Controllers.Permission'),'store'])->name('permission.create');
        Route::match(['get', 'head'], '{permission}/edit', [config('instant.Controllers.Permission'),'edit'])->name('permission.edit');
        Route::match(['put', 'patch'], '{permission}/edit', [config('instant.Controllers.Permission'),'update'])->name('permission.edit');
        Route::match(['delete'], '{permission}/delete', [config('instant.Controllers.Permission'),'destroy'])->name('permission.destroy');
    });
});
