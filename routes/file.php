<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth_admin', 'can:access-admin-panel']], function () {
    Route::group(['prefix' => 'file'], function () {
        Route::match(['post'], 'directories', [config('instant.Controllers.File'),'directories'])->name('folder.directories');
        Route::match(['put'], 'directories/make/{path?}', [config('instant.Controllers.File'),'make'])->name('folder.make');
        Route::match(['put'], 'directories/{path?}/rename', [config('instant.Controllers.File'),'change'])->name('folder.change');
        Route::match(['put'], 'directories/{path?}/clone', [config('instant.Controllers.File'),'clone'])->name('folder.clone');
        Route::match(['delete'], 'directories/{path?}/remove', [config('instant.Controllers.File'),'remove'])->name('folder.remove');

        // Route::match(['get', 'head'], '{file}/read', [config('instant.Controllers.File'),'show'])->name('file.show');

        Route::match(['get', 'head'], '/{path?}', [config('instant.Controllers.File'),'index'])->name('file');
        Route::match(['post'], 'upload/{path?}', [config('instant.Controllers.File'),'upload'])->name('file.upload');
        Route::match(['put', 'patch'], '{path?}/rename', [config('instant.Controllers.File'),'rename'])->name('file.rename');
        Route::match(['put', 'patch'], '{path?}/duplicate', [config('instant.Controllers.File'),'duplicate'])->name('file.duplicate');
        Route::match(['delete'], '{path?}/delete', [config('instant.Controllers.File'),'destroy'])->name('file.destroy');
    });
});
