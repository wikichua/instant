<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth_admin', 'can:access-admin-panel']], function () {
    Route::group(['prefix' => 'mailer'], function () {
        Route::match(['get', 'head'], '', [config('instant.Controllers.Mailer'),'index'])->name('mailer');
        Route::match(['get', 'head'], '{mailer}/read', [config('instant.Controllers.Mailer'),'show'])->name('mailer.show');
        Route::match(['get', 'head'], '{mailer}/edit', [config('instant.Controllers.Mailer'),'edit'])->name('mailer.edit');
        Route::match(['put', 'patch'], '{mailer}/edit', [config('instant.Controllers.Mailer'),'update'])->name('mailer.update');
        Route::match(['delete'], '{mailer}/delete', [config('instant.Controllers.Mailer'),'destroy'])->name('mailer.destroy');
        Route::match(['get', 'head'], '{mailer}/preview', [config('instant.Controllers.Mailer'),'preview'])->name('mailer.preview');
        Route::match(['post'], '{mailer}/preview', [config('instant.Controllers.Mailer'),'preview'])->name('mailer.preview');
    });
});
