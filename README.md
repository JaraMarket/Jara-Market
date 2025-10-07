# JaraMarket — Bringing the Market to Your Doorstep

## 📖 Story: Why JaraMarket?
In the hustle and bustle of daily life, one thing remains constant — everyone needs to eat.  
But what happens when time runs short, traffic gets overwhelming, or the stress of a crowded market becomes just too much?  

At **JaraMarket**, we saw a problem and chose to build a solution rooted in **convenience, community, and care**.  

> **Market at Your Door** — that’s more than just a tagline. It’s our promise.

We believe everyone deserves access to **fresh, local ingredients** without sacrificing their time or peace of mind. Whether you're a busy professional, a student juggling schedules, or a parent managing the home front, **JaraMarket** is here to make food shopping one less thing to worry about.

- **We Source, You Cook**: You send us your recipe or select from our featured meals — we hit the markets, gather every item you need, and deliver them to your doorstep **within minutes**.  
- **More Than Food**: JaraMarket is not just a food tech platform. It’s a **movement** for people who still believe in the magic of home-cooked meals but need a little help making it happen.  

The market is changing. And with **JaraMarket**, it’s coming to your door. 🛒

---

## ⚙️ Project Setup

This project is built with **Laravel 10, Kafka, Redis, MySQL, and WebSockets** running inside **Docker**.

### 🔑 Prerequisites
Make sure you have:
- Docker & Docker Compose installed
- Git installed

---

## 🛠️ 1. Clone and Build

```bash
git clone https://github.com/JaraMarket/jaramarket.git
cd jaramarket
```
## GIT BRANCH
fix/product-create

Build and start containers:

```bash
docker compose up -d --build
```

This starts:
- **app** → Laravel + Apache + Supervisor  
- **db** → MySQL 8  
- **redis** → Redis for queues/cache  
- **kafka & zookeeper** → Kafka event streaming  
- **kafka-ui** → UI for managing topics (http://localhost:8080)  
- **phpmyadmin** → DB management (http://localhost:8081)  

---

## 🗄️ 2. Database Setup

Run migrations and seeders inside the container:

```bash
docker exec -it yara_app bash
php artisan migrate --force
php artisan db:seed --force
```

This prepares all tables (users, orders, wallets, etc.) with initial seed data.

---

## 🔄 3. Supervisor & Services

The container is already configured with **Supervisor** to run background workers:

- Laravel Queue Worker
- Kafka Consumer (wallet-events, order-events, etc.)
- WebSocket server (for broadcasting real-time notifications)

You can check logs:

```bash
docker logs -f yara_app
```

Or inside the container:

```bash
supervisorctl status
```

---

## 📡 4. Kafka & Notifications Flow

1. **Laravel Action**: User places an order / wallet is credited or debited.  
2. **Kafka Producer**: A payload is published to `wallet-events` or `order-events`.  
3. **Kafka Consumer**: Supervisor runs a `KafkaWalletConsumerCommand` that consumes messages.  
4. **Laravel Notification**: Message is turned into a `WalletNotification` or `OrderNotification`.  
5. **Broadcast via WebSocket**: Frontend clients receive the notification in real time.  

---

## 💻 5. Frontend — Connecting to WebSocket

Example (using Laravel Echo + Pusher replacement):

```javascript
import Echo from "laravel-echo";

window.Echo = new Echo({
    broadcaster: "pusher",
    key: "anyKey",
    wsHost: window.location.hostname,
    wsPort: 6001,
    forceTLS: false,
    disableStats: true,
});

window.Echo.private(`users.${userId}`)
    .listen(".Illuminate\\Notifications\\Events\\BroadcastNotificationCreated", (notification) => {
        console.log("📢 New Notification:", notification);
    });
```

---

## 🔍 6. Testing

### Send Test Wallet Event
Inside Laravel Tinker:

```bash
php artisan tinker
```

```php
$user = \App\Models\User::first();

KafkaService::publish('wallet-events', [
    'type'      => 'debit',
    'amount'    => 1000,
    'balance'   => $user->wallet->balance - 1000,
    'reference' => 'ORDER1234',
    'remarks'   => 'Order payment',
    'user_id'   => $user->id,
]);
```

Check frontend → should receive real-time notification.  

---

## ✅ Summary

- **Docker** builds and runs the full stack (App + Kafka + MySQL + Redis).  
- **Migrations & Seeds** are run via Artisan.  
- **Supervisor** runs WebSocket + Kafka consumers automatically.  
- **Kafka Events** flow into Laravel Notifications → Broadcast to WebSocket → Frontend receives instantly.  

---

# 🚀 With JaraMarket
We’re not just delivering ingredients.  
We’re delivering **ease, comfort, and peace of mind**.  