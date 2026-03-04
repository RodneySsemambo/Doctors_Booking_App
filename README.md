# 🏥 Doctors Booking App

A production-ready healthcare booking platform built with Laravel 11. Patients can discover doctors, book appointments, pay via mobile money, and chat with an AI-powered assistant and a well structured Rest full api — all in one place.

> 🔗 **Live Demo:** [Coming Soon](#) <!-- Replace with Render URL after deployment -->

---

## 📸 Screenshots

<!-- Upload your screenshots to the repo and replace the paths below -->

### Patient Dashboard
![Patient Dashboard](screenshots/patient-dashboard.png)

### Book Appointment
![Book Appointment](screenshots/book-appointment.png)

### AI Chatbot
![Chatbot](screenshots/chatbot.png)

### Admin Panel
![Admin Panel](screenshots/admin-panel.png)

### Payment Flow
![Payment](screenshots/payment.png)

---

## ✨ Features

### 👤 Patient
- Register, login, and manage profile
- Search and filter doctors by specialization
- Book appointments with real-time slot conflict prevention
- Pay via **MTN Mobile Money** or **Airtel Money**
- Chat with an AI assistant to find doctors and book appointments
- View appointment history and upcoming appointments
- Receive email and SMS notifications

### 👨‍⚕️ Doctor
- Manage availability and schedules
- View and manage patient appointments
- Issue prescriptions and medical records
- Track earnings and request withdrawals

### 🛡️ Admin (Filament Panel)
- Full system management dashboard
- Doctor verification and management
- Payment monitoring and withdrawal approvals
- Platform wallet management
- Chatbot analytics

---

## 🛠️ Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP 8.2, Laravel 11 |
| Frontend | Laravel Livewire 3, Blade, Tailwind CSS, Alpine.js |
| Admin Panel | Filament 4 |
| Database | MySQL 8.0 |
| Payments | MarzPay (MTN & Airtel Mobile Money) |
| Notifications | SMS via Africa's Talking, Email via SMTP |
| Testing | PHPUnit, Laravel Feature & Unit Tests |
| DevOps | Docker, Docker Compose, Nginx |
| Auth | Laravel Sanctum |

---

## 🧪 Test Suite

**31 tests · 95 assertions · All passing ✅**

```bash
php artisan test
```

```
PASS  Tests\Unit\ChatServiceTest        10 tests
PASS  Tests\Feature\ChatWidgetTest       8 tests  
PASS  Tests\Unit\PaymentServiceTest      5 tests
PASS  Tests\Unit\AppointmentServiceTest 13 tests
PASS  Tests\Feature\ExampleTest          1 test

Tests: 31 passed (95 assertions)
```

Tests cover:
- Appointment booking, slot conflict prevention, cancellation and refunds
- MTN & Airtel payment initiation and verification
- Chatbot intent detection, specialization extraction, conversation management
- Livewire chat widget validation and real-time behaviour

---

## ⚙️ Local Setup

### Requirements
- PHP 8.2+
- Composer
- MySQL 8.0+
- Node.js & NPM

### Installation

```bash
# 1. Clone the repo
git clone https://github.com/RodneySsemambo/Doctors_Booking_App.git
cd Doctors_Booking_App

# 2. Install dependencies
composer install
npm install && npm run build

# 3. Environment setup
cp .env.example .env
php artisan key:generate

# 4. Configure your database in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=doctors_booking
DB_USERNAME=root
DB_PASSWORD=

# 5. Run migrations and seed demo data
php artisan migrate --seed

# 6. Start the server
php artisan serve
```

Visit `http://localhost:8000`

### Demo Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@example.com | password |
| Doctor | doctor@example.com | password |
| Patient | patient@example.com | password |

---

## 🐳 Docker Setup

```bash
# Copy environment file
cp .env.example .env

# Update .env database settings
DB_HOST=db
DB_DATABASE=doctors_booking
DB_USERNAME=doctors_user
DB_PASSWORD=secret

# Build and run
docker-compose up --build
```

Visit `http://localhost:8000` · phpMyAdmin at `http://localhost:8080`

---

## 📁 Project Structure

```
app/
├── Http/
│   ├── Controllers/        # API & web controllers
│   └── Middleware/         # Custom middleware
├── Livewire/               # Livewire components
│   ├── BookAppointment.php # Multi-step booking wizard
│   └── ChatWidget.php      # Real-time chat component
├── Models/                 # Eloquent models
├── Services/               # Business logic layer
│   ├── AppointmentService.php
│   ├── ChatService.php
│   └── PaymentService.php
└── Filament/               # Admin panel resources
```

---

## 🔑 Key Implementation Details

- **Slot conflict prevention** — `AppointmentService` checks availability atomically before writing, preventing double-bookings even under concurrent requests
- **Payment flow** — MarzPay integration with webhook callback verification and automatic refunds on cancellation
- **Chatbot NLP** — Intent detection via regex with context awareness, specialization matching against the database, and conversation history for multi-turn dialogue
- **Test isolation** — SQLite in-memory database for tests with proper handling of nested transactions via `$connectionsToTransact = []`

---

## 📄 License

MIT License — feel free to use this project as a reference or starting point.

---

## 👨‍💻 Author

**Rodney Ssemambo**
- GitHub: [@RodneySsemambo](https://github.com/RodneySsemambo)
- Email: ssemamborodney94@gmail.com
- Location: Kampala, Uganda 🇺🇬

*Open to remote Laravel/PHP developer roles.*
