# 📱 Luồng Hoạt Động Hệ Thống Chat Real-time (Laravel Reverb)

## 🎯 Tổng Quan

Đây là ứng dụng chat 1-1 real-time sử dụng **Laravel Reverb** (WebSocket server) để phát sóng tin nhắn tức thời. Kiến trúc chia thành 2 phần chính:
- **Backend**: Laravel (HTTP APIs + Event Broadcasting)
- **Frontend**: Vanilla JavaScript + Alpine.js (WebSocket listener)

---

## 📊 Quy Trình Tổng Quát

```
┌─────────────────┐
│  User A Gửi    │
│   Tin Nhắn      │
└────────┬────────┘
         │
         ▼
┌─────────────────────────────────────────┐
│  Frontend - chat.blade.php              │
│  - Form submit listener                 │
│  - Gọi API /messages/send-message       │
└────────┬────────────────────────────────┘
         │
         ▼
┌─────────────────────────────────────────┐
│  Backend - MessageController            │
│  - Xác thực request (StoreChatMessageReq)
│  - Gọi ChatService.sendMessage()        │
└────────┬────────────────────────────────┘
         │
         ▼
┌─────────────────────────────────────────┐
│  ChatService - sendMessage()            │
│  - Tạo Message object                   │
│  - DB transaction:                      │
│    1. Lưu tin nhắn vào DB               │
│    2. Dispatch MessageSent event        │
└────────┬────────────────────────────────┘
         │
         ▼
┌─────────────────────────────────────────┐
│  MessageSent Event                      │
│  - Phát qua 2 channels:                 │
│    1. chat.{userA_id}.{userB_id}       │
│    2. notify.{userB_id}                 │
│  (được sắp xếp ID để tránh lộn xộn)    │
└────────┬────────────────────────────────┘
         │
         ▼
┌──────────────────────────────────────────┐
│  Laravel Reverb Server (WebSocket)       │
│  - Nhận event từ Redis (scaling)         │
│  - Phát sóng đến clients đã subscribe    │
│  - Gửi payload qua 2 channels            │
└────────┬─────────────────────────────────┘
         │
         ├─────────────────┬─────────────────┐
         ▼                 ▼                 ▼
    (User A)          (User B)          (User B - notify)
    - Nhận tin nhắn    - Nhận tin nhắn    - Nhận thông báo
      trên channel      trên channel       (không trong chat)
      chat             chat
    - Hiển thị TN       - Hiển thị TN      - Cộng badge
      (gửi bởi A)       (gửi bởi A)        - Phát âm thanh
    - User A thấy      - User B thấy
      tin nhắn          tin nhắn
      của mình          từ A
         │                 │                 │
         └─────────────────┴─────────────────┘
                   (Realtime Update)
```

---

## 🔄 Chi Tiết Các Bước

### **1️⃣ Frontend - Gọi API Gửi Tin Nhắn**

**File**: `resources/views/user/chat.blade.php`

```
User nhập tin nhắn và nhấn "Send"
    ↓
Form submit event listener
    ↓
Kiểm tra: tin nhắn không trống + có user đã chọn
    ↓
Gọi function sendMessage(content)
    ↓
POST /messages/send-message
Headers:
  - Content-Type: application/json
  - X-CSRF-TOKEN: (CSRF protection)
  - X-Socket-Id: (Pusher feature - để loại bỏ duplicate)
Body:
{
  "message": "Nội dung tin nhắn",
  "to_user_id": 2
}
    ↓
Immediately append message vào DOM (Optimistic Update)
    ↓
Clear input field
```

---

### **2️⃣ Backend - MessageController**

**File**: `app/Http/Controllers/MessageController.php`

#### **Endpoint: POST /messages/send-message**

```
Nhận request (StoreChatMessageRequest)
    ↓
Validate:
  - message: required|string|max:1000
  - to_user_id: required|exists:users,id
    ↓
Lấy authenticated user ($fromUser = Auth::user())
    ↓
Gọi $chatService->sendMessage($fromUser, $toUserId, $message)
    ↓
Trả về JSON response: { "status": "success" }
```

