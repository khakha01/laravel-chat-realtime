@extends('user.layout.layout')

@section('content')
<section class="home">
    <!-- Hero -->
    <div class="hero">
        <h1>⚡ Realtime Mini System</h1>
        <p>
            Hệ thống demo realtime sử dụng <strong>Laravel Reverb</strong> & WebSocket
        </p>

        @auth
            <a href="/chat" class="btn-primary">Vào phòng chat</a>
        @else
            <div class="hero-actions">
                <a href="{{ route('login') }}" class="btn-primary">Đăng nhập</a>
                <a href="{{ route('register') }}" class="btn-outline">Đăng ký</a>
            </div>
        @endauth
    </div>

    <!-- Features -->
    <div class="features">
        <div class="feature-card">
            <h3>⚡ Realtime</h3>
            <p>Gửi & nhận dữ liệu ngay lập tức qua WebSocket</p>
        </div>

        <div class="feature-card">
            <h3>🔒 Secure</h3>
            <p>Private channel, auth, CSRF, socket ID</p>
        </div>

        <div class="feature-card">
            <h3>🚀 Scalable</h3>
            <p>Dễ mở rộng chat room, notification, presence</p>
        </div>
    </div>

    <!-- Info -->
    <div class="info">
        <h2>Kiến trúc hệ thống</h2>
        <p>
            Frontend sử dụng <strong>Laravel Echo</strong>,
            Backend broadcast event qua <strong>Laravel Reverb</strong>.
        </p>
    </div>
</section>
@endsection
