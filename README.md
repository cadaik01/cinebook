# 🎬 CineBook - Cinema Booking System

A modern cinema booking application built with Laravel 12 and Vue.js.

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat-square&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat-square&logo=mysql)
![Vite](https://img.shields.io/badge/Vite-5.x-646CFF?style=flat-square&logo=vite)

## 📖 About CineBook

CineBook is a comprehensive cinema ticket booking platform that allows users to browse movies, select seats, and make online payments. The system includes an admin panel for managing movies, showtimes, bookings, and QR code check-ins.

### ✨ Key Features

#### For Customers
- 🎥 Browse **Now Showing** and **Upcoming Movies**
- 🎫 Interactive seat selection with real-time availability
- 💳 Online payment integration (VNPay & MoMo)
- ⏰ 10-minute countdown timer for booking confirmation
- 📱 QR code generation for each seat/couple seat pair
- ⭐ Movie reviews and ratings
- 👤 User profile with booking history

#### For Admin
- 🎬 Movie management (CRUD operations)
- 🏢 Room and seat configuration
- 📅 Showtime scheduling
- 📊 Booking management and analytics
- 🔍 QR code scanner for check-in
- 💬 Review moderation
- 👥 User management

## 🚀 Quick Start

### Prerequisites

- PHP >= 8.2
- Composer
- Node.js >= 18 & NPM
- MySQL >= 8.0
- XAMPP (recommended)

### Installation

1. **Clone the repository**
```bash
git clone https://github.com/yourusername/cinebook.git
cd cinebook
```

2. **Copy environment file**
```bash
copy .env.example .env
```

3. **Configure database** in `.env`
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cinebook
DB_USERNAME=root
DB_PASSWORD=
```

4. **Install dependencies**
```bash
composer install
npm install
```

5. **Generate application key**
```bash
php artisan key:generate
```

6. **Create database and import data**
```bash
# Create database
mysql -u root -e "CREATE DATABASE cinebook;"

# Import schema
mysql -u root cinebook < mySQL/mySQL.sql

# Import sample data
mysql -u root cinebook < mySQL/data.sql
```

7. **Link storage**
```bash
php artisan storage:link
```

8. **Run the application**
```bash
# Terminal 1 - Laravel server
php artisan serve

# Terminal 2 - Vite dev server
npm run dev
```

9. **Access the application**
- Frontend: http://localhost:8000
- Admin Panel: http://localhost:8000/admin

## 👤 Default Credentials

### Admin Account
- Email: `admin@cinebook.com`
- Password: `admin123`

### User Account
- Email: `user@cinebook.com`
- Password: `user123`

## 📁 Project Structure

```
cinebook/
├── app/
│   ├── Http/Controllers/     # Application controllers
│   │   ├── Admin/           # Admin panel controllers
│   │   ├── BookingController.php
│   │   ├── PaymentController.php
│   │   └── ...
│   └── Models/              # Eloquent models
├── database/
│   ├── migrations/          # Database migrations
│   └── seeders/             # Database seeders
├── mySQL/
│   ├── mySQL.sql           # Database schema
│   └── data.sql            # Sample data
├── public/
│   ├── css/                # Compiled CSS
│   ├── js/                 # JavaScript utilities
│   │   ├── seat_map.js    # Seat selection logic
│   │   ├── booking-countdown.js
│   │   └── qr_checkin.js  # QR scanner
│   └── images/            # Static images
├── resources/
│   ├── css/               # Source CSS files
│   ├── js/                # Source JavaScript
│   └── views/             # Blade templates
│       ├── admin/         # Admin panel views
│       ├── booking/       # Booking flow views
│       ├── payment/       # Payment views
│       └── ...
└── routes/
    └── web.php            # Application routes
```

## 🎯 Core Functionality

### Booking Flow
1. **Select Movie** → Browse now showing or upcoming movies
2. **Choose Showtime** → Select date and time
3. **Pick Seats** → Interactive seat map with color-coded types
4. **Confirm Booking** → 10-minute countdown timer starts
5. **Payment** → QR code for VNPay/MoMo (mock)
6. **Success** → Receive QR codes for each seat/couple pair

### QR Code System
- Each regular seat gets **1 unique QR code**
- Couple seats (2 adjacent seats) share **1 QR code**
- QR codes are displayed on booking success page
- Admin can scan QR codes for check-in verification
- Status tracking: `active` → `checked` → `cancelled`

### Countdown Timer
- Starts at booking confirmation (10 minutes)
- Persists across pages using localStorage
- Warning style when < 1 minute remaining
- Auto-redirects on expiration
- Cleared on successful payment

## 🛠️ Technologies Used

### Backend
- **Laravel 12** - PHP Framework
- **MySQL** - Database
- **SimpleSoftwareIO/QrCode** - QR code generation

### Frontend
- **Vite** - Build tool
- **Blade Templates** - Laravel templating engine
- **Vanilla JavaScript** - Interactive features
- **CSS3** - Styling with gradients and animations

## 📚 Documentation

For detailed setup instructions, see [SETUP_GUIDE.md](SETUP_GUIDE.md)

## 🐛 Common Issues

### "APP_URL is undefined"
```bash
copy .env.example .env
php artisan key:generate
```

### "Vite manifest not found"
```bash
npm install
npm run dev
```

### Database connection failed
1. Start MySQL in XAMPP
2. Check `.env` database credentials
3. Ensure database `cinebook` exists

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 📧 Contact

For questions or support, please open an issue on GitHub.

---

**Built with ❤️ using Laravel**
