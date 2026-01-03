<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController
{

    public function index()  {
        return view('user.chat');
    }

    public function chatMessage(Request $request)
    {
        $message = $request->input('message');
        $user = Auth::user();
        broadcast(new MessageSent($message, $user))->toOthers();

        return response()->json(['status' => 'success']);
    }


}
