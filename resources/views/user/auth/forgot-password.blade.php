

@extends('user.layout.layout')
@section('content')
   <div class="mb-4 text-sm text-gray-600">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="max-w-md mx-auto bg-white p-6 rounded shadow">
        @csrf

        <h2 class="text-2xl font-bold mb-6 text-center">
            Forgot Password
        </h2>

        <!-- Email -->
        <div class="mb-4">
            <label for="email" class="block mb-1 font-medium">
                Email
            </label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                class="w-full border rounded px-3 py-2">

            @error('email')
                <p class="text-red-500 text-sm mt-1">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Email Password Reset Link
            </button>
        </div>
    </form>


@endsection
