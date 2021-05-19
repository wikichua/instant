<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth_admin', 'can:access-admin-panel']], function () {
    Route::group(['prefix' => 'user'], function () {
        Route::match(['get'], '', [config('instant.Controllers.User'),'index'])->name('user');
        Route::match(['get'], '{user}/read', [config('instant.Controllers.User'),'show'])->name('user.show');
        Route::match(['get'], 'create', [config('instant.Controllers.User'),'create'])->name('user.create');
        Route::match(['post'], 'create', [config('instant.Controllers.User'),'store'])->name('user.store');
        Route::match(['get'], '{user}/edit', [config('instant.Controllers.User'),'edit'])->name('user.edit');
        Route::match(['put', 'patch'], '{user}/edit', [config('instant.Controllers.User'),'update'])->name('user.update');
        Route::match(['delete'], '{user}/delete', [config('instant.Controllers.User'),'destroy'])->name('user.destroy');
        Route::match(['get'], '{user}/editPassword', [config('instant.Controllers.User'),'editPassword'])->name('user.editPassword');
        Route::match(['put', 'patch'], '{user}/editPassword', [config('instant.Controllers.User'),'updatePassword'])->name('user.updatePassword');

        // pat => personal access token
        Route::group(['prefix' => '{user}/pat'], function () {
            Route::match(['get'], '', [config('instant.Controllers.PAT'),'index'])->name('pat');
            Route::match(['get'], 'create', [config('instant.Controllers.PAT'),'create'])->name('pat.create');
            Route::match(['post'], 'create', [config('instant.Controllers.PAT'),'store'])->name('pat.store');
            Route::match(['delete'], '{pat}/delete', [config('instant.Controllers.PAT'),'destroy'])->name('pat.destroy');
        });
    });
});
