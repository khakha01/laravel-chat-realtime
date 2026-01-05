export function initUserList({ AUTH_USER, onSelectUser } = {}) {
    const userListEl = document.getElementById('user-list');
    if (!userListEl || !AUTH_USER || typeof onSelectUser !== 'function') return;

    // Tạo item user (gọn + escape HTML an toàn hơn nếu cần sau này)
    const createUserItem = (user) => {
        const div = document.createElement('div');
        div.className = 'user-item';

        const avatarUrl = user.avatar || 'https://www.gravatar.com/avatar/?d=mp&s=200';

        div.innerHTML = `
            <img class="avatar" src="${avatarUrl}" alt="${user.name}">
            <span class="name">${user.name}</span>
        `;

        div.onclick = () => onSelectUser(user);

        return div;
    };

    // Load danh sách user từ server
    const loadUsers = async () => {
        try {
            const res = await fetch('/users');

            if (!res.ok) throw new Error(`HTTP ${res.status}`);

            const users = await res.json();

            // Xóa danh sách cũ
            userListEl.innerHTML = '';

            // Render từng user (loại chính mình)
            users.forEach(user => {
                if (user.id === AUTH_USER.id) return;
                userListEl.appendChild(createUserItem(user));
            });
        } catch (err) {
            console.error('Load user list failed:', err);
            userListEl.innerHTML = '<div class="error">Không tải được danh sách người dùng</div>';
        }
    };

    // Khởi chạy lần đầu
    loadUsers();
}

// Export ra window để dùng ở nơi khác (nếu cần)
window.initUserList = initUserList;
