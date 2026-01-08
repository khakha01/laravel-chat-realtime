// chat/history.js
export async function loadHistory(otherUserId, appendMessage) {
    try {
        const response = await fetch(`/messages/history/${otherUserId}`, {
            headers: {
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
                Accept: "application/json",
            },
        });

        const data = await response.json();

        const messagesContainer = document.getElementById("messages");
        messagesContainer.innerHTML = "";

        data.forEach((msg) => {
            appendMessage({
                content: msg.content,
                user: {
                    id: msg.from_user_id,
                    name: msg.sender.name,
                },
            });
        });

        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    } catch (err) {
        console.error("Lỗi load lịch sử:", err);
    }
}
