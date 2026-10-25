<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\SportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BetController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Public / User routes
Route::middleware('auth')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/sport/{sport}', [HomeController::class, 'sport'])->name('sport.show');

    Route::get('/bets', [BetController::class, 'index'])->name('bets.index');
    Route::post('/bets', [BetController::class, 'store'])->name('bets.store');
    Route::patch('/bets/{bet}/cancel', [BetController::class, 'cancel'])->name('bets.cancel');

    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('sports', SportController::class)->except(['show']);

    Route::resource('events', EventController::class)->except(['show']);
    Route::post('events/{event}/result', [EventController::class, 'result'])->name('events.result');

    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::post('users/{user}/credit', [UserController::class, 'addCredit'])->name('users.credit');
    Route::patch('users/{user}/toggle', [UserController::class, 'toggleActive'])->name('users.toggle');
});
