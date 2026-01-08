
import { renderChatHeader, updateCurrentChatHeader } from './chatHeader';


/* =======================
   1. GLOBAL STATE
======================= */
const AUTH_USER = window.AUTH_USER; // Được inject từ Blade

let currentChatUser = null;
let echoChannel = null;

// Object lưu trạng thái online/offline của các user (sẽ được cập nhật từ presence hoặc API)
window.USER_STATUS = window.USER_STATUS || {};

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
    userList: () => document.getElementById('user-list'),
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
    dom.chatWith().replaceChildren(renderChatHeader(user));
    dom.messages().innerHTML = '';
}

function appendMessage({ content, user }) {
    const isMine = user.id === AUTH_USER.id;
    const div = document.createElement('div');
    div.className = `message ${isMine ? 'mine' : 'others'}`;
    div.innerHTML = `<strong>${user.name}:</strong> ${content}`;
    dom.messages().appendChild(div);
    dom.messages().scrollTop = dom.messages().scrollHeight;
}
/*
function renderChatHeader(user) {
    const div = document.createElement('div');
    div.className = 'chat-header';

    const status = window.USER_STATUS[user.id];
    let statusText = 'Đang kiểm tra trạng thái...';

    if (status) {
        if (status.online) {
            statusText = '🟢 Đang online';
        } else if (status.last_seen) {
            statusText = `🕒 Hoạt động ${formatLastSeen(status.last_seen)}`;
        } else {
            statusText = '🔴 Offline';
        }
    }

    div.innerHTML = `
        <div class="chat-info">
            <div>
                <img class="chat-avatar" src="${user.avatar ?? '/images/default-avatar.png'}" />
                <div>
                    <span class="chat-name">${user.name}</span><br>
                    <small class="chat-status" data-user-id="${user.id}">${statusText}</small>
                </div>
            </div>
        </div>
        <div class="chat-actions">⋮</div>
    `;
    return div;
}

function updateCurrentChatHeader() {
    if (!currentChatUser) return;

    const statusEl = document.querySelector(`.chat-status[data-user-id="${currentChatUser.id}"]`);
    console.log('gs',statusEl);

    if (!statusEl) return;

    const status = window.USER_STATUS[currentChatUser.id];

    if (!status) {
        statusEl.textContent = 'Đang kiểm tra trạng thái...';
        return;
    }

    if (status.online) {
        statusEl.innerHTML = '🟢 Đang online';
    } else if (status.last_seen) {
        statusEl.innerHTML = `🕒 Hoạt động ${formatLastSeen(status.last_seen)}`;
    } else {
        statusEl.innerHTML = '🔴 Offline';
    }
}

// Helper format thời gian last seen (ví dụ: "5 phút trước")
function formatLastSeen(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);

    if (diffInSeconds < 60) return 'vài giây trước';
    if (diffInSeconds < 3600) return Math.floor(diffInSeconds / 60) + ' phút trước';
    if (diffInSeconds < 86400) return Math.floor(diffInSeconds / 3600) + ' giờ trước';
    return Math.floor(diffInSeconds / 86400) + ' ngày trước';
}
*/
/* =======================
   4. CHAT HELPERS
======================= */
function getChatChannelName(otherUserId) {
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
            const isCurrentChat = fromId === currentChatUser?.id || isMine;

            if (!isCurrentChat) return;

            appendMessage({
                content: e.message.content,
                user: e.user
            });
        })
        .subscribed(() => {
            console.log('Subscribed to channel:', newChannel);
        })
        .error((err) => {
            console.error('Channel error:', err);
        });
}

async function loadHistory(otherUserId) {
    try {
        const response = await fetch(`/messages/history/${otherUserId}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            }
        });

        const data = await response.json();
        dom.messages().innerHTML = '';

        data.forEach(msg => {
            appendMessage({
                content: msg.content,
                user: {
                    id: msg.from_user_id,
                    name: msg.sender.name
                }
            });
        });
    } catch (err) {
        console.error('Lỗi load lịch sử:', err);
    }
}

function openChat(user) {
    currentChatUser = user;

    window.onNewMessageReceivedNotification?.(user.id);

    showChatArea(user);
    subscribeToChat(user.id);
    loadHistory(user.id);
    updateCurrentChatHeader();
}

/* =======================
   5. API SEND MESSAGE
======================= */
async function sendMessage(content) {
    const headers = {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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
   7. INIT USER LIST (giả sử bạn có hàm này ở file khác)
======================= */
// Hàm này sẽ được gọi từ file khác (ví dụ: user-list.js) hoặc inject từ server
window.initUserList = function(options) {
    // Code render danh sách user bên trái
    // Gọi options.onSelectUser(user) khi click
    console.log('initUserList called', options);
};

/* =======================
   8. ONLINE STATUS INIT (sẽ tách riêng sau nếu cần)
======================= */
window.onlineStatus = function() {
    // Ở đây bạn sẽ init Presence Channel hoặc polling last_seen
    console.log('onlineStatus initialized');
};

/* =======================
   9. BOOTSTRAP
======================= */
document.addEventListener('DOMContentLoaded', () => {
    showDefaultMessage();
    initForm();
    onlineStatus();        // Gọi hàm online status
    window.initUserList?.({       // Gọi init danh sách user nếu có
        AUTH_USER,
        onSelectUser: (user) => openChat(user)
    });
});
