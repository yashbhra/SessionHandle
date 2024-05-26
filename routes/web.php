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
    return view('welcome');
});

Route::post('/check_signin', [App\Http\Controllers\HomeController::class, 'checkSignIn']);
Route::post('/force_login', [App\Http\Controllers\HomeController::class, 'forceLogin']);
Route::get('/force_logout', [App\Http\Controllers\HomeController::class, 'forceLogout']);
Route::get('/check_session', [App\Http\Controllers\HomeController::class, 'checkSession']);

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
