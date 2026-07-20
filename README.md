# OIDC Admin Hub

A Laravel 12 admin-hub scaffold demonstrating the structure of an OpenID Connect login flow, organisation subaccounts, session-based organisation context, and a JSON metrics endpoint.

The project is intentionally small. It is useful for reviewing authentication-flow design, Laravel middleware, organisation switching, Blade interfaces, database seeding, and API contracts.

> **Important:** The current OIDC flow is a stub. It constructs an authorisation redirect, validates the `state` value on callback, and signs in a local placeholder user. It does not currently exchange an authorisation code, validate an ID token, verify issuer or audience claims, or retrieve a real user profile from an identity provider.

## Current Features

### OIDC Login Scaffold

- Login screen with an OIDC sign-in action
- Random `state` value stored in the session
- Redirect URL construction for an external identity provider
- Callback route with state validation
- Placeholder local-user creation and Laravel session login
- Logout support

### Organisation Subaccounts

- Seeded organisation records
- Authenticated dashboard listing organisations
- “Open as subaccount” action
- Selected organisation stored in the session as `current_organization`
- JSON success response after switching organisation context

### Metrics Endpoint

- JSON application-health style response
- Active user count
- Organisation count
- ISO 8601 timestamp

### User Interface

- Laravel Blade views
- Tailwind CSS 4
- Vite asset compilation
- Responsive login and dashboard foundations

## Technology Stack

### Backend

- PHP 8.2+
- Laravel 12
- Laravel Sanctum
- Guzzle
- Eloquent ORM
- Laravel session authentication
- PHPUnit 11
- Laravel Pint
- Laravel Sail

### Frontend

- Blade
- Tailwind CSS 4
- Vite 7
- Axios

## Project Structure

```text
oidc_adminhub/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   └── OidcController.php
│   │   │   ├── MetricsController.php
│   │   │   └── SubaccountController.php
│   │   └── Middleware/
│   │       └── AuthenticateWithOidc.php
│   ├── Models/
│   │   ├── Organization.php
│   │   └── User.php
│   └── Providers/
├── bootstrap/
├── config/
│   ├── auth.php
│   └── services.php
├── database/
│   ├── migrations/
│   ├── seeders/
│   │   └── OrganizationSeeder.php
│   └── database.sqlite
├── public/
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
├── routes/
│   ├── web.php
│   └── console.php
├── tests/
│   ├── Feature/
│   └── Unit/
├── composer.json
├── package.json
├── phpunit.xml
├── vite.config.js
└── README.md
```

## Application Flow

### 1. Start OIDC Login

The user opens the login page and selects the OIDC sign-in action.

The application:

1. Generates a random state value.
2. Stores it in the Laravel session.
3. Constructs an external authorisation URL.
4. Redirects the browser to the configured provider URL.

### 2. Handle Callback

The identity provider redirects to:

```text
/oidc/callback
```

The current callback implementation:

1. Reads the returned `state`.
2. Compares it with the session value.
3. Rejects mismatched state values.
4. Creates or retrieves a placeholder local user.
5. Authenticates the user through Laravel.
6. Redirects to the dashboard.

### 3. Open an Organisation Subaccount

The dashboard lists organisations from the database.

Selecting an organisation:

1. Resolves the organisation through route-model binding.
2. Stores its ID in the session.
3. Returns a JSON response containing the selected organisation and dashboard redirect.

This is a context-switching scaffold. It does not yet implement organisation-scoped authorisation, data isolation, impersonation auditing, or tenant-aware queries.

## Routes

| Method | Endpoint | Access | Purpose |
|---|---|---|---|
| `GET` | `/` | Public | Display the login page |
| `GET` | `/oidc/redirect` | Public | Begin the stubbed OIDC authorisation flow |
| `GET` | `/oidc/callback` | Public | Validate state and complete the stubbed login |
| `POST` | `/logout` | Authenticated session | End the Laravel session |
| `GET` | `/dashboard` | OIDC middleware | Display organisation subaccounts |
| `POST` | `/subaccount/{organization}` | OIDC middleware | Store the selected organisation in the session |
| `GET` | `/metrics` | Public in the current scaffold | Return application counts and timestamp |
| `GET` | `/up` | Public | Laravel health endpoint |

## Metrics Response

A successful metrics response follows this structure:

```json
{
  "status": "ok",
  "metrics": {
    "active_users": 1,
    "organizations": 3,
    "timestamp": "2026-01-01T12:00:00+00:00"
  }
}
```

The default organisation seeder creates:

- Acme Corp
- Globex Inc
- Initech

> The metrics endpoint is currently public. Protect it before exposing operational or tenant information in a real environment.

## Prerequisites

Install:

