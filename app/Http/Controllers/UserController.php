<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Support\Facades\Auth;

class UserController
{
    public function __construct(protected UserService $userService) {}
    
    public function index()
    {
        return $this->userService->getAllUsers(Auth::user());
    }

    public function offline()
    {
        $this->userService->markUserOffline(Auth::user());
        
        return response()->json(['ok' => true]);
    }
}
