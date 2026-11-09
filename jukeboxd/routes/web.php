<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Register
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Login
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Profile
Route::get('/profile', function () {
    return view('welcome');
})->middleware('auth')->name('profile');

// Rating
Route::post('/albums/{spotify_album_id}/rate', [RatingController::class, 'rate'])
    ->middleware('auth')
    ->name('ratings.rate');

// Search
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/search/live', [SearchController::class, 'live'])->name('search.live');



