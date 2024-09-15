<?php

use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/verificado', function () {
    return view('verificado');
});

Route::get('/email', function () {return view('email');})->name("email");
Route::get('/confirm/{id}', [UsersController::class, 'confirmEmail'])->name('confirm')->where('id', '[0-9]+');