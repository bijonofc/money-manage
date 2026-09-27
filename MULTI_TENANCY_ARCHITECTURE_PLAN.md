# Multi-Tenancy Architecture Plan (Hybrid B2B & B2C)
**Project:** Money Manage  
**Document Version:** 1.0.0  
**Date:** September 2026  
**Status:** Approved for Implementation (Option 1 Immediate, Option 2 Future-Ready)

---

## 1. Executive Summary & Vision

### The Goal
Transform **Money Manage** from a single-user personal finance tracker into a robust, enterprise-ready **Hybrid Multi-Tenant SaaS Platform**. The platform will support two distinct financial management scenarios under a single codebase and database:

1. **B2C (Individual Subscribers):** Personal finance management where a single user manages their own wallets, incomes, expenses, budgets, and savings goals.
2. **B2B (Companies / Organizations):** Organizational finance management where a Company/Business has its own financial entity (Tenant) and multiple users/staff (e.g., Owner, Manager, Accountant) collaborating on the same company ledger with distinct permission levels.

---

## 2. Core Architectural Decisions

### 2.1 The Two Options Strategy
* **Option 1 (Immediate Implementation):** Single domain / single portal deployment.
  - Every tenant and user accesses the app via the primary domain (e.g., `app.domain.com`).
  - Tenant resolution is handled via the authenticated user's profile (`auth()->user()->tenant_id`).
  - Separation between Personal Accounts and Company Accounts is strictly enforced.
* **Option 2 (Future-Ready Subdomain Architecture):** Subdomain-based routing (e.g., `acme.moneymanage.com`).
  - Option 1 is designed from Day 1 with a `slug` column in the `tenants` table.
  - When Option 2 is activated in the future, only a subdomain resolver middleware is needed—**no rewrites of business logic, models, or transactions will be required**.

### 2.2 Global Email Uniqueness Invariant
* **Strict Rule:** Every email address in the system is **globally unique**.
* An email address represents **one single identity**:
  - It belongs strictly to an **Individual Personal Subscriber**, OR
  - It belongs strictly to a **Company Owner / Staff Member**.
* A user cannot use the exact same email for both a personal account and a company account at the same time. If someone wants separate accounts, they must use separate emails (e.g., `personal@gmail.com` vs `work@company.com`).

### 2.3 Identity vs Ownership Separation
| Entity | Concept | Responsibility in System |
| :--- | :--- | :--- |
| **Tenant** | Financial / Accounting Boundary (Workspace) | Owns Accounts, Categories, Budgets, Recurring Transactions, and Debts. |
| **User** | Identity / Actor | Authenticates, enters records, triggers workflows. Holds a role with specific permissions. |
| **Transaction** | Financial Record | Belongs to **Tenant** (`tenant_id`: whose money) and recorded by **User** (`user_id`: who logged it). |

---

## 3. Option 1: Detailed Architectural Design (Immediate)

### 3.1 Database Schema Additions & Modifications

```
                              ┌───────────────────────────────────────┐
                              │            tenants                    │
                              ├───────────────────────────────────────┤
                              │ id               (PK, BigInt)         │
                              │ name             (String, 150)        │
                              │ slug             (String, 100, Unique)│ <-- Future Subdomain Bridge!
                              │ type             (Enum: personal,     │
                              │                        company)       │
                              │ owner_id         (FK to users.id)     │
                              │ status           (Enum: A, I, P)      │
                              │ meta             (JSON, nullable)     │
                              │ created_at, updated_at                │
                              └───────────────────┬───────────────────┘
                                                  │ 1
                                                  │
                                                  │ N
                              ┌───────────────────┴───────────────────┐
                              │             users                     │
                              ├───────────────────────────────────────┤
                              │ id               (PK, BigInt)         │
                              │ tenant_id        (FK to tenants.id)   │ <-- NEW COLUMN
                              │ account_type     (Enum: personal,     │ <-- NEW COLUMN
                              │                   company_owner,      │
                              │                   company_staff)      │
                              │ name, email (Unique), username...     │
                              │ role_id          (FK to roles.id)     │
                              │ status           (Enum: A, I, P)      │
                              └───────────────────┬───────────────────┘
                                                  │
                                  ┌───────────────┴───────────────┐
                                  │                               │
                                  ▼                               ▼
                 ┌────────────────────────────────┐ ┌────────────────────────────────┐
                 │          accounts              │ │          transactions          │
                 ├────────────────────────────────┤ ├────────────────────────────────┤
                 │ id                             │ │ id                             │
                 │ tenant_id (FK to tenants.id)   │ │ tenant_id (FK to tenants.id)   │
                 │ name, balance, currency...     │ │ user_id   (FK to users.id)     │
                 └────────────────────────────────┘ │ amount, type, date...          │
                                                    └────────────────────────────────┘
```