- PHP 8.2 or later
- Composer
- Node.js and npm
- SQLite, MySQL, or PostgreSQL
- Git

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/josiahking/oidc_adminhub.git
cd oidc_adminhub
```

### 2. Install dependencies

```bash
composer install
npm install
```

### 3. Configure the environment

```bash
cp .env.example .env
php artisan key:generate
```

For SQLite:

```bash
touch database/database.sqlite
```

Then configure:

```dotenv
APP_NAME="OIDC Admin Hub"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=sqlite

OIDC_PROVIDER_URL=https://identity-provider.example.com/oauth/authorize
OIDC_CLIENT_ID=your_client_id
OIDC_CLIENT_SECRET=your_client_secret
OIDC_REDIRECT_URI=http://127.0.0.1:8000/oidc/callback
```

For MySQL:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_admin_hub
DB_USERNAME=root
DB_PASSWORD=
```

> Add the OIDC variables to `.env.example` as part of the next repository cleanup so new contributors can discover the required configuration.

### 4. Run migrations

```bash
php artisan migrate
```

### 5. Seed organisations

```bash
php artisan db:seed --class=OrganizationSeeder
```

### 6. Start development mode

Run Laravel, the queue listener, and Vite together:

```bash
composer dev
```

Or use separate terminals:

```bash
php artisan serve
```

```bash
npm run dev
```

The application will normally be available at:

```text
http://127.0.0.1:8000
```

## Testing

Run the test suite:

```bash
composer test
```

Or:

```bash
php artisan test
```

The current feature test verifies that the login page responds successfully.

Before treating the project as complete, add tests for:

- OIDC state generation
- Invalid callback state rejection
- Successful callback handling
- Protected dashboard access
- Logout
- Organisation listing
- Organisation-context switching
- Unknown organisation handling
- Metrics response structure
- Metrics access control

## Code Quality

Format PHP:

```bash
vendor/bin/pint
```

Build frontend assets:

```bash
npm run build
```

Run Vite development mode:

```bash
npm run dev
```

## Current OIDC Limitations

The repository does **not** yet implement a complete OpenID Connect client.

Missing production requirements include:

- Discovery through `/.well-known/openid-configuration`
- Authorisation-code exchange
- PKCE
- ID-token signature verification
- Issuer validation
- Audience validation
- Expiration and nonce validation
- JWKS retrieval and key rotation
- UserInfo retrieval
- Provider error handling
- Refresh-token handling
- Logout at the identity provider
- Account-linking rules
- Role and group claim mapping

The current redirect also uses scaffold values rather than a fully implemented provider client.

## Security and Multi-Tenant Considerations

Before using this pattern in production:

- Use a maintained OIDC client library
- Require HTTPS
- Use PKCE and nonce validation
- Rotate session IDs after authentication
- Regenerate or remove consumed state values
- Add rate limiting
- Protect the metrics endpoint
- Add role- and permission-based access control
- Validate organisation membership before switching context
- Apply organisation scoping to every tenant-owned query
- Prevent insecure direct-object references
- Audit every organisation-context switch
- Display the active organisation clearly
- Add session timeout and reauthentication rules
- Encrypt sensitive session data
- Store identity-provider subject identifiers
- Avoid relying on email alone for account identity
- Add centralised logging and alerting
- Add security and integration tests

## Project Status

OIDC Admin Hub is a portfolio scaffold demonstrating:

- Laravel 12 application structure
- Authentication-flow modelling
- OIDC state validation
- Laravel session authentication
- Middleware-protected routes
- Organisation records and seeded data
- Session-based organisation context
- JSON metrics contracts
- Blade and Tailwind interfaces

It should be reviewed as an architectural prototype, not as a production identity or multi-tenant platform.

## Roadmap

### Real OIDC Integration

- Add discovery-document support
- Implement authorisation-code exchange
- Add PKCE and nonce validation
- Validate ID tokens
- Retrieve and map provider user claims
- Add provider logout
- Add integration tests with Keycloak or Auth0

### Organisation Access

- Add organisation memberships
- Add roles and permissions
- Enforce organisation-level data scoping
- Add context-switch audit history
- Add tenant-aware policies
- Add an active-organisation indicator

### Platform Engineering

- Protect and version the metrics endpoint
- Add Sanctum-protected API access
- Expand automated tests
- Add GitHub Actions
- Add Docker development support
- Add structured logging
- Add production monitoring
- Add deployment documentation
- Add verified screenshots

## Author

**Josiah Gerald**

Senior Backend Engineer specialising in PHP, Laravel, REST APIs, payment integrations, WordPress, and production business platforms.

- GitHub: [github.com/josiahking](https://github.com/josiahking)
- LinkedIn: [linkedin.com/in/josiah-g-0919763b](https://www.linkedin.com/in/josiah-g-0919763b/)

## License

This project is available under the [MIT License](LICENSE).
