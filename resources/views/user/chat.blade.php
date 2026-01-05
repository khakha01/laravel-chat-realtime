@extends('user.layout.layout')
@section('content')
    <div class="mt-5">
        <div class="row">
            <div class="col-md-3">
                <div class="user-lits">
                    <div class="d-flex justify-content-between">
                        <h4>Đoạn chat</h4>
                    </div>
                    <div id="user-list" class="main-user-list"></div>
                </div>
            </div>
            <div class="col-md-9 main-chat">
                <div id="chat-area">
                    <!-- Mặc định hiển thị thông báo này -->
                    <div id="default-message" class="text-center mt-5">
                        <h5>Vui lòng chọn một người dùng từ danh sách để bắt đầu trò chuyện</h5>
                    </div>

                    <!-- Khu vực chat sẽ hiển thị khi đã chọn user -->
                    <div id="chat-container" style="display: none;">
                        <h5 id="chat-with"></h5>
                        <div id="messages"></div>
                        <form id="chat-form">
                            <input type="text" name="message" placeholder="Nhập tin nhắn..." required autocomplete="off">
                            <button type="submit">Send</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>



    <script>
        window.AUTH_USER = @json(auth()->user());
    </script>

    <script>
        /* =======================
                           1. GLOBAL STATE
                        ======================= */
        const AUTH_USER = window.AUTH_USER;

        let currentChatUser = null;
        let echoChannel = null;

        /* =======================
           2. DOM HELPERS
        ======================= */
        const dom = {
            messages: () => document.getElementById('messages'),
            form: () => document.getElementById('chat-form'),
            input: () => document.querySelector('#chat-form input[name="message"]'),
            defaultMessage: () => document.getElementById('default-message'),
            chatContainer: () => document.getElementById('chat-container'),
            chatWith: () => document.querySelector('#chat-with'),
        };

        /* =======================
           3. UI HELPERS
        ======================= */
        function showDefaultMessage() {
            dom.defaultMessage().style.display = 'block';
            dom.chatContainer().style.display = 'none';
            dom.messages().innerHTML = '';
        }

        function showChatArea(user) {
            dom.defaultMessage().style.display = 'none';
            dom.chatContainer().style.display = 'block';
            dom.chatWith().replaceChildren(
                renderChatHeader(user)
            );
            dom.messages().innerHTML = '';
        }

        function appendMessage({
            content,
            user
        }) {
            const isMine = user.id === AUTH_USER.id;

            const div = document.createElement('div');
            div.className = `message ${isMine ? 'mine' : 'others'}`;
            div.innerHTML = `<strong>${user.name}:</strong> ${content}`;

            dom.messages().appendChild(div);
            dom.messages().scrollTop = dom.messages().scrollHeight;
        }

        function renderChatHeader(user) {
            const div = document.createElement('div');
            div.className = 'chat-header';
            div.innerHTML = `
                <div class="chat-info">
                    <div>
                    <img class="chat-avatar"
                    src="${user.avatar ?? '/images/default-avatar.png'}" />
                    <span class="chat-name">${user.name}</span>
                    </div>
                </div>

                <div class="chat-actions">⋮</div>
            `;
            return div;
        }
        /* =======================
           4. CHAT HELPERS
        ======================= */
        function getChatChannelName(
            otherUserId) { // Việc sort id này đảm bảo client và server luôn dùng cùng tên channel, tránh mâu thuẫn thứ tự.
            const ids = [AUTH_USER.id, otherUserId].sort((a, b) => a - b);
            return `chat.${ids[0]}.${ids[1]}`;
        }

        function subscribeToChat(otherUserId) {
            const newChannel = getChatChannelName(otherUserId);

            if (echoChannel) {
                Echo.leave(echoChannel.name);
                echoChannel = null;
            }

            echoChannel = Echo.private(newChannel);

            echoChannel
                .listen('.MessageSent', (e) => {
                    const fromId = e.user.id;
                    const isMine = fromId === AUTH_USER.id;
                    const isCurrentChat = fromId === currentChatUser.id || isMine;
                    if (!isCurrentChat) return;
                    appendMessage({
                        content: e.message.content,
                        user: e.user
                    });
                })
                .subscribed(() => {
                    console.log('Subscribed:', newChannel);
                })
                .error(err => {
                    console.error('Channel error:', err);
                });
        }

        async function loadHistory(otherUserId) {
            try {
                const response = await fetch(`/messages/history/${otherUserId}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content'),
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();

                dom.messages().innerHTML = ''; // clear trước

                data.forEach(msg => {
                    appendMessage({
                        content: msg.content,
                        user: {
                            id: msg.from_user_id,
                            name: msg.sender.name // giả sử bạn with('sender')
                        }
                    });
                });
            } catch (err) {
                console.error('Lỗi load lịch sử:', err);
            }
        }

        function openChat(user) {
            currentChatUser = user;

            // khi nhận được thông báo tin nhắn mới
            window.onNewMessageReceivedNotification?.(user.id);

            showChatArea(user); // hiển thị khu vực chat
            subscribeToChat(user.id); // subscribe channel realtime

            loadHistory(user.id);
        }

        /* =======================
           5. API
        ======================= */
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

            await fetch('/messages/send-message', {
                method: 'POST',
                headers,
                body: JSON.stringify({
                    message: content,
                    to_user_id: currentChatUser.id
                }),
            });
        }

        /* =======================
           6. EVENT HANDLERS
        ======================= */
        function initForm() {
            dom.form().addEventListener('submit', async (e) => {
                e.preventDefault();

                const msg = dom.input().value.trim();
                if (!msg || !currentChatUser) return;

                appendMessage({
                    content: msg,
                    user: AUTH_USER
                });

                try {
                    await sendMessage(msg);
                } catch {
                    alert('Lỗi gửi tin nhắn!');
                }

                dom.input().value = '';
            });
        }

        /* =======================
           7. BOOTSTRAP
        ======================= */

        document.addEventListener('DOMContentLoaded', () => {
            showDefaultMessage();
            initForm();
            initUserList({
                AUTH_USER,
                onSelectUser: (user) => {
                    openChat(user);
                }
            });
        });
    </script>
@endsection
