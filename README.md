# Reverb Mini - Chat Realtime

Ứng dụng chat realtime được xây dựng với **Laravel 12**, **Reverb** (WebSocket), và **Tailwind CSS**.

## 📁 Cấu trúc Project

```
app/
├── Contracts/Repository/    # Interface cho Repository pattern
│   ├── Message/
│   └── User/
├── Events/                   # Event broadcasting
│   ├── MessageSent.php
│   └── UserStatus.php
├── Http/
│   ├── Controllers/          # Controllers
│   │   ├── MessageController.php
│   │   ├── UserController.php
│   │   └── ProfileController.php
│   └── Requests/
├── Models/                   # Eloquent models
│   ├── User.php
│   └── Message.php
├── Repositories/             # Repository pattern implementation
│   ├── Message/
│   └── User/
├── Services/                 # Business logic
│   ├── ChatService.php
│   └── UserService.php
└── Providers/
    └── AppServiceProvider.php

resources/
├── js/                       # Frontend (Alpine.js)
│   ├── app.js
│   ├── echo.js              # Reverb configuration
│   ├── bootstrap.js
│   └── components/
│       └── chat/            # Chat components
├── views/                    # Blade templates
│   ├── user/
│   ├── admin/
│   └── auth/
└── css/
    └── app.css

routes/
├── web.php                   # Web routes
├── auth.php                  # Auth routes (Breeze)
├── channels.php              # Broadcasting channels
└── console.php

database/
├── migrations/               # Database migrations
├── seeders/
└── factories/

config/
├── reverb.php               # Reverb WebSocket config
├── broadcasting.php
└── (other configs)
```

## 🚀 Cách Cài Đặt & Chạy

### 1. **Cài đặt Dependencies**

```bash
# PHP dependencies
composer install

# Node dependencies
npm install
```

### 2. **Cấu hình Environment**

```bash
# Copy file config
cp .env.example .env

# Tạo app key
php artisan key:generate
```

### 3. **Database Setup**

```bash
# Chạy migrations
php artisan migrate

# (Optional) Seed dữ liệu
php artisan db:seed
```

### 4. **Build Frontend Assets**

```bash
# Development mode
npm run dev

# Production mode
npm run build
```

### 5. **Chạy Server**

Mở **3 terminal** riêng biệt:

**Terminal 1 - Laravel Server:**
```bash
php artisan serve

**Terminal 2 - Reverb WebSocket:**
```bash
php artisan reverb:start  --host=127.0.0.1 --port=6001

**Terminal 3 - Frontend Dev Server:**
```bash
npm run dev

### 6. **Truy cập Ứng Dụng**

- **App:** http://localhost:8000
