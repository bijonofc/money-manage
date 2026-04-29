# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Money Manage is a Laravel 12 + Vue 3 full-stack application for financial/customer management with an admin dashboard. The frontend uses Vue 3 with Pinia for state management, Vue Router for navigation, and a custom component library (`@appsbd/vue3-*` packages).

## Development Commands

### Setup
```bash
composer install                    # Install PHP dependencies
npm install                         # Install JS dependencies
cp .env.example .env                # Create environment file
php artisan key:generate            # Generate app key
php artisan migrate                 # Run database migrations
npm run build                       # Build frontend assets
```

### Development Server
```bash
composer dev                        # Run server, queue worker, and Vite concurrently
php artisan serve                   # Laravel development server only
npm run dev                         # Vite dev server with hot reload
```

### Testing
```bash
composer test                       # Run PHPUnit tests
php artisan test --filter=Name      # Run specific test
```

### Linting/Formatting
```bash
./vendor/bin/pint                   # Run Laravel Pint (PHP formatter)
```

## Architecture

### Backend (Laravel 12)
- **Entry Point**: `public/index.php` → `bootstrap/app.php`
- **Routing**: `routes/web.php` - SPA-style catch-all routes serving Vue app
- **Controllers**: Minimal - extends `App\Http\Controllers\Controller`
- **Models**: Located in `app/Models/`, use `appsbd\Core\AppModel` base class
- **Helpers**: `app/Helpers/helpers.php` (global helpers), `app/Helpers/BanglaHelper.php`
- **Services**: `app/Services/` - business logic layer (e.g., `SettingService`)
- **ACL System**: Role-based access via `Role` and `RoleAccess` models with `is_super` flag for super-admin

### Frontend (Vue 3)
- **Entry**: `resources/js/app.js` - initializes Vue app with plugins
- **Router**: `resources/js/router/AdminRoutes.js` - all admin routes under `/admin/*`
- **State**: Pinia stores in `resources/js/modules/*/Store.js` files
- **Components**: `resources/js/components/` (shared), `resources/js/modules/` (feature-specific)
- **Path Alias**: `@` resolves to `resources/js/`
- **Build**: Vite outputs to `public/build/`

### Key Frontend Libraries
- **State Management**: Pinia with persistence plugin
- **Routing**: Vue Router with `requiresAuth` meta guards
- **HTTP**: Axios via `AppsbdAxiosHelper`
- **UI**: Bootstrap 5, `@appsbd/vue3-elite-grid`, `floating-vue`
- **Forms**: VeeValidate for validation
- **Notifications**: vue-toastification, vue-sweetalert2

### Database
- MySQL with Laravel migrations in `database/migrations/`
- Seeders in `database/seeders/` (RoleSeeder, UserSeeder)
- Core tables: `users`, `roles`, `role_accesses`, `cache`, `jobs`

### Authentication
- Session-based auth with `useLoginStore()` Pinia store
- Route guards check `requiresAuth` meta in `AdminRoutes.js`
- Google OAuth via `vue3-google-login`
- Cloudflare Turnstile captcha support

## Common Patterns

### API Calls
Frontend uses `appbdURLs.route()` helper for URL generation:
```js
const url = appbdURLs.route('admin/customer/list');
```

### ACL Checks
```js
import ACL from '@/libs/acl.js';
ACL.checkACL('role-list'); // Returns boolean
```

### Settings
Backend: `app_setting($key)` or `get_option($key)` / `set_option($key, $value)`
Frontend: Via Pinia stores or `app_settings` global

## File Structure
```
├── app/
│   ├── Helpers/       # Global helper functions
│   ├── Models/        # Eloquent models
│   └── Providers/     # Service providers
├── resources/js/
│   ├── components/    # Shared Vue components
│   ├── layouts/       # Layout components
│   ├── libs/          # Utility libraries
│   ├── modules/       # Feature modules (AdminPanel, etc.)
│   └── router/        # Vue Router configuration
├── routes/
│   └── web.php        # Web routes (SPA catch-all)
├── database/
│   ├── migrations/    # Database migrations
│   └── seeders/       # Database seeders
└── public/            # Web root (compiled assets)
```

## SaaS-Ready Architecture (Future Proofing)

The system is designed with a "Build Simple Now, Scale Later" approach. It currently operates for a single-user but is structured to easily transition to multi-tenant SaaS.

### Tenant Strategy
- **Internal Tenants**: Every user is currently treated as their own tenant.
- **`tenant_id`**: All domain models (`Account`, `Category`, `Transaction`, etc.) use `tenant_id` instead of `user_id` to indicate workspace ownership. For now, `tenant_id` references `users.id`.
- **Transitions**: When transitioning to a full SaaS model, a `tenants` table will be introduced, and `tenant_id` foreign keys will simply be repointed. No massive data migrations will be needed for existing user boundaries.
- **Data Isolation**: Always scope queries using the tenant. Avoid querying globally where possible.

### Development Guidelines for Domain Models
- Use `$table->foreignId('tenant_id')->constrained('users')` in migrations to define ownership.
- Use the `tenant()` relationship in models instead of `user()`.
- For models where the *actor* matters (like `Transaction`), use both `tenant_id` (Workspace) and `user_id` (Actor).

### Credit Card Implementation
- Stored as `account_type = 'credit_card'`.
- Use the `meta` JSON column to store `credit_limit`, `billing_cycle_day`, and `payment_due_day`.
- Expenses push the balance into the negative. Payments towards the card (transfers from Bank to Credit Card) move the balance towards zero.
