# MediQueue

A web-based **outpatient medical management system** built with Laravel. Patients browse departments and doctors, book appointments by selecting a time slot, pay online, and receive a digital queue token with real-time queue tracking. The system also manages prescriptions, medical history, payments, and provides an admin analytics dashboard.

## Features (20)

- **Appointment & Queue:** department catalogue, doctor profiles, 4-step booking wizard with real-time slot availability (AJAX), queue token generation with QR code, live queue dashboard, rescheduling
- **Payments:** multi-method gateway (card / mobile banking / wallet), digital receipts (PDF), fee transparency, policy-based refunds and cancellations
- **Medical Records:** visit history, secure report upload/storage, allergy & chronic-condition profile
- **Prescriptions:** digital prescription generation, history with PDF export, medication tracker
- **Notifications:** 24h / 2h appointment reminders, queue position alerts, post-visit consultation summary emails
- **Feedback & Admin:** doctor ratings and reviews, admin analytics dashboard

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 12 (PHP 8.2) |
| Database | MySQL 8.0+ |
| Frontend | Blade, Tailwind CSS, Alpine.js, Vite |
| Auth | Laravel Breeze (session-based) |
| Payments | Stripe / SSLCommerz (planned) |
| PDF / QR | dompdf, barryvdh/laravel-qrcode (planned) |

## Local Setup (Windows / XAMPP)

Prerequisites: [XAMPP](https://www.apachefriends.org/) (PHP 8.2 + MySQL), Composer, Node.js, Git.

```bash
# 1. Clone and install dependencies
git clone <repo-url> mediqueue
cd mediqueue
composer install
npm install

# 2. Configure environment
cp .env.example .env
php artisan key:generate
#   - set DB_DATABASE, DB_USERNAME, DB_PASSWORD for your local MySQL
#   - set APP_NAME=MediQueue

# 3. Create the database (XAMPP default: root / no password)
mysql -u root -e "CREATE DATABASE mediqueue CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 4. Migrate and build front-end assets
php artisan migrate
npm run build

# 5. Serve
php artisan serve
# visit http://localhost:8000
```

> With XAMPP, use the bundled PHP if it is not on your PATH: `C:\xampp\php\php.exe` (and `C:\xampp\mysql\bin\mysql.exe` for the database). Composer can be run as `php composer.phar ...` from the project root.

## Status

Current milestone — **Sprint 1 (Foundation)**:

- [x] Laravel project scaffold, MySQL schema, Git repo
- [x] Session-based authentication (Laravel Breeze): register, login, logout, password reset, profile
- [ ] Role-based access control (patient / doctor / admin)
- [ ] Department & specialty catalogue (FR-01)
- [ ] Doctor directory & profiles (FR-02)
- [ ] Appointment booking wizard (FR-03)
- [ ] Queue token generation (FR-04)

## Project Structure

```
mediqueue/
├── app/
│   ├── Http/Controllers/   # web + auth controllers
│   ├── Models/             # Eloquent models
│   ├── Providers/
│   └── View/Components/
├── config/                 # framework configuration
├── database/
│   ├── migrations/         # schema (users, sessions, cache, jobs)
│   └── seeders/
├── resources/
│   ├── css/  js/           # Tailwind + Alpine via Vite
│   └── views/              # Blade templates (layouts, auth, components)
├── routes/                 # web.php, auth.php, console.php
├── tests/                  # PHPUnit feature + unit tests
└── docs/                   # (outside the repo) SRS + project report
```

## Testing

```bash
php artisan test
```

## License

MIT
