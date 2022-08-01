<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if (env('IS_SIAP',false))
        return view('siap.landingpage');
    else
        return view('welcome');
})->name('home');

Route::redirect('/login', '/')->name('login');

Route::controller(App\Http\Controllers\OAuthController::class)
    ->prefix('/auth')
    ->name('oauth.')
    ->group(function () {
        Route::get('/redirect', 'redirect')->name('login');
        Route::get('/callback', 'callback')->name('callback');
        Route::get('/logout', 'logout')->name('logout');
        Route::post('/logout_webhook', 'logoutWebhook')->name('logout_webhook');
    });

use Illuminate\Http\Request;

// metode yang digunakan di website SIAP untuk memberikan data user login
// yang sesuai dari SIAP
if (env('IS_SIAP',false)) {
    Route::controller(App\Http\Controllers\UserController::class)
        ->name('users.')
        ->group(function () {
            Route::get('/get_user_detail', 'getUserDetail')->name('get_detail');

            Route::get('/sessions', 'listSession')->name('list_session');
            Route::delete('/sessions/{id}', 'removeSession')->name('remove_session');
            Route::match(['get', 'post'], '/reset_password', 'resetPassword' )->name('reset_password');
    });
    Route::resource('users', App\Http\Controllers\UserController::class);
} else {
    Route::get('/mobile-api/me', [ App\Http\Controllers\MobileController::class, 'getCurrentUserData' ]);
}

