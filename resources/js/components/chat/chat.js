import { subscribeToChat } from "./channel";
import { renderChatHeader, updateCurrentChatHeader } from "./chatHeader";
import { loadHistory } from "./chatHistory";
import { onlineStatus } from "./online-status";


const AUTH_USER = window.AUTH_USER; // Được inject từ Blade

let currentChatUser = null;

window.USER_STATUS = window.USER_STATUS || {};


const dom = {
    messages: () => document.getElementById("messages"),
    form: () => document.getElementById("chat-form"),
    input: () => document.querySelector('#chat-form input[name="message"]'),
    defaultMessage: () => document.getElementById("default-message"),
    chatContainer: () => document.getElementById("chat-container"),
    chatWith: () => document.querySelector("#chat-with"),
    userList: () => document.getElementById("user-list"),
};


function showDefaultMessage() {
    dom.defaultMessage().style.display = "block";
    dom.chatContainer().style.display = "none";
    dom.messages().innerHTML = "";
}

function showChatArea(user) {
    dom.defaultMessage().style.display = "none";
    dom.chatContainer().style.display = "block";
    dom.chatWith().replaceChildren(renderChatHeader(user));
    dom.messages().innerHTML = "";
}

function appendMessage({ content, user }) {
    const isMine = user.id === AUTH_USER.id;
    const div = document.createElement("div");
    div.className = `message ${isMine ? "mine" : "others"}`;
    div.innerHTML = `<strong>${user.name}:</strong> ${content}`;
    dom.messages().appendChild(div);
    dom.messages().scrollTop = dom.messages().scrollHeight;
}



async function openChat(user) {
    currentChatUser = user;
    window.currentChatUser = user;
    window.onNewMessageReceivedNotification?.(user.id);

    showChatArea(user);
    await loadHistory(user.id, appendMessage);
    subscribeToChat(AUTH_USER.id, user.id, (message, sender) => {
        appendMessage({
            content: message.content,
            user: sender,
        });
    });

    updateCurrentChatHeader(user.id);
}


async function sendMessage(content) {
    const headers = {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content"),
    };

    if (window.Echo?.socketId) {
        headers["X-Socket-Id"] = window.Echo.socketId();
    }

    await fetch("/messages/send-message", {
        method: "POST",
        headers,
        body: JSON.stringify({
            message: content,
            to_user_id: currentChatUser.id,
        }),
    });
}

function initForm() {
    dom.form().addEventListener("submit", async (e) => {
        e.preventDefault();

        const msg = dom.input().value.trim();
        if (!msg || !currentChatUser) return;

        appendMessage({
            content: msg,
            user: AUTH_USER,
        });

        try {
            await sendMessage(msg);
        } catch {
            alert("Lỗi gửi tin nhắn!");
        }

        dom.input().value = "";
    });
}

document.addEventListener("DOMContentLoaded", () => {
    showDefaultMessage();
    initForm();
    onlineStatus();
    window.initUserList?.({
        AUTH_USER,
        onSelectUser: (user) => openChat(user),
    });
});
