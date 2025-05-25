# Dashboard

## Description

This project is an internal company dashboard developed with Laravel. It functions as an intranet platform, providing a centralized space for managing internal operations and communication within a company.

## 💻 Prerequisites
* PHP `^8.2`
* Composer `^8.0`
* Node `^23.11`
* Npm `^11.0`

## ⚙️ Installation

1. Install PHP dependencies
```bash
composer install
```

2. Install JavaScript dependencies
```bash
npm install
```

3. Copy the .env file and generate the application key
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure your .env file
```bash
Update the database and other environment settings as needed.
```

5. Run migrations
```bash
php artisan migrate
```

6. Seed the database
```bash
php artisan db:seed
```

7. Serve the application
```bash
php artisan serve
```

8. Build frontend assets
```bash
npm run dev
```

> 💡 You may want to run both the Laravel server and the Vite dev server simultaneously for hot module replacement (HMR). In that case, use `php artisan serve` and `npm run dev` in separate terminals.
