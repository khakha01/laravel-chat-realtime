


@extends('user.layout.layout')
@section('content')
  <form method="POST" action="{{ route('password.store') }}"
      class="max-w-md mx-auto bg-white p-6 rounded shadow">
    @csrf

    <!-- Token reset -->
    <input type="hidden" name="token" value="{{ $request->route('token') }}">

    <h2 class="text-2xl font-bold mb-6 text-center">
        Reset Password
    </h2>

    <!-- Email -->
    <div class="mb-4">
        <label for="email" class="block mb-1 font-medium">
            Email
        </label>
        <input
            id="email"
            type="email"
            name="email"
            value="{{ old('email', $request->email) }}"
            required
            autofocus
            autocomplete="username"
            class="w-full border rounded px-3 py-2"
        >

        @error('email')
            <p class="text-red-500 text-sm mt-1">
                {{ $message }}
            </p>
        @enderror
    </div>

    <!-- Password -->
    <div class="mb-4">
        <label for="password" class="block mb-1 font-medium">
            New Password
        </label>
        <input
            id="password"
            type="password"
            name="password"
            required
            autocomplete="new-password"
            class="w-full border rounded px-3 py-2"
        >

        @error('password')
            <p class="text-red-500 text-sm mt-1">
                {{ $message }}
            </p>
        @enderror
    </div>

    <!-- Confirm Password -->
    <div class="mb-6">
        <label for="password_confirmation" class="block mb-1 font-medium">
            Confirm Password
        </label>
        <input
            id="password_confirmation"
            type="password"
            name="password_confirmation"
            required
            autocomplete="new-password"
            class="w-full border rounded px-3 py-2"
        >

        @error('password_confirmation')
            <p class="text-red-500 text-sm mt-1">
                {{ $message }}
            </p>
        @enderror
    </div>

    <div class="flex justify-end">
        <button
            type="submit"
            class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
        >
            Reset Password
        </button>
    </div>
</form>


@endsection
