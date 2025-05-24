# Library Management System

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
</p>

## Project Overview

This Library Management System is a web application that allows librarians to manage books, users, and loans efficiently. It features role-based access control with four user types:

- **Administrators**: Full system access including user management
- **Bookkeepers**: Book and loan management
- **Regular Users**: Browse books and manage their own loans
- **Guests**: Browse available books (no login required)

## Features

- User authentication with role-based access control
- Book management (add, edit, delete)
- Bulk book addition using ISBN numbers
- Automated book data retrieval from OpenLibrary API
- Book cover image caching for improved performance
- Loan management (borrow, return, track due dates)
- User profile management
- Admin dashboard with statistics

## Requirements

- PHP 8.2 or higher
- Composer
- MySQL 8.0 or higher
- Node.js and npm (for frontend assets)
- Docker Desktop (optional, for containerized setup)

## Installation

### Step 1: Clone the Repository

```bash
git clone <repository-url>
cd WSB_prog_w_zastosowaniach
```

### Step 2: Install Dependencies

```bash
composer install
npm install
npm run build
```

### Step 3: Configure Environment

```bash
php artisan key:generate
```

Edit the `.env` file to configure your database connection:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=wsb_2024_k07_10
DB_USERNAME=root
DB_PASSWORD=
```

Make sure to create the `wsb_2024_k07_10` database in your MySQL server before proceeding.

### Step 4: Setup Database

```bash
php artisan migrate --seed
```

This will create the database structure and seed it with:
- Default users (admin, bookkeeper, regular user)
- Genre categories

### Step 5: Create Storage Link

```bash
php artisan storage:link
```

## Running the Application

```bash
php artisan serve
```

Access the application at http://localhost:8000

Make sure no other applications are using port 8000. If you need to use a different port, you can specify it with:

```bash
php artisan serve --port=8888
```

## Default Login Credentials

- **Admin User**:
  - Email: admin@admin.com
  - Password: admin

- **Bookkeeper User**:
  - Email: bookkeeper@example.com
  - Password: password

- **Regular User**:
  - Email: user@example.com
  - Password: password

## Verification Steps

Follow these steps to ensure the application is working correctly:

### 1. User Authentication

- [ ] Navigate to the login page
- [ ] Login with the admin credentials
- [ ] Verify you can access the admin dashboard
- [ ] Logout and login with bookkeeper credentials
- [ ] Verify bookkeeper access restrictions
- [ ] Logout and login with regular user credentials
- [ ] Verify user role restrictions

### 2. Book Management

- [ ] Login as admin or bookkeeper
- [ ] Navigate to the Books section in admin panel
- [ ] Add a new book manually
- [ ] Edit the added book
- [ ] Test bulk add feature using ISBNs from ISBN_LIST.txt
- [ ] Verify books appear in both admin panel and public listing

### 3. Loan System

- [ ] Login as a regular user
- [ ] Browse available books
- [ ] Borrow a book
- [ ] Verify the book appears in the user's "My Books" section
- [ ] Return the book
- [ ] Verify loan history is maintained

### 4. User Management

- [ ] Login as admin
- [ ] Navigate to Users section
- [ ] Create a new user
- [ ] Assign different roles
- [ ] Test role permissions

### 5. Troubleshooting

If you encounter issues:

- Check storage permissions (storage directory should be writable)
- Ensure database connection is properly configured
- Verify the database `wsb_2024_k07_10` exists in your MySQL server
- Look at Laravel logs in `storage/logs/laravel.log`
- Verify the public storage symlink exists
- If cover images aren't displaying, run `php artisan storage:link` to create the necessary symlink

## Adding Books via Bulk Add

The system includes a bulk add feature to quickly add books using ISBN numbers. A sample list of ISBNs is provided in the `ISBN_LIST.txt` file.

1. Login as admin or bookkeeper
2. Go to Books section in admin panel
3. Click on the dropdown next to "Add Book" and select "Bulk Add"
4. Enter ISBNs (separated by commas, spaces, or new lines)
5. Submit to create new book entries

## Authors

- Dawid Skrzypacz
- Patryk Pawlicki
- Witold Mikołajczak

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## Running with Docker

Alternatively, you can run the application using Docker, which provides a consistent environment across different operating systems.

### Prerequisites

- **Docker Desktop** (https://www.docker.com/products/docker-desktop/)
  - Make sure Docker Desktop is running before you start the app.

### Docker Setup Steps

1. **Clone the repository** (if you haven't already):
   ```sh
   git clone https://github.com/xPatricki/WSB_prog_w_zastosowaniach.git
   cd WSB_prog_w_zastosowaniach
   ```

2. **Start the containers:**
   ```sh
   docker compose up --build
   ```
   This will build and start the app, database, and phpMyAdmin.

3. **Run database migrations and seeders:**
   (In a new terminal, with containers running)
   ```sh
   docker compose exec app php artisan migrate:fresh --seed
   ```

4. **Access the app:**
   - Library application: [http://localhost:8000](http://localhost:8000)
   - phpMyAdmin: [http://localhost:8080](http://localhost:8080) (host: `db`, user: `laravel`, pass: `password`)

### Troubleshooting Docker Setup

- If you get database or migration errors, try:
  ```sh
  docker compose exec app php artisan migrate:fresh --seed
  ```
- Make sure Docker Desktop is running and no other services are using ports 8000, 8080, or 3306.
