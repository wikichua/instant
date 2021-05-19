<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth_admin', 'can:access-admin-panel']], function () {
    Route::group(['prefix' => 'role'], function () {
        Route::match(['get', 'post'], '', [config('instant.Controllers.Role'),'index'])->name('role');
        Route::match(['get'], '{role}/read', [config('instant.Controllers.Role'),'show'])->name('role.show');
        Route::match(['get'], 'create', [config('instant.Controllers.Role'),'create'])->name('role.create');
        Route::match(['post'], 'create', [config('instant.Controllers.Role'),'store'])->name('role.store');
        Route::match(['get'], '{role}/edit', [config('instant.Controllers.Role'),'edit'])->name('role.edit');
        Route::match(['put', 'patch'], '{role}/edit', [config('instant.Controllers.Role'),'update'])->name('role.update');
        Route::match(['delete'], '{role}/delete', [config('instant.Controllers.Role'),'destroy'])->name('role.destroy');
    });
});
