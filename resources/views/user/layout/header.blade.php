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
                <a href="{{ route('chat') }}" style="text-decoration:none">
                    @include('user.components.notify-message')
                </a>
                <div class="dropdown">
                    <button class="btn btn-light rounded-circle no-caret p-2 ms-2" type="button" id="dropdownMenuButton"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-gear"></i>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>

            @endauth
        </div>
    </div>
</header>
