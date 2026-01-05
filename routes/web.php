<?php

use App\Events\MessageSent;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Models\Message;
use Illuminate\Support\Facades\Route;

Route::get('/', function(){
    return view('user.layout.home');
})->name('/');

Route::middleware('auth')->group(function () {

    Route::get('/chat', [MessageController::class, 'index'])->name('chat');

    // MESSAGE GROUP
    Route::prefix('messages')->group(function () {

        // send message
        Route::post('/send-message', [MessageController::class, 'chatMessage']);

        Route::get('/history/{userId}', [MessageController::class, 'history']);

        // total unread messages
        Route::get('/unread-total', [MessageController::class, 'getUnreadTotal']);

        // mark read / unread count with user
        Route::post('/read/{user}', [MessageController::class, 'markAsRead']);
    });


    Route::get('/users', [UserController::class, 'index']);
});


require __DIR__ . '/auth.php';
