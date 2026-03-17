<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Api\SocialAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
    Route::get('/oauth/{provider}/redirect', [SocialAuthController::class, 'redirect'])->name('oauth.redirect');
    Route::get('/oauth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('oauth.callback');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/feed', [CommunityController::class, 'feed'])->name('feed');
    Route::get('/projects', [CommunityController::class, 'projects'])->name('projects');
    Route::get('/events', [CommunityController::class, 'events'])->name('events');
    Route::get('/developers', [CommunityController::class, 'profiles'])->name('profiles');

    Route::prefix('m')->name('mobile.')->group(function () {
        Route::get('/feed', [CommunityController::class, 'mobileFeed'])->name('feed');
        Route::get('/post', [CommunityController::class, 'mobilePost'])->name('post');
        Route::get('/notifications', [CommunityController::class, 'mobileNotifications'])->name('notifications');
        Route::get('/profile', [CommunityController::class, 'mobileProfile'])->name('profile');
        Route::get('/messages', [CommunityController::class, 'mobileMessages'])->name('messages');
    });
});
