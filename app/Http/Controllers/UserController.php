<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController
{
    public function index()  {
        $users = User::whereKeyNot(Auth::user())->select('id','name','avatar')->get();
        return $users;
    }
}
