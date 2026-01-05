
@extends('user.layout.layout')
@section('content')
   <form method="POST" action="{{ route('register') }}" class="auth-card d-block">
    @csrf

    <h2 class="auth-title">Create Account</h2>
    <p class="auth-subtitle">Đăng ký để sử dụng hệ thống realtime</p>

    <!-- Name -->
    <div class="form-group">
        <label for="name">Name</label>
        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>
        @error('name')
            <small class="error">{{ $message }}</small>
        @enderror
    </div>

    <!-- Email -->
    <div class="form-group">
        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required>
        @error('email')
            <small class="error">{{ $message }}</small>
        @enderror
    </div>

    <!-- Password -->
    <div class="form-group">
        <label for="password">Password</label>
        <input id="password" type="password" name="password" required>
        @error('password')
            <small class="error">{{ $message }}</small>
        @enderror
    </div>

    <!-- Confirm -->
    <div class="form-group">
        <label for="password_confirmation">Confirm Password</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required>
    </div>

    <button type="submit" class="btn-auth">
        Register
    </button>

    <div class="auth-footer">
        Already have an account?
        <a href="{{ route('login') }}">Login</a>
    </div>
</form>


@endsection
