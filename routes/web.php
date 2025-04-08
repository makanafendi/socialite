<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PostsController;
use App\Http\Controllers\ProfilesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\ChatController;

// Redirect '/' to '/home'
Route::get('/', function () {
    return redirect('/home');
});


Auth::routes();

Route::get('/p/create', [PostsController::class, 'create']);
Route::post('/p', [PostsController::class, 'store']);
Route::get('/p/{post}', [PostsController::class, 'show']);

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('/profile/{user}', [ProfilesController::class, 'index'])->name('profile.show');
Route::get('/profile/{user}/edit', [ProfilesController::class, 'edit'])->name('profile.edit');
Route::get('/profile/{user}/following', [FollowController::class, 'followingPage'])->name('following.page');

Route::patch('/profile/{user}', [ProfilesController::class, 'update'])->name('profile.udpate');

Route::post('/follow/{id}', [FollowController::class, 'follow'])->name('follow');
Route::post('/unfollow/{id}', [FollowController::class, 'unfollow'])->name('unfollow');

Route::middleware('auth')->group(function () {
    Route::get('/chat', [ChatController::class, 'followingList'])->name('chat.list');
    Route::get('/chat/{user}', [ChatController::class, 'chatWithUser'])->name('chat.user');
    Route::get('/chat/messages/{user}', [ChatController::class, 'fetchMessages'])->name('chat.messages');
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
});
