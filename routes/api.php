<?php

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BudgetController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DebtController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\SavingsGoalController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (/api/v1)
|--------------------------------------------------------------------------
*/

// Authentication
Route::post('user/login', [AuthController::class, 'login']);
Route::post('user/social-login', [AuthController::class, 'socialLogin']);
Route::post('user/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('user/resend-otp', [AuthController::class, 'resendOtp']);
Route::post('user/logout', [AuthController::class, 'logout']);
Route::get('user/profile', [AuthController::class, 'profile']);
Route::patch('user/update', [AuthController::class, 'updateProfile']);

// Dashboard / System Initial Data
Route::get('initial-data', [DashboardController::class, 'initialData']);
Route::get('notifications', [DashboardController::class, 'notifications']);
Route::post('notifications/list', [DashboardController::class, 'notificationList']);

// Roles & Permissions
Route::get('role-accesses/list', [RoleController::class, 'roleAccessList']);
Route::post('role-accesses/change-permission', [RoleController::class, 'changePermission']);
Route::post('role-accesses/reset-permission', [RoleController::class, 'resetPermission']);
Route::post('role-accesses/copy-permission', [RoleController::class, 'copyPermission']);
Route::post('roles/list', [RoleController::class, 'list']);
Route::apiResource('roles', RoleController::class)->except(['index']);
Route::get('roles', [RoleController::class, 'list']);

// Users
Route::post('users/list', [UserController::class, 'list']);
Route::post('users/change-password', [UserController::class, 'changePassword']);
Route::apiResource('users', UserController::class)->except(['index']);
Route::get('users', [UserController::class, 'list']);

// Accounts
Route::match(['get', 'post'], 'accounts/list', [AccountController::class, 'index']);
Route::apiResource('accounts', AccountController::class);

// Categories
Route::match(['get', 'post'], 'categories/list', [CategoryController::class, 'index']);
Route::apiResource('categories', CategoryController::class);

// Transactions
Route::match(['get', 'post'], 'transactions/list', [TransactionController::class, 'index']);
Route::apiResource('transactions', TransactionController::class);

// Budgets
Route::match(['get', 'post'], 'budgets/list', [BudgetController::class, 'index']);
Route::apiResource('budgets', BudgetController::class);

// Savings Goals
Route::match(['get', 'post'], 'savings-goals/list', [SavingsGoalController::class, 'index']);
Route::post('savings-goals/{id}/contribute', [SavingsGoalController::class, 'contribute']);
Route::apiResource('savings-goals', SavingsGoalController::class);

// Debts
Route::match(['get', 'post'], 'debts/list', [DebtController::class, 'index']);
Route::post('debts/{id}/pay', [DebtController::class, 'pay']);
Route::apiResource('debts', DebtController::class);

// Reports & Analytics
Route::match(['get', 'post'], 'reports/overview', [\App\Http\Controllers\Api\ReportController::class, 'overview']);
Route::match(['get', 'post'], 'reports/export', [\App\Http\Controllers\Api\ReportController::class, 'export']);
