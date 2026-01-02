<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use Illuminate\Http\Request;

class MessageController
{

    public function index()  {
        return view('chat');
    }

    public function chatMessage(Request $request)
    {
        $message = $request->input('message');

        broadcast(new MessageSent($message, auth()->user()))->toOthers();

        return response()->json(['status' => 'success']);
    }
}
