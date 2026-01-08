// chat/header.js

export function renderChatHeader(user) {
    const status = window.USER_STATUS[user.id] ;
    let statusText = "Đang kiểm tra trạng thái...";

    if (status.online) {
        statusText = "Đang online";
    } else if (status.last_seen) {
        statusText = `Hoạt động ${formatLastSeen(status.last_seen)}`;
    } else if (status.last_seen === null) {
        statusText = "Offline";
    }

    const div = document.createElement("div");
    div.className = "chat-header";
    div.innerHTML = `
        <div class="chat-info">
            <div>
                <img class="chat-avatar" src="${
                    user.avatar ?? "/images/default-avatar.png"
                }" alt="${user.name}" />
                <div>
                    <span class="chat-name">${user.name}</span><br>
                    <small class="chat-status" data-user-id="${
                        user.id
                    }">${statusText}</small>
                </div>
            </div>
        </div>
        <div class="chat-actions">⋮</div>
    `;
    return div;
}

export function updateCurrentChatHeader(userId) {
    if (!window.currentChatUser) return;
    if (window.currentChatUser.id !== userId) return;

    const statusEl = document.querySelector(
        `.chat-status[data-user-id="${userId}"]`
    );
    if (!statusEl) return;

    const status = window.USER_STATUS[userId] || {};

    if (status.online) {
        statusEl.textContent = "🟢 Đang online";
    } else if (status.last_seen) {
        statusEl.textContent = `🕒 Hoạt động ${formatLastSeen(status.last_seen)}`;
    } else {
        statusEl.textContent = "🔴 Offline";
    }
}

// Helper format thời gian last seen (ví dụ: "5 phút trước")
function formatLastSeen(dateString) {
    if (!dateString) return "Offline";

    const date = new Date(dateString);
    if (isNaN(date.getTime())) return "Offline";

    const now = new Date();
    const diffMs = now - date;
    const diffSec = Math.floor(diffMs / 1000);

    if (diffSec < 60) return "vài giây trước";
    if (diffSec < 3600) return `${Math.floor(diffSec / 60)} phút trước`;
    if (diffSec < 86400) return `${Math.floor(diffSec / 3600)} giờ trước`;
    if (diffSec < 604800) return `${Math.floor(diffSec / 86400)} ngày trước`;

    // Nếu lâu quá → chỉ hiện ngày
    return date.toLocaleDateString("vi-VN", {
        day: "numeric",
        month: "short",
        year: "numeric",
    });
}
