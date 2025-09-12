

# Laravel Admin Hub Stub

A minimal Laravel 11 application stub with OIDC login, organization subaccounts, a JSON `/metrics` endpoint, and a stubbed "Open as subaccount" feature, styled with TailwindCSS.

## Features
- **OIDC Login**: Stubbed OpenID Connect authentication flow with state validation.
- **Subaccounts**: Displays seeded organizations (Acme Corp, Globex Inc, Initech) as subaccounts in a dashboard.
- **Metrics Endpoint**: JSON API at `/metrics` returning user and organization counts with a timestamp.
- **Open as Subaccount**: Stubbed button to simulate switching to an organization context (sets session variable).
- **TailwindCSS**: Responsive UI for login and dashboard pages.

## Requirements
- PHP ^8.2
- Composer
- Node.js (for TailwindCSS)
- Laravel 11
- Database (SQLite, MySQL, or PostgreSQL)

## Setup Instructions

1. **Create Project**:
   ```bash
   composer create-project laravel/laravel laravel-admin-hub "11.*"
   cd laravel-admin-hub
   ```

2. **Copy Files**:
   - Replace the project files with the provided stub files (see project structure below).

3. **Install Dependencies**:
   ```bash
   composer install
   npm install
   npm run dev
   ```

4. **Environment Configuration**:
   - Copy `.env.example` to `.env`:
     ```bash
     cp .env.example .env
     ```
   - Update `.env` with database and OIDC settings:
     ```
     DB_CONNECTION=sqlite
     # Or for MySQL/PostgreSQL:
     # DB_CONNECTION=mysql
     # DB_HOST=127.0.0.1
     # DB_PORT=3306
     # DB_DATABASE=laravel_admin_hub
     # DB_USERNAME=root
     # DB_PASSWORD=

     OIDC_PROVIDER_URL=https://your-oidc-provider.com/oauth/authorize
     OIDC_CLIENT_ID=your_client_id
     OIDC_CLIENT_SECRET=your_client_secret
     OIDC_REDIRECT_URI=http://localhost:8000/oidc/callback
     ```

5. **Database Setup**:
   - For SQLite, create an empty database file:
     ```bash
     touch database/database.sqlite
     ```
   - Run migrations and seed data:
     ```bash
     php artisan migrate
     php artisan db:seed --class=OrganizationSeeder
     ```

6. **Register Middleware** (optional, for cleaner routes):
   - In `bootstrap/app.php`, add the OIDC middleware alias:
     ```php
     ->withMiddleware(function (Middleware $middleware) {
         $middleware->alias([
             'auth.oidc' => \App\Http\Middleware\AuthenticateWithOidc::class,
         ]);
     })
     ```
   - Update `routes/web.php` to use `auth.oidc` instead of the full middleware class.

7. **Start Server**:
   ```bash
   php artisan serve
   ```
   - Access at `http://localhost:8000`.

## Usage
- **Login**: Visit `/` and click "Login with OIDC". The stub redirects to the OIDC provider URL (configure in `.env`). After callback, you’ll land on the dashboard.
- **Dashboard**: Lists seeded organizations with "Open as Subaccount" buttons. Clicking a button sends a POST request and displays a stubbed success message.
- **Metrics**: Access `/metrics` for a JSON response with user/organization counts.
- **Logout**: Click "Logout" on the dashboard to clear the session and return to the login page.

## OIDC Integration
The OIDC flow is stubbed. For production:
- Install an OIDC package:
  ```bash
  composer require maicol07/laravel-oidc-client
  ```
- Publish config:
  ```bash
  php artisan vendor:publish --provider="Maicol07\OIDCClient\ServiceProvider"
  ```
- Update `app/Http/Controllers/Auth/OidcController.php` to use `OIDC::redirect()` and `OIDC::user()` per the package docs.
- Ensure `.env` has correct OIDC credentials.

## Project Structure
```
laravel-admin-hub/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/OidcController.php
│   │   │   ├── MetricsController.php
│   │   │   └── SubaccountController.php
│   │   └── Middleware/AuthenticateWithOidc.php
│   ├── Models/
│   │   ├── Organization.php
│   │   └── User.php
│   └── Providers/AppServiceProvider.php
├── config/
│   ├── auth.php
│   └── services.php
├── database/
│   ├── migrations/
│   │   ├── 2014_10_12_000000_create_users_table.php
│   │   └── 2025_09_12_000000_create_organizations_table.php
│   └── seeders/OrganizationSeeder.php
├── resources/
│   ├── css/app.css
│   ├── views/
│   │   ├── auth/login.blade.php
│   │   ├── layouts/app.blade.php
│   │   └── dashboard.blade.php
│   └── js/app.js
├── tailwind.config.js
├── vite.config.js
├── composer.json
└── package.json
```

## Notes
- The OIDC login is a stub; replace with a real provider (e.g., Keycloak, Auth0).
- The "Open as Subaccount" feature sets a session variable (`current_organization`) but is a stub—extend for actual context switching.
- TailwindCSS is used for styling; customize `resources/css/app.css` or `tailwind.config.js` as needed.
- The `/metrics` endpoint is unprotected; add authentication (e.g., Sanctum) for production.

## Troubleshooting
- **404 on OIDC Login**: Ensure `routes/web.php` has `oidc.redirect` and `oidc.callback` routes. Check `.env` for correct `OIDC_REDIRECT_URI`.
- **Database Errors**: Verify `.env` database settings and run `php artisan migrate:fresh --seed`.
- **Asset Issues**: Run `npm run build` for production or `npm run dev` for development.