---

### **3️⃣ ChatService - Xử Lý Logic**

**File**: `app/Services/ChatService.php`

#### **Function: sendMessage()**

```
Tạo Message object:
  Message::make($fromUser->id, $toUserId, $content)
    ↓
DB Transaction:
  ├─ Step 1: Lưu tin nhắn vào DB
  │   $messageRepository->save($message)
  │   (Gọi MessageRepository.save())
  │
  └─ Step 2: Dispatch Broadcasting Event
      broadcast(new MessageSent(
        $content,
        $fromUser,
        $fromUser->id,
        $toUserId
      ))->toOthers()
      
      .toOthers() = không gửi lại cho chính người gửi
    ↓
Return Message object
```

---

### **4️⃣ MessageRepository - Lưu Dữ Liệu**

**File**: `app/Repositories/Message/MessageRepository.php`

#### **Function: save()**

```
Message::save()
    ↓
INSERT INTO messages (
  from_user_id,
  to_user_id,
  content,
  is_read = false,
  created_at,
  updated_at
) VALUES (...)
```

**Database Structure**:
```sql
messages
├── id (Primary Key)
├── from_user_id (Foreign Key → users)
├── to_user_id (Foreign Key → users)
├── content (Text)
├── is_read (Boolean, default: false)
├── created_at (Timestamp)
└── updated_at (Timestamp)
```

---

### **5️⃣ MessageSent Event - Phát Sóng**

**File**: `app/Events/MessageSent.php`

#### **Khái Niệm**

Event này implement `ShouldBroadcastNow` (phát sóng ngay lập tức, không vào queue)

#### **Function: broadcastOn()**

```
Sắp xếp từ_id và tới_id (để tránh mâu thuẫn thứ tự)
    ↓
Phát trên 2 channels (private):

┌─ Channel 1: chat.{userA_id}.{userB_id}
│  Dùng cho: Hai người đang chat 1-1
│  AI & B đều có quyền truy cập
│  → Cả A và B thấy tin nhắn realtime trong chat
│
└─ Channel 2: notify.{toUserId}
   Dùng cho: Thông báo cho người nhận
   → User nhận được thông báo (+ cộng badge + phát âm thanh)
```

#### **Authorization (channels.php)**

```php
Broadcast::channel('chat.{userA}.{userB}', function ($user, $userA, $userB) {
    return in_array($user->id, [(int)$userA, (int)$userB]);
    // Chỉ 2 người này mới có quyền nghe channel
});

Broadcast::channel('notify.{userId}', function ($user, $userId) {
    return $user->id == $userId;
    // Chỉ chính người đó mới nhận thông báo
});
```

#### **Function: broadcastAs()**

```
return 'MessageSent';

→ Frontend listener:
   .listen('.MessageSent', (e) => {})
   
Nếu không có broadcastAs():
   .listen('App\\Events\\MessageSent', (e) => {})
```

#### **Function: broadcastWith()**

```
return [
    'message' => [
        'content' => tin nhắn nội dung
    ],
    'user' => [
        'id'   => id người gửi,
        'name' => tên người gửi
    ]
];

→ Frontend nhận được event object với dữ liệu này
```

---

### **6️⃣ Reverb Server (WebSocket) - Phát Sóng**

**File**: `config/reverb.php`

```
┌─────────────────────────────────────────────┐
│  Reverb Server (Laravel Broadcasting)       │
│                                             │
│  Chạy trên port: 8080                      │
│  Protocol: WebSocket (ws/wss)              │
│  Scaling: Redis (nếu enabled)              │
└─────────────────┬───────────────────────────┘
                  │
        ┌─────────┴─────────┐
        ▼                   ▼
  (Local Mode)        (Scaling via Redis)
  - In-process        - Multiprocess support
  - Tốt cho dev       - Tốt cho production
```

---

### **7️⃣ Frontend - Echo Listener (Real-time Update)**

**File**: `resources/js/echo.js` + `resources/views/user/chat.blade.php`

