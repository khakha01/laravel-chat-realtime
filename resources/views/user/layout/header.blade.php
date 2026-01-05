<header class="app-header">
    <div class="header-container">
        <!-- Logo -->
        <a href="/" class="logo">
            ⚡ Realtime Mini
        </a>

        <!-- Navigation -->
        <nav class="nav">
            <a href="/" class="nav-link active">Home</a>
        </nav>

        <!-- Right actions -->
        <div class="actions">
            @auth
            <a href="{{route('chat')}}" style="text-decoration:none">
                @include('user.components.notify-message')
            </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-logout">Logout</button>
                </form>
            @endauth
        </div>
    </div>
</header>
