@extends('user.layout.layout')

@section('content')

@if (session('status'))
    <div class="alert-success">
        {{ session('status') }}
    </div>
@endif

<form method="POST" action="{{ route('login') }}" class="auth-card d-block">
    @csrf

    <h2 class="auth-title">Welcome Back</h2>
    <p class="auth-subtitle">Đăng nhập để tiếp tục</p>

    <!-- Email -->
    <div class="form-group">
        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
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

    <!-- Remember -->
    <div class="form-options">
        <label class="remember">
            <input type="checkbox" name="remember">
            Remember me
        </label>

        @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="link">
                Forgot password?
            </a>
        @endif
    </div>

    <button type="submit" class="btn-auth">
        Log in
    </button>

    <div class="auth-footer">
        Don’t have an account?
        <a href="{{ route('register') }}">Register</a>
    </div>
</form>

@endsection
