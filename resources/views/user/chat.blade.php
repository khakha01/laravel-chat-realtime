



@extends('user.layout.layout')
@section('content')
    <div class="mt-5">
        <div id="messages"></div>

    <form id="chat-form">
        <input type="text" name="message" placeholder="Nhập tin nhắn..." required autocomplete="off">
        <button type="submit">Send</button>
    </form>

    </div>
    {{-- <script src="https://js.pusher.com/8.2/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.min.js"></script> --}}

    {{-- npm run dev --}}
    @vite(['resources/js/app.js'])
    <script>
        window.AUTH_USER = @json(auth()->user());
    </script>

    <script>
        // ===== Realtime config =====

    </script>

    <script>
        // ===== DOM elements =====
        function getDom() {
            return {
                messagesEl: document.getElementById('messages'),
                form: document.getElementById('chat-form'),
                input: document.querySelector('#chat-form input[name="message"]'),
            };
        }

        // ===== UI =====
        function appendMessage({
            content,
            user
        }) {
            const {
                messagesEl
            } = getDom();
            const isMine = user.id === window.AUTH_USER.id;

            const div = document.createElement('div');
            div.className = `message ${isMine ? 'mine' : 'others'}`;
            div.innerHTML = `<strong>${user.name}:</strong> ${content}`;

            messagesEl.appendChild(div);
            messagesEl.scrollTop = messagesEl.scrollHeight;
        }

        // ===== Realtime listener =====
        function listenChat() {
            Echo.private('chat')
                .listen('.MessageSent', (e) => {
                    appendMessage({
                        content: e.message.content,
                        user: e.user
                    });
                });
        }

        // ===== Send message =====
        async function sendMessage(content) {
            const headers = {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute('content'),
            };

            if (window.Echo?.socketId) {
                headers['X-Socket-Id'] = window.Echo.socketId();
            }

            await fetch('/send-message', {
                method: 'POST',
                headers,
                body: JSON.stringify({
                    message: content
                }),
            });
        }
    </script>

    <script>
        // ===== Form handler =====
        function initForm() {
            const {
                form,
                input
            } = getDom();

            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                const msg = input.value.trim();
                if (!msg) return;

                // Optimistic UI
                appendMessage({
                    content: msg,
                    user: window.AUTH_USER
                });

                try {
                    await sendMessage(msg);
                } catch (err) {
                    alert('Lỗi gửi tin nhắn!');
                }

                input.value = '';
            });
        }

        // ===== Bootstrap =====
        document.addEventListener('DOMContentLoaded', () => {
            listenChat();
            initForm();
        });
    </script>

@endsection
