export async function initNotify() {
    if (!window.AUTH_USER) return;

    const badge = document.querySelector(".message-notify span");
    if (!badge) return;

    let unreadCount = 0;
    let currentChatUserId = null;
    const unreadUsers = new Set();

    /* =======================
       🔊 SOUND SETUP
    ======================= */
    const notifySound = new Audio("/sounds/notify-message.mp3");
    let audioUnlocked = false;

    const unlockAudio = () => {
        if (audioUnlocked) return;

        notifySound
            .play()
            .then(() => {
                notifySound.pause();
                notifySound.currentTime = 0;
                audioUnlocked = true;
            })
            .catch(() => {});

        document.removeEventListener("click", unlockAudio);
        document.removeEventListener("keydown", unlockAudio);
    };

    document.addEventListener("click", unlockAudio);
    document.addEventListener("keydown", unlockAudio);

    const updateBadge = () => {
        badge.style.display = unreadCount > 0 ? "inline-block" : "none";
        badge.innerText = unreadCount;
    };

    const loadUnreadCount = async () => {
        try {
            const res = await fetch("/messages/unread-total");
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const data = await res.json();
            unreadCount = data.count;
            updateBadge();
        } catch (err) {
            console.error("Load unread count failed:", err);
        }
    };

    // Load lần đầu
    await loadUnreadCount();

    // Realtime listener
    Echo.private(`notify.${window.AUTH_USER.id}`).listen(
        ".MessageSent",
        (e) => {
            const fromUserId = e.user.id;

            if (currentChatUserId === fromUserId) return;
            if (unreadUsers.has(fromUserId)) return;

            unreadUsers.add(fromUserId);
            unreadCount++;
            updateBadge();

            if (audioUnlocked) {
                notifySound.currentTime = 0;
                notifySound.play().catch(() => {});
            }
        }
    );

    // Khi mở chat
    window.onNewMessageReceivedNotification = async function (userId) {
        currentChatUserId = userId;

        try {
            const res = await fetch(`/messages/read/${userId}`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                },
            });

            if (!res.ok) throw new Error(`HTTP ${res.status}`);
        } catch (err) {
            console.error("Mark as read failed:", err);
            // Có thể thông báo cho user nếu muốn
        }

        // Dù mark as read thất bại thì vẫn xóa khỏi set local để UI không bị lệch
        unreadUsers.delete(userId);

        // Reload count chính xác từ server
        await loadUnreadCount();
    };
}
