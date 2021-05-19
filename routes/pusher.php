<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth_admin', 'can:access-admin-panel']], function () {
    Route::group(['prefix' => 'pusher'], function () {
        Route::match(['get', 'head'], '', [config('instant.Controllers.Pusher'),'index'])->name('pusher');
        Route::match(['get', 'head'], '{pusher}/read', [config('instant.Controllers.Pusher'),'show'])->name('pusher.show');
        Route::match(['get', 'head'], 'create', [config('instant.Controllers.Pusher'),'create'])->name('pusher.create');
        Route::match(['post'], 'create', [config('instant.Controllers.Pusher'),'store'])->name('pusher.store');
        Route::match(['get', 'head'], '{pusher}/edit', [config('instant.Controllers.Pusher'),'edit'])->name('pusher.edit');
        Route::match(['put', 'patch'], '{pusher}/edit', [config('instant.Controllers.Pusher'),'update'])->name('pusher.update');
        Route::match(['delete'], '{pusher}/delete', [config('instant.Controllers.Pusher'),'destroy'])->name('pusher.destroy');
        Route::match(['post'], 'push/{pusher}', [config('instant.Controllers.Pusher'),'push'])->name('pusher.push');
    });
});