#### 1. New Table: `tenants`
```php
Schema::create('tenants', function (Blueprint $table) {
    $table->id();
    $table->string('name', 150);
    $table->string('slug', 100)->unique(); // Used for identification & future subdomains
    $table->enum('type', ['personal', 'company'])->default('personal');
    $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
    $table->enum('status', ['A', 'I', 'P'])->default('A');
    $table->json('settings')->nullable();
    $table->timestamps();

    $table->index(['type', 'status']);
});
```

#### 2. Modified Table: `users`
```php
Schema::table('users', function (Blueprint $table) {
    $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->nullOnDelete();
    $table->enum('account_type', ['personal', 'company_owner', 'company_staff'])->default('personal')->after('role_id');
});
```

#### 3. Foreign Key Adjustments on Financial Tables
The existing `tenant_id` columns in `accounts`, `categories`, `transactions`, `recurring_transactions`, `budgets`, `budget_alerts`, `savings_goals`, `debts`, `app_settings`, and `activity_logs` will have their foreign key references pointed to `tenants(id)` instead of `users(id)`.

---

### 3.2 Data Scoping & Trait Logic (`BelongsToTenant`)

Update [app/Traits/BelongsToTenant.php](file:///c:/wamp64/www/money-manage/app/Traits/BelongsToTenant.php) to resolve `tenant_id` from the active user's assigned tenant:

```php
<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $builder->where($builder->getModel()->getTable() . '.tenant_id', auth()->user()->tenant_id);
            }
        });

        static::creating(function (Model $model) {
            if (auth()->check() && empty($model->tenant_id)) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }
}
```

---

### 3.3 Role & Permission Hierarchy

| Role Slug | Scope | Permissions & Capabilities |
| :--- | :--- | :--- |
| `super-admin` | Global Platform | Manages all tenants, system settings, approval of new company/personal registrations, platform health. |
| `admin` (or `company-admin`) | Tenant-level | Full control over the company tenant: add/remove staff, manage accounts, view all reports, edit company settings. |
| `manager` | Tenant-level | Manage transactions, budgets, view accounts and reports; cannot delete accounts or manage company users. |
| `accountant` (or `staff`) | Tenant-level | Record daily income/expenses, view categories and accounts. Cannot modify settings or delete master data. |
| `customer` (Personal User) | Tenant-level | Full control over their personal single-user tenant workspace. |

---

### 3.4 User Registration & Onboarding Workflows

#### Scenario A: Personal Subscriber Registration
1. User provides: `name`, `email`, `username`, `password`.
2. Form selects / defaults to **Account Type: Personal**.
3. Registration Controller:
   - Verifies email is globally unique.
   - Creates a Tenant:
     - `name`: `"{name}'s Personal Account"`
     - `slug`: `Str::slug($username . '-' . Str::random(4))`
     - `type`: `'personal'`
   - Creates User:
     - `tenant_id`: newly created tenant ID
     - `account_type`: `'personal'`
     - `role_id`: 4 (Customer)
     - `status`: `'A'` (or `'P'` if admin approval required)
   - Links tenant `owner_id` to new user.
   - Seeds default personal categories via `CategoryService`.

#### Scenario B: Company / Organization Registration
1. User provides: `company_name`, `name` (Contact Person), `email`, `username`, `password`.
2. Form selects **Account Type: Company / Business**.
3. Registration Controller:
   - Verifies email is globally unique.
   - Generates unique slug from `company_name` (e.g., `acme-holdings`).
   - Creates a Tenant:
     - `name`: `"Acme Holdings Ltd"`
     - `slug`: `'acme-holdings'`
     - `type`: `'company'`
   - Creates User:
     - `tenant_id`: newly created tenant ID
     - `account_type`: `'company_owner'`
     - `role_id`: 2 (Admin)
   - Links tenant `owner_id` to new user.
   - Seeds default business categories via `CategoryService`.

#### Scenario C: Company Staff Invitation / Creation
1. Company Owner/Admin opens "Users" module inside their dashboard.
2. Clicks "Add Staff Member":
   - Inputs: Name, Email (must be unique), Role (`manager` or `accountant`), Password.
3. System automatically sets:
   - `tenant_id = auth()->user()->tenant_id`
   - `account_type = 'company_staff'`
4. When the staff member logs in:
   - They enter the exact same company environment.
   - All transactions they log automatically carry `tenant_id = Company ID` and `user_id = Staff User ID`.

---

## 4. Option 2: Future Subdomain Architecture (Seamless Upgrade)

Because Option 1 includes the `slug` column in `tenants`, upgrading to Subdomain Tenancy in the future requires **zero disruption to core business logic**:

### 4.1 How It Works
1. **DNS Wildcard Routing:**
   - Configure DNS: `*.moneymanage.com` -> Points to Server IP.
   - Configure Nginx / Apache: VirtualHost handles `*.moneymanage.com`.
2. **Subdomain Identification Middleware:**
   ```php
   // app/Http/Middleware/ResolveTenantBySubdomain.php
   public function handle(Request $request, Closure $next)
   {
       $host = $request->getHost(); // e.g. "acme.moneymanage.com"
       $parts = explode('.', $host);
       
       if (count($parts) >= 3 && $parts[0] !== 'www' && $parts[0] !== 'app') {
           $slug = $parts[0];
           $tenant = Tenant::where('slug', $slug)->where('status', 'A')->firstOrFail();
           
           // Store globally in container
           app()->instance('currentTenant', $tenant);
       }
       
       return $next($request);
   }
   ```
3. **Session & Cookie Sharing:**
   - In `.env`: `SESSION_DOMAIN=.moneymanage.com`
   - Single sign-on across the organization's domain.

---

## 5. Step-by-Step Implementation Roadmap

```
  Phase 1: DB & Migration     Phase 2: Trait & Scoping      Phase 3: Services & Auth
  ┌──────────────────────┐    ┌──────────────────────┐    ┌──────────────────────┐
  │ Create tenants table │───▶│ BelongsToTenant trait│───▶│ AuthController       │
  │ Add tenant_id to user│    │ Models relationships │    │ Registration logic   │
  │ Backfill existing db │    │ Global scopes update │    │ TransactionService   │
  └──────────────────────┘    └──────────────────────┘    └──────────────────────┘
                                                                     │
                                                                     ▼
  Phase 6: QA & Testing       Phase 5: Staff Management   Phase 4: Controllers & API
  ┌──────────────────────┐    ┌──────────────────────┐    ┌──────────────────────┐
  │ Multi-user isolation │◀───│ Team member invite/  │◀───│ Dashboard, Accounts, │
  │ Audit trail check    │    │ Staff CRUD per tenant│    │ Categories, Reports  │
  │ Email uniqueness test│    │ Role assignments     │    │ Settings cleanup     │
  └──────────────────────┘    └──────────────────────┘    └──────────────────────┘
```

### Phase 1: Database Migration & Zero-Loss Data Backfill
* Create `create_tenants_table` migration.
* Add `tenant_id` and `account_type` to `users` table.
* **Backfill Script:** For every existing user in `users`, automatically create a corresponding `personal` record in `tenants` matching their current `id`, ensuring all existing transactions, accounts, and categories remain 100% intact.
* Update foreign keys on all domain tables to reference `tenants(id)`.

### Phase 2: Models & Scoping Overhaul
* Create [app/Models/Tenant.php](file:///c:/wamp64/www/money-manage/app/Models/Tenant.php).
* Update [app/Traits/BelongsToTenant.php](file:///c:/wamp64/www/money-manage/app/Traits/BelongsToTenant.php) to filter by `auth()->user()->tenant_id`.
* Update [app/Models/User.php](file:///c:/wamp64/www/money-manage/app/Models/User.php) with `tenant(): BelongsTo` relationship.

### Phase 3: Service Layer Alignment
* **`TransactionService`:** Record `tenant_id = auth()->user()->tenant_id` and `user_id = auth()->id()`.
* **`DashboardService`:** Query dashboard metrics by `$user->tenant_id` instead of `$userId`.
* **`CategoryService`:** Seed default categories per `tenant_id`.
* **`SettingService`:** Fetch tenant-specific settings with fallback to global settings.
* **`ActivityLogger`:** Record `tenant_id = auth()->user()->tenant_id` and `user_id = auth()->id()`.

### Phase 4: Authentication & Registration (Personal vs Company)
* Update [AuthController::register](file:///c:/wamp64/www/money-manage/app/Http/Controllers/Api/AuthController.php) to accept `account_type` (`personal` vs `company`).
* Validate global email uniqueness.
* Create corresponding Tenant and User records with appropriate roles.

### Phase 5: Team / Staff Management UI & API
* Ensure [UserController::store](file:///c:/wamp64/www/money-manage/app/Http/Controllers/Api/UserController.php) scopes created users to `auth()->user()->tenant_id` if the logged-in user is a Company Admin.
* Allow Company Admins to manage staff accounts within their company.
* Prevent cross-tenant user visibility in the User List.

### Phase 6: Quality Assurance & Edge Case Verification
* Verify Personal Subscriber isolation (User A cannot see User B's accounts).
* Verify Company Multi-User Collaboration (Staff B sees Company A's accounts; transaction author is recorded as Staff B).
* Verify Global Email Uniqueness (Attempting to register an existing email under either account type returns a clean validation error).
* Ensure Super Admin retains full visibility across the platform.

---

## 6. Conclusion
By following this plan, **no frontend components need to be discarded or rewritten**, existing database data is preserved without loss, and Money Manage transforms into a clean, modern SaaS architecture ready for immediate single-domain launch (Option 1) and seamless future subdomain scaling (Option 2).
