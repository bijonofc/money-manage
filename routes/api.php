<?php

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\ActivityLogController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BudgetController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DebtController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\SavingsGoalController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\TestMailController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\UserController;
use appsbd\Libs\AppRoute;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (/api/v1)
|--------------------------------------------------------------------------
*/

// System / Utilities
Route::get('test-email', [TestMailController::class, 'send']);

// Authentication
Route::post('user/register', [AuthController::class, 'register']);
Route::post('user/login', [AuthController::class, 'login']);
Route::post('user/social-login', [AuthController::class, 'socialLogin']);
Route::post('user/forget-password', [AuthController::class, 'forgetPassword']);
Route::post('forget-password', [AuthController::class, 'forgetPassword']);
Route::post('user/reset-password', [AuthController::class, 'resetPassword']);
Route::post('users/reset-password', [AuthController::class, 'resetPassword']);
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
Route::match(['get', 'post'], 'role-accesses/list', [RoleController::class, 'roleAccessList']);
Route::post('role-accesses/change-permission', [RoleController::class, 'changePermission']);
Route::post('role-accesses/reset-permission', [RoleController::class, 'resetPermission']);
Route::post('role-accesses/copy-permission', [RoleController::class, 'copyPermission']);
Route::post('roles/list', [RoleController::class, 'list']);
Route::get('roles', [RoleController::class, 'list']);
Route::apiResource('roles', RoleController::class)->except(['index']);

// Users
Route::post('users/list', [UserController::class, 'list']);
Route::post('users/change-password', [UserController::class, 'changePassword']);
Route::post('users/{id}/approve', [UserController::class, 'approve']);
Route::get('users', [UserController::class, 'list']);
Route::apiResource('users', UserController::class)->except(['index']);

// Accounts
AppRoute::apiResource('accounts', AccountController::class);

// Categories
AppRoute::apiResource('categories', CategoryController::class);

// Transactions
AppRoute::apiResource('transactions', TransactionController::class);

// Budgets
AppRoute::apiResource('budgets', BudgetController::class);

// Savings Goals
Route::post('savings-goals/{id}/contribute', [SavingsGoalController::class, 'contribute']);
AppRoute::apiResource('savings-goals', SavingsGoalController::class);

// Debts
Route::post('debts/{id}/pay', [DebtController::class, 'pay']);
AppRoute::apiResource('debts', DebtController::class);

// Reports & Analytics
Route::match(['get', 'post'], 'reports/overview', [ReportController::class, 'overview']);
Route::match(['get', 'post'], 'reports/export', [ReportController::class, 'export']);

// Activity Logs
Route::match(['get', 'post'], 'activity/list', [ActivityLogController::class, 'list']);
Route::get('activity/{id}', [ActivityLogController::class, 'show']);
Route::delete('activity/{id}', [ActivityLogController::class, 'destroy']);

// Settings
Route::match(['get', 'post'], 'settings/list', [SettingController::class, 'list']);
Route::match(['get', 'post'], 'settings/save', [SettingController::class, 'save']);
Route::post('settings', [SettingController::class, 'save']);
Route::apiResource('settings', SettingController::class)->except(['store']);
