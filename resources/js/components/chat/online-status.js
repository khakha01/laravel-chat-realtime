import { updateCurrentChatHeader } from "./chatHeader";

window.USER_STATUS = window.USER_STATUS || {};

export function onlineStatus() {
    window.Echo.join("online")
        .here((users) => {
            users.forEach((u) => {
                USER_STATUS[u.id] = { online: true, last_seen: null };
            });
            if (window.currentChatUser) {
                updateCurrentChatHeader(window.currentChatUser.id);
            }
        })
        .joining((user) => {
            USER_STATUS[user.id] = { online: true, last_seen: null };
            if (window.currentChatUser?.id === user.id) {
                updateCurrentChatHeader(user.id);
            }
        })
        .leaving((user) => {
            USER_STATUS[user.id] = { online: false, last_seen: new Date() };
            if (window.currentChatUser?.id === user.id) {
                updateCurrentChatHeader(user.id);
            }
        });
};
