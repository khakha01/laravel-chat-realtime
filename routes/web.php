<?php

use App\Events\MessageSent;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfileController;
use App\Models\Message;
use Illuminate\Support\Facades\Route;

Route::get('/', function(){
    return view('user.layout.home');
})->name('/');

Route::middleware('auth')->group(function () {

    Route::get('/chat', [MessageController::class, 'index']);
    Route::post('/send-message', [MessageController::class, 'chatMessage']);
});


require __DIR__ . '/auth.php';
