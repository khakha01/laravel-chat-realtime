

@extends('user.layout.layout')
@section('content')
 <!-- Session Status -->
    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="max-w-md mx-auto bg-white p-6 rounded shadow">
        @csrf

        <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>

        <!-- Email -->
        <div class="mb-4">
            <label for="email" class="block mb-1 font-medium">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                class="w-full border rounded px-3 py-2">
            @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-4">
            <label for="password" class="block mb-1 font-medium">Password</label>
            <input id="password" type="password" name="password" required class="w-full border rounded px-3 py-2">
            @error('password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember me -->
        <div class="mb-4 flex items-center">
            <input id="remember" type="checkbox" name="remember" class="mr-2">
            <label for="remember" class="text-sm">Remember me</label>
        </div>

        <div class="flex justify-between items-center">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:underline">
                    Forgot your password?
                </a>
            @endif

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Log in
            </button>
        </div>
    </form>

@endsection

