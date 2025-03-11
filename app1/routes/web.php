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

Route::get('/set-session', function () {
    session(['user' => 'Laravel User from APP 1']);
    return "Session set in " . request()->getHost();
});

Route::get('/get-session', function () {
    return session('user', 'No session found') . " from " . request()->getHost();
});

