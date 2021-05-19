<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/register', [config('instant.Controllers.Auth.RegisteredUser'), 'create'])
                ->middleware('guest')
                ->name('register');

Route::post('/register', [config('instant.Controllers.Auth.RegisteredUser'), 'store'])
                ->middleware('guest');

Route::get('/login', [config('instant.Controllers.Auth.AuthenticatedSession'), 'create'])
                ->middleware('guest')
                ->name('login');

Route::post('/login', [config('instant.Controllers.Auth.AuthenticatedSession'), 'store'])
                ->middleware('guest');

Route::get('/forgot-password', [config('instant.Controllers.Auth.PasswordResetLink'), 'create'])
                ->middleware('guest')
                ->name('password.request');

Route::post('/forgot-password', [config('instant.Controllers.Auth.PasswordResetLink'), 'store'])
                ->middleware('guest')
                ->name('password.email');

Route::get('/reset-password/{token}', [config('instant.Controllers.Auth.NewPassword'), 'create'])
                ->middleware('guest')
                ->name('password.reset');

Route::post('/reset-password', [config('instant.Controllers.Auth.NewPassword'), 'store'])
                ->middleware('guest')
                ->name('password.update');

Route::get('/verify-email', [config('instant.Controllers.Auth.EmailVerificationPrompt'), '__invoke'])
                ->middleware('auth')
                ->name('verification.notice');

Route::get('/verify-email/{id}/{hash}', [config('instant.Controllers.Auth.VerifyEmail'), '__invoke'])
                ->middleware(['auth', 'signed', 'throttle:6,1'])
                ->name('verification.verify');

Route::post('/email/verification-notification', [config('instant.Controllers.Auth.EmailVerificationNotification'), 'store'])
                ->middleware(['auth', 'throttle:6,1'])
                ->name('verification.send');

Route::get('/confirm-password', [config('instant.Controllers.Auth.ConfirmablePassword'), 'show'])
                ->middleware('auth')
                ->name('password.confirm');

Route::post('/confirm-password', [config('instant.Controllers.Auth.ConfirmablePassword'), 'store'])
                ->middleware('auth');

Route::match(['post','get'], '/logout', [config('instant.Controllers.Auth.AuthenticatedSession'), 'destroy'])
                ->middleware('auth')
                ->name('logout');

Route::match(['get'], 'reauth', [config('instant.Controllers.Auth.Reauth'),'reauth'])->middleware('auth')->name('reauth');
Route::match(['post'], 'reauth', [config('instant.Controllers.Auth.Reauth'),'processReauth'])->middleware('auth')->name('reauth');

Route::group(['middleware' => ['auth','reauth_admin']], function () {
    Route::impersonate();
});

Route::match(['post'], 'editor/upload/image', function (Request $request) {
    $url = '';
    if ($request->file('image')->isValid()) {
        $url = asset(
            str_replace(
                'public',
                'storage',
                $request->file('image')
                    ->storeAs(
                        'public/editor',
                        Str::uuid().'.'.$request->file('image')->extension()
                    )
            )
        );
    }
    return response()->json(compact('url'));
})->middleware('auth')->name('editor.upload_image');

Route::match(['get'], '/search', [config('instant.Controllers.GlobalSearch'),'index'])->middleware('auth')->name('global.search');
Route::match(['post'], '/search/suggest', [config('instant.Controllers.GlobalSearch'),'suggest'])->middleware('auth')->name('global.suggest');
