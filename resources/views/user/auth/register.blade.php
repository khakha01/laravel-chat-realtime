
@extends('user.layout.layout')
@section('content')
   <form method="POST" action="{{ route('register') }}" class="max-w-md mx-auto bg-white p-6 rounded shadow">
        @csrf

        <h2 class="text-2xl font-bold mb-6 text-center">Register</h2>

        <!-- Name -->
        <div class="mb-4">
            <label for="name" class="block mb-1 font-medium">Name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                class="w-full border rounded px-3 py-2">
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email -->
        <div class="mb-4">
            <label for="email" class="block mb-1 font-medium">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required
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

        <!-- Confirm Password -->
        <div class="mb-6">
            <label for="password_confirmation" class="block mb-1 font-medium">
                Confirm Password
            </label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                class="w-full border rounded px-3 py-2">
        </div>

        <div class="flex justify-between items-center">
            <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:underline">
                Already registered?
            </a>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Register
            </button>
        </div>
    </form>
@endsection
