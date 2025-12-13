# FlyEase Backend

FlyEase Backend is a RESTful API built with **Laravel 12**. It powers the FlyEase flight booking application, handling authentication, flight management, bookings, and payments integration with Stripe.

## Features

### Authentication & Authorization
- **Sanctum Authentication**: Secure token-based authentication (Bearer Token).
- **Role-Based Access Control**: Middleware to separate User and Admin access.

### Core Functionalities
- **User Management**: Registration, login, and profile management.
- **Flight Management**:
  - Public: Search and view flight details.
  - Admin: CRUD operations for flights (schedule, price, route).
- **Booking System**:
  - Users can book flights and manage their reservations.
  - Integration with **Stripe** for payment processing.
  - Admin oversight of all bookings.
- **Admin Dashboard**: Aggregated statistics for revenue, bookings, and active flights.

## Technology Stack

- **Framework**: [Laravel 12](https://laravel.com/)
- **Language**: PHP 8.2
- **Database**: MySQL
- **Authentication**: Laravel Sanctum
- **Payments**: Stripe PHP SDK
- **Containerization**: Docker

## Project Structure

```
flyease-backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/    # API Controllers (Auth, User, Admin)
│   │   ├── Middleware/     # Admin and Auth Middleware
│   │   └── Requests/       # Form Requests for validation
│   └── Models/             # Eloquent Models (User, Flight, Booking)
├── routes/
│   └── api.php             # API Routes definition
├── database/               # Migrations, Factories, and Seeders
├── tests/                  # Unit and Feature tests
├── Dockerfile              # Docker configuration for production
└── docker-entrypoint.sh    # Entry script for Render deployment
```

## Installation & Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-username/flyease-backend.git
   cd flyease-backend
   ```

2. **Install Dependencies**
   ```bash
   composer install
   ```

3. **Environment Configuration**
   Copy the example environment file and configure your database and Stripe keys.
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   **Required .env variables:**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=flyease
   DB_USERNAME=root
   DB_PASSWORD=

   STRIPE_KEY=pk_test_...
   STRIPE_SECRET=sk_test_...
   ```

4. **Run Migrations**
   ```bash
   php artisan migrate
   ```

5. **Start Development Server**
   ```bash
   php artisan serve
   ```
   The API will be available at `http://localhost:8000`.

## API Endpoints

### Public
- `POST /api/register`: Register a new user.
- `POST /api/login`: Log in and get a token.
- `GET /api/flights`: Search for flights.

### User (Authenticated)
- `POST /api/bookings`: Create a booking.
- `POST /api/bookings/confirm`: Confirm payment.
- `GET /api/me`: Get user profile.

### Admin
- `GET /api/admin/stats`: Get dashboard statistics.
- `apiResource /api/admin/flights`: Manage flights.
- `apiResource /api/admin/users`: Manage users.

## Deployment (Docker/Render)

The project includes a `Dockerfile` optimized for production deployment

1. **Build Docker Image**
   ```bash
   docker build -t flyease-backend .
   ```

2. **Run Container**
   ```bash
   docker run -p 8000:80 flyease-backend
   ```
