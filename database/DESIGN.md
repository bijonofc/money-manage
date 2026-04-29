# Money Manage - SaaS-Ready Database Design

## Architectural Vision: "Build Simple Now, Scale Later"

This system is currently designed for single-user personal finance management but structured to easily transition into a multi-tenant SaaS application in the future. 

### The Hybrid Tenant Strategy
Right now, **every user is treated internally as their own "Tenant" (1:1 mapping)**. 
- `tenant_id` exists on all domain tables and currently references `users.id`.
- When transitioning to full SaaS later, we will simply create a `tenants` (or `workspaces`) table, point the foreign keys there, and introduce a `user_tenant` pivot table for multi-user support. The transition will require minimal schema refactoring.

### Why this approach?
1. **No Overengineering**: We don't need a `tenants` table, team invites, or complex ACLs right now.
2. **Easy Migration**: When we need multi-user capabilities, the boundary (`tenant_id`) is already established. We just change what `tenant_id` references.
3. **Data Integrity**: We can implement a global `TenantScope` in Laravel from day one.

---

## Core Tables

### `users` (Existing)
*Keeps the user identity. Later, we can add `current_tenant_id` when users belong to multiple tenants.*
```
id              BIGINT PRIMARY KEY
name            VARCHAR(255)
email           VARCHAR(255) UNIQUE
password        VARCHAR(255)
role_id         BIGINT (FK -> roles.id)
created_at      TIMESTAMP
```

### `accounts`
*Uses `tenant_id` to define ownership. Includes JSON `meta` to handle Credit Card logic without cluttering the table.*
```
id              BIGINT PRIMARY KEY
tenant_id       BIGINT NOT NULL (FK -> users.id for now)
name            VARCHAR(100) NOT NULL       -- e.g., "Cash", "Bank Account", "bKash"
account_type    ENUM('cash', 'bank', 'mobile', 'credit_card', 'other')
account_number  VARCHAR(50) NULL            
balance         DECIMAL(15,2) DEFAULT 0.00  -- Negative balance represents liability/credit used
currency        VARCHAR(3) DEFAULT 'BDT'
meta            JSON NULL                   -- Prepares for SaaS: credit_limit, billing_cycle_day, payment_due_day
is_active       BOOLEAN DEFAULT TRUE
description     TEXT NULL
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

### `categories`
```
id              BIGINT PRIMARY KEY
tenant_id       BIGINT NOT NULL (FK -> users.id for now)
name            VARCHAR(100) NOT NULL       
type            ENUM('income', 'expense')
parent_id       BIGINT NULL (FK -> categories.id)  
icon            VARCHAR(50) NULL            
color           VARCHAR(20) NULL            
is_system       BOOLEAN DEFAULT FALSE       
is_active       BOOLEAN DEFAULT TRUE
created_at      TIMESTAMP
```

### `transactions`
*Uses BOTH `tenant_id` (for data isolation) and `user_id` (actor who made the change). Prepares for splitting via `parent_id`.*
```
id                  BIGINT PRIMARY KEY
tenant_id           BIGINT NOT NULL (FK -> users.id for now) -- Workspace isolation
user_id             BIGINT NOT NULL (FK -> users.id)         -- Who performed the action
parent_id           BIGINT NULL (FK -> transactions.id)      -- For future split transactions
transaction_type    ENUM('income', 'expense', 'transfer')
amount              DECIMAL(15,2) NOT NULL
category_id         BIGINT (FK -> categories.id)
account_id          BIGINT NOT NULL (FK -> accounts.id)      -- Target/Main account
from_account_id     BIGINT NULL (FK -> accounts.id)          -- For transfers (Source)
date                DATE NOT NULL
description         TEXT NULL
reference_number    VARCHAR(100) NULL         
payment_method      ENUM('cash', 'card', 'bank_transfer', 'mobile', 'check', 'other')
is_recurring        BOOLEAN DEFAULT FALSE
recurring_id        BIGINT NULL (FK -> recurring_transactions.id)
tags                JSON NULL                 
attachment_path     VARCHAR(255) NULL         
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

*(Other tables like `budgets`, `savings_goals`, `debts`, etc., follow the exact same pattern: replace `user_id` with `tenant_id`).*

---

## Design Decisions for Scalability

### 1. The Credit Card System
**Goal:** Work for a single user now, scale to SaaS later.
**Implementation:**
- Use `account_type = 'credit_card'`.
- Store configuration in the `meta` JSON column: `{"credit_limit": 50000, "billing_cycle_day": 15, "payment_due_day": 5}`.
- **Liability Tracking**: When an expense is made via the credit card, the `balance` goes *negative*.
- **Payments**: Paying the credit card bill is a `transfer` from a Bank Account to the Credit Card account, which increases the credit card balance back towards zero.

### 2. The Transaction System
- **Splitting**: We introduced a nullable `parent_id` on the `transactions` table. We won't build UI for it yet, but the database is ready.
- **Double-Entry Accounting**: Full double-entry (journals, ledgers) is overengineering for personal finance. We use a hybrid: `account_id` and `from_account_id` on a single row for transfers. If strict compliance is needed later, we can map `transactions` to a new `journal_entries` table.

### 3. Laravel Implementation Strategies
- **Global Scope**: Create a `TenantScope` trait and apply it to all models (Accounts, Transactions, etc.).
- **Context Binding**: In a service provider or middleware, bind the current tenant context: `app()->instance('tenant.id', auth()->id());`. The scope automatically filters queries by `app('tenant.id')`.
- **Avoid Tight Coupling**: Never write `where('user_id', auth()->id())` in controllers. Rely on the `TenantScope` and relationships: `auth()->user()->accounts`.

## What NOT to Build Right Now
1. **A `tenants` or `workspaces` table**: Treat `users` as tenants.
2. **Team Invite System**: Assume 1 user = 1 tenant for now.
3. **Roles within a Workspace**: Standard RBAC is enough for now. Workspace-specific roles can wait.
4. **Strict Double-Entry Ledgers**: Start with the simple `transactions` table.
5. **Complex Billing/Subscription logic**: Only build this when launching the SaaS version.