#### **Step 1: Khởi tạo Echo**

```javascript
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: VITE_REVERB_APP_KEY,
    wsHost: VITE_REVERB_HOST,
    wsPort: VITE_REVERB_PORT,
    wssPort: VITE_REVERB_PORT,
    forceTLS: true (nếu https),
    enabledTransports: ['ws', 'wss']
});
```

#### **Step 2: Khi User Chọn 1 Người Để Chat**

```javascript
function openChat(user) {
    currentChatUser = user
        ↓
    Callback: window.onNewMessageReceivedNotification?.(user.id)
        ↓
    showChatArea(user)  // Hiển thị chat UI
        ↓
    subscribeToChat(user.id)  // Subscribe WebSocket channel
        ↓
    loadHistory(user.id)  // Load tin nhắn lịch sử
}
```

#### **Step 3: Subscribe Channel**

```javascript
function subscribeToChat(otherUserId) {
    // Tính tên channel (phải giống server)
    const channelName = getChatChannelName(otherUserId);
    // VD: "chat.2.5" (user ID 2 & 5)
        ↓
    Unsubscribe channel cũ (nếu có)
        ↓
    echoChannel = Echo.private(channelName)
        ↓
    echoChannel
        .listen('.MessageSent', (e) => {
            // Nhận tin nhắn realtime
            if (e.user.id === currentChatUser.id || e.user.id === AUTH_USER.id) {
                // Tin nhắn từ người chat hoặc từ chính mình
                appendMessage({
                    content: e.message.content,
                    user: e.user
                });
            }
        })
        .subscribed(() => {
            console.log('Subscribed:', channelName);
        })
        .error((err) => {
            console.error('Channel error:', err);
        });
}
```

#### **Step 4: Hiển Thị Tin Nhắn**

```javascript
function appendMessage({ content, user }) {
    const isMine = user.id === AUTH_USER.id;
    
    Create div.message element:
    - Class: 'mine' (nếu từ mình) hoặc 'others'
    - HTML: <strong>${user.name}:</strong> ${content}
        ↓
    appendChild vào DOM (#messages)
        ↓
    Scroll to bottom: scrollTop = scrollHeight
}
```

---

### **8️⃣ Load Lịch Sử Tin Nhắn**

**Endpoint**: GET `/messages/history/{userId}`

```javascript
async function loadHistory(otherUserId) {
    fetch(`/messages/history/${otherUserId}`)
        ↓
    Response: Array of messages
    [
        {
            "id": 1,
            "from_user_id": 2,
            "to_user_id": 5,
            "content": "Nội dung tin nhắn",
            "is_read": false,
            "created_at": "2026-01-09...",
            "sender": {
                "id": 2,
                "name": "User A"
            }
        },
        ...
    ]
        ↓
    Loop & append từng tin nhắn vào DOM
        ↓
    Scroll to bottom
}
```

**Backend Logic** (MessageRepository):

```php
getChatHistory($userId1, $userId2):
  SELECT * FROM messages
  WHERE (from_user_id = userId1 AND to_user_id = userId2)
     OR (from_user_id = userId2 AND to_user_id = userId1)
  ORDER BY created_at ASC
  WITH sender info (relationships)
```

---

### **9️⃣ Tính Năng: Thông Báo Tin Nhắn Chưa Đọc**

**File**: `resources/js/components/notify-message.js`

#### **Flow**

```
┌─ Khi load trang ─────────────────────────┐
│                                           │
│  Fetch /messages/unread-total             │
│      ↓                                    │
│  Lấy số lượng tin nhắn chưa đọc           │
│      ↓                                    │
│  Hiển thị badge (nếu count > 0)           │
│      ↓                                    │
│  Subscribe: Echo.private('notify.{userId}')
│      ↓                                    │
│  .listen('.MessageSent', (e) => {        │
│      if (tin nhắn từ user khác) {         │
│          ├─ Cộng badge count              │
│          ├─ Add fromUserId to Set         │
│          └─ Phát âm thanh                 │
│      }                                    │
│  })                                      │
└──────────────────────────────────────────┘
```

