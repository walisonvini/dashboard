# Dashboard

## Description

This project is an internal company dashboard developed with Laravel. It functions as an intranet platform, providing a centralized space for managing internal operations and communication within a company.

## 💻 Prerequisites

### Option 1: Local Development
* **PHP** `^8.2`
* **Composer** `^8.0`
* **Node.js** `^23.11`
* **NPM** `^11.0`
* **MySQL/PostgreSQL** (or your preferred database)

### Option 2: Docker Development
* **Docker** `^24.0`
* **Docker Compose** `^2.0`

## 🐋 Installation with Docker

1. Copy the .env file and generate the application key
    ```bash
    cp .env.example .env
    ```

2. Configure your .env file
    ```bash
    # Update the database credentials for Docker:
    # DB_CONNECTION=mysql
    # DB_HOST=mysql
    # DB_PORT=3306
    # DB_DATABASE=your_database_name
    # DB_USERNAME=your_username
    # DB_PASSWORD=your_password
    ```

3. Build Docker images
    ```bash
    docker compose build
    ```

4. Start containers
    ```bash
    docker compose up -d
    ```

5. Access the application container
    ```bash
    docker exec -it dashboard_web bash
    ```

6. Install PHP dependencies
    ```bash
    composer install
    ```

7. Run migrations
    ```bash
    php artisan migrate
    ```

8. Seed the database
    ```bash
    php artisan db:seed
    ```

9. Access the application
    ```bash
    # Open your browser and navigate to:
    http://localhost
    ```

> 🔧 **Permission Issues?** If you encounter permission problems when accessing the application, run these commands inside the container:
> ```bash
> chown -R $USER:www-data storage bootstrap/cache
> chmod -R 775 storage bootstrap/cache
> ```

## ⚙️ Installation without Docker

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
    # Update the database and other environment settings as needed
    ```

5. Run migrations
    ```bash
    php artisan migrate
    ```

6. Seed the database
    ```bash
    php artisan db:seed
    ```

7. Build frontend assets
    ```bash
    npm run dev
    ```

8. Serve the application
    ```bash
    php artisan serve
    ```

> 💡 **Development Tip:** For hot module replacement (HMR), run both servers simultaneously:
> ```bash
> # Terminal 1: Laravel server
> php artisan serve
> 
> # Terminal 2: Vite dev server  
> npm run dev
> ```

## ✅ Running Tests

This project uses [PHPUnit](https://phpunit.de/) for feature and unit testing.

1. Make sure you have a `.env.testing` file configured. You can copy it from the default `.env.testing.example` file:
    ```bash
    cp .env.testing.example .env.testing
    ```

2. Update `.env.testing` with your **testing database** credentials.

3. Cache the config specifically for the test environment:

    ```bash
    php artisan config:cache --env=testing
    ```

4. Run the tests:

    ```bash
    php artisan test
    ```

5. (Optional) To run a specific test method:

    ```bash
    php artisan test --filter=test_method_name
    ```

> 🧪 **Note:** Tests use a dedicated test database. The database will be refreshed between test runs using Laravel's `RefreshDatabase` trait, so **no production data will be affected**.