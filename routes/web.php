<?php

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TransactionController;

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
    return view('login');
});

Route::get('/get-session-data', [Controller::class, 'getSessionData']);

Route::post('/user', [UserController::class, 'store'])->name('user.store');
Route::get('/user', [UserController::class, 'index']);

Route::get('/login', [UserController::class, 'login']);
Route::post('/login', [UserController::class, 'checklogin'])->name('user.login');
Route::get('/logout', [UserController::class, 'logout'])->name('user.logout');

Route::get('/deposit', [TransactionController::class, 'deposit'])->name('user.deposit');
Route::post('/deposit', [TransactionController::class, 'deposits'])->name('user.deposits');

Route::get('/withdraw', [TransactionController::class, 'withdraw'])->name('user.withdraw');
Route::post('/withdraw', [TransactionController::class, 'withdraws'])->name('user.withdraws');

Route::get('/transaction', [TransactionController::class, 'showAll']);




