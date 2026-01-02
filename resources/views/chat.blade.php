<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Realtime Chat với Laravel Reverb</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f0f2f5;
        }

        #messages {
            height: 200px;
            overflow-y: auto;
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        #messages::after {
            content: '';
            display: table;
            clear: both;
        }

        .message {
            margin: 8px 0;
            padding: 10px 15px;
            border-radius: 20px;
            max-width: 70%;
            word-wrap: break-word;
            clear: both;
            animation: fadeIn 0.3s;
        }

        .mine {
            background: #007bff;
            color: white;
            float: right;
            border-bottom-right-radius: 5px;
        }

        .others {
            background: #e9ecef;
            color: #333;
            float: left;
            border-bottom-left-radius: 5px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        form {
            display: flex;
            gap: 10px;
        }

        input[type="text"] {
            flex: 1;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 30px;
            font-size: 16px;
        }

        button {
            padding: 12px 24px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background: #0056b3;
        }
    </style>
</head>

<body>
    <h1 style="text-align:center; color:#333;">Realtime Chat Laravel Reverb</h1>
    <div id="messages"></div>

    <form id="chat-form">
        <input type="text" name="message" placeholder="Nhập tin nhắn..." required autocomplete="off">
        <button type="submit">Send</button>
    </form>

    <script src="https://js.pusher.com/8.2/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.min.js"></script>

    <script>
        Pusher.logToConsole = true; // Tắt sau khi debug xong

        window.Pusher = Pusher;

        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: "{{ env('REVERB_APP_KEY') }}",
            wsHost: window.location.hostname,
            wsPort: {{ env('REVERB_PORT') ?? 6001 }},
            wssPort: {{ env('REVERB_PORT') ?? 6001 }},
            forceTLS: false,
            enabledTransports: ['ws'],
        });

        const messagesEl = document.getElementById('messages');
        const form = document.getElementById('chat-form');
        const input = form.querySelector('input[name="message"]');

        function appendMessage(content, sender = 'Guest', isMine = false) {
            const div = document.createElement('div');
            div.className = 'message ' + (isMine ? 'mine' : 'others');
            div.innerHTML = `<strong>${isMine ? 'Bạn' : sender}:</strong> ${content}`;
            messagesEl.appendChild(div);
            messagesEl.scrollTop = messagesEl.scrollHeight;
        }

        // CHỈ 1 LẦN LISTEN DUY NHẤT – fix duplicate
    Echo.channel('chat').listen('.MessageSent', (e) => {if (e.user?.name === 'Bạn') return;
        const content = e?.message?.content || '';
        const sender = e?.user?.name || 'Guest';
        appendMessage(content, sender, false);
    });
        // Gửi tin nhắn
        form.onsubmit = async function(e) {
            e.preventDefault();
            const msg = input.value.trim();
            if (!msg) return;
            // Optimistic: hiện ngay tin nhắn của mình
            appendMessage(msg, 'Bạn', true);

            try {
                const headers = {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                };

                // Include socket id so server can exclude sender when broadcasting with ->toOthers()
                try {
                    if (window.Echo && typeof window.Echo.socketId === 'function') {
                        const sid = window.Echo.socketId();
                        if (sid) headers['X-Socket-Id'] = sid;
                    }
                } catch (e) {
                    // ignore
                }

                await fetch('/send-message', {
                    method: 'POST',
                    headers,
                    body: JSON.stringify({ message: msg })
                });
            } catch (err) {
                alert('Lỗi gửi tin nhắn!');
            }

            input.value = '';
        };
    </script>
</body>

</html>