#### **Khi User Mở Chat**

```javascript
window.onNewMessageReceivedNotification = async function (userId) {
    currentChatUserId = userId
        ↓
    Fetch POST /messages/read/{userId}
    → Đánh dấu tất cả tin nhắn từ user này là đã đọc
        ↓
    Reload unread count từ server
        ↓
    Update badge
}
```

**Backend Logic** (MessageRepository):

```php
markAsRead($fromUserId, $toUserId):
  UPDATE messages
  SET is_read = true
  WHERE from_user_id = $fromUserId
    AND to_user_id = $toUserId
    AND is_read = false
```

---

### **🔟 Danh Sách Người Dùng**

**File**: `resources/js/components/user-list.js`

#### **Flow**

```
Khởi tạo: initUserList({ AUTH_USER, onSelectUser })
    ↓
Fetch GET /users
    ↓
Response: Array of users (không bao gồm chính mình)
[
    {
        "id": 2,
        "name": "User A",
        "avatar": "url/to/avatar.jpg"
    },
    ...
]
    ↓
Loop & render từng user:
  ├─ Create div.user-item
  ├─ Add avatar image
  ├─ Add user name
  └─ onclick → onSelectUser(user) → openChat(user)
        ↓
Append vào #user-list
```

---

## 🏗️ Kiến Trúc Thư Mục

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── MessageController.php      ← Xử lý tin nhắn
│   │   └── UserController.php         ← Danh sách user
│   └── Requests/
│       └── Chat/
│           └── StoreChatMessageRequest.php  ← Validation
├── Services/
│   └── ChatService.php                ← Business logic
├── Models/
│   ├── Message.php                    ← Tin nhắn model
│   └── User.php                       ← Người dùng model
├── Events/
│   └── MessageSent.php                ← Broadcasting event
├── Repositories/
│   └── Message/
│       └── MessageRepository.php      ← Data access layer
└── Contracts/
    └── Repository/
        └── Message/
            └── IMessageRepository.php  ← Interface

config/
└── reverb.php                          ← WebSocket config

routes/
├── web.php                            ← HTTP routes
└── channels.php                       ← Broadcasting channels

resources/
├── js/
│   ├── echo.js                        ← Echo initialization
│   ├── bootstrap.js                   ← Bootstrap
│   └── components/
│       ├── user-list.js               ← User list
│       └── notify-message.js          ← Notifications
└── views/
    └── user/
        └── chat.blade.php             ← Main UI

database/
└── migrations/
    └── 2026_01_02_133115_create_messages_table.php
```

---

## 🔐 Security Features

### **1. Authentication (Xác Thực)**
- Middleware `auth` trên các route
- `Auth::user()` lấy user hiện tại
- JWT/Session token trong request

### **2. Authorization (Phân Quyền)**
- Channel authorization:
  ```php
  Broadcast::channel('chat.{userA}.{userB}', function ($user, $userA, $userB) {
      return in_array($user->id, [(int)$userA, (int)$userB]);
  });
  ```
- Chỉ 2 người tham gia chat mới có quyền

### **3. CSRF Protection**
- X-CSRF-TOKEN header trong mọi POST request
- Laravel automatically validates

### **4. Input Validation**
- `StoreChatMessageRequest`:
  - `message`: max 1000 characters
  - `to_user_id`: phải exist trong users table

### **5. Socket ID**
- Pusher feature để tránh duplicate messages
- Ngừng gửi tin nhắn cho socket ID của người gửi (`.toOthers()`)

---

## 🌐 Real-time Communication Flow

### **Sequence Diagram**

```
User A                Frontend A          Backend              Reverb        Frontend B          User B
  │                       │                   │                  │              │                   │
  │ Type & Send Message   │                   │                  │              │                   │
  ├──────────────────────>│                   │                  │              │                   │
  │                       │ POST /send-message│                  │              │                   │
  │                       ├────────────────────>                 │              │                   │
  │                       │                   │ Save to DB       │              │                   │
  │                       │                   │ Dispatch Event   │              │                   │
  │                       │   JSON {ok}       ├─────────────────>│              │                   │
  │                       │<───────────────────┤                 │ Broadcast to │                   │
  │ Optimistic Update     │                   │                 │ chat & notify│                   │
  │<──────────────────────┤                   │                 ├─────────────────────────────────>│
  │ Message displayed     │                   │                 │              │ Listen event      │
  │                       │                   │                 │              │ Append message    │
  │                       │                   │                 │              ├─────────────────>│
  │                       │                   │                 │              │                   │
  │                       │                   │                 │              │                   │
  │                       │                   │                 │              │ User gets notif   │
  │                       │                   │                 ├─────────────────────────────────>│
  │                       │                   │                 │              │                   │
  │                       │                   │                 │              │                   │
