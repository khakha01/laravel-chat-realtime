<?php

namespace App\Http\Controllers;

use App\Events\UserOffline;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController
{
    public function index()  {
        $users = User::whereKeyNot(Auth::user())->select('id','name','avatar')->get();
        return $users;
    }

    public function offline(Request $request) {
         $user = User::find($request->user_id);

         $user->update([
            'last_seen_at' => now()
        ]);

        broadcast(new UserOffline($user->id));

        return response()->json(['ok' => true]);
    }
}
