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
        return view('landingpage_siap');
    else
        return view('welcome');
})->name('home');

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

Route::get('/get_user_detail', function(Request $request) {
    $decodedAccessToken = parseJWTToken($request->token);

    return response()->json(App\Models\User::where('nik', $decodedAccessToken->nik)->first());
});