```

---

## ⚡ Performance & Optimization

### **1. Optimistic Updates**
```javascript
// Tin nhắn được append vào UI ngay
appendMessage({ content: msg, user: AUTH_USER });
// Rồi mới gửi API
await sendMessage(msg);
```
- User cảm thấy app nhanh hơn
- Nếu API fail, có thể show error & undo

### **2. WebSocket Connection Pool**
- 1 WebSocket connection cho mỗi browser tab
- Echo manage subscriptions tự động
- `unsubscribe` channel cũ khi chọn user mới

### **3. Database Optimization**
- Load history với `.with('sender')` (eager loading)
- Tránh N+1 query problem

### **4. Broadcasting Optimization**
- `.toOthers()` → không gửi lại cho sender (tránh duplicate)
- Tin nhắn được append optimistically trước khi nhận từ server

---

## 📡 Tech Stack

| Layer | Technology | Purpose |
|-------|-----------|---------|
| **Server** | Laravel 11 | Framework HTTP & Broadcasting |
| **WebSocket** | Laravel Reverb | Real-time messaging server |
| **Broadcast** | Laravel Broadcasting + Pusher Protocol | Event distribution |
| **Frontend JS** | Vanilla JS + Alpine.js | UI & Real-time listeners |
| **Real-time Lib** | Laravel Echo | WebSocket client wrapper |
| **Database** | MySQL/PostgreSQL | Store messages & users |
| **Queue** | Redis/Database | Message queue (optional) |
| **Scaling** | Redis | Cross-server WebSocket sync |

---

## 🚀 Workflow Tổng Kết

```
1. User A gõ tin nhắn & submit form
   ↓
2. Frontend gọi POST /messages/send-message
   ↓
3. MessageController validate & gọi ChatService::sendMessage()
   ↓
4. ChatService lưu Message vào DB trong transaction
   ↓
5. Dispatch MessageSent event (broadcast)
   ↓
6. Reverb nhận event từ Redis, phát đến clients
   ↓
7. Frontend A & B nhận sự kiện trên channel chat.{ids}
   ↓
8. JavaScript append tin nhắn vào DOM realtime
   ↓
9. User B cũng nhận notify event trên channel notify.{B_id}
   ↓
10. Badge count cộng + phát âm thanh thông báo
    ↓
11. Khi User B mở chat → POST /messages/read/{A}
    ↓
12. Mark messages from A as read (is_read = true)
    ↓
13. Badge count update
```

---

## 🔗 Dependency Injection

```
AppServiceProvider:
  └─ bind(IMessageRepository) → MessageRepository

MessageController:
  └─ __construct(ChatService $chatService)

ChatService:
  └─ __construct(IMessageRepository $messageRepository)
```

---

## 📝 Notes

- **Channel naming**: ID luôn được sort để tránh mâu thuẫn (`chat.2.5` = `chat.5.2`)
- **toOthers()**: Người gửi không nhận lại tin nhắn của chính mình
- **is_read flag**: Theo dõi tin nhắn chưa đọc
- **Realtime**: Không cần refresh page, tất cả cập nhật tức thì
- **Scalability**: Reverb + Redis support multiple servers

---

**Tài liệu được tạo**: 09/01/2026
**Version**: Laravel 11 + Reverb 1.0
