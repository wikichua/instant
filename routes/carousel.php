<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth_admin', 'can:access-admin-panel']], function () {
    Route::group(['prefix' => 'carousel'], function () {
        Route::match(['get', 'head'], '', [config('instant.Controllers.Carousel'),'index'])->name('carousel');
        Route::match(['get', 'head'], '{model}/read', [config('instant.Controllers.Carousel'),'show'])->name('carousel.show');
        Route::match(['get', 'head'], 'create', [config('instant.Controllers.Carousel'),'create'])->name('carousel.create');
        Route::match(['post'], 'create', [config('instant.Controllers.Carousel'),'store'])->name('carousel.store');
        Route::match(['get', 'head'], '{model}/edit', [config('instant.Controllers.Carousel'),'edit'])->name('carousel.edit');
        Route::match(['put', 'patch'], '{model}/edit', [config('instant.Controllers.Carousel'),'update'])->name('carousel.update');
        Route::match(['delete'], '{model}/delete', [config('instant.Controllers.Carousel'),'destroy'])->name('carousel.destroy');
        Route::match(['get', 'head'], 'orderable/{groupValue?}/{brand_id?}', [config('instant.Controllers.Carousel'),'orderable'])->name('carousel.orderable');
        Route::match(['post'], 'orderable/{groupValue?}/{brand_id?}', [config('instant.Controllers.Carousel'),'orderableUpdate'])->name('carousel.orderableUpdate');
    });
});
