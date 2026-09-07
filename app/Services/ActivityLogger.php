<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ActivityLogger
{
    /**
     * Create an activity log record.
     */
    public static function log(
        string $event,
        string $des,
        array $desParam = [],
        ?string $subjectType = null,
        ?int $subjectId = null,
        ?int $userId = null,
        ?int $tenantId = null,
        ?array $properties = null,
        ?string $url = null,
        ?string $ipAddress = null
    ): ?ActivityLog {
        try {
            $user = $userId ? User::find($userId) : Auth::user();
            $effectiveUserId = $user?->id ?? $userId;
            $effectiveTenantId = $tenantId ?? ($user?->id ?? (auth()->id() ?? 1));

            if (! isset($desParam['uname'])) {
                $desParam['uname'] = $user?->name ?? 'System';
            }

            $request = request();
            $ip = $ipAddress;
            if (empty($ip)) {
                $ip = function_exists('appsbd_get_remote_ip') ? appsbd_get_remote_ip() : ($request?->ip() ?? '127.0.0.1');
            }

            $currentUrl = $url ?? ($request ? $request->fullUrl() : '');
            $userAgent = $request ? $request->userAgent() : '';

            return ActivityLog::create([
                'tenant_id' => $effectiveTenantId,
                'user_id' => $effectiveUserId,
                'event' => $event,
                'log_type' => $subjectType ? strtolower(class_basename($subjectType)) : null,
                'subject_type' => $subjectType,
                'subject_id' => $subjectId,
                'des' => $des,
                'des_param' => $desParam,
                'ip_address' => $ip,
                'user_agent' => $userAgent,
                'url' => $currentUrl,
                'properties' => $properties,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to log activity: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Log user login.
     */
    public static function logLogin(User $user, string $method = 'standard'): ?ActivityLog
    {
        $uname = $user->name ?? $user->username ?? 'User';

        return self::log(
            event: 'login',
            des: 'act.login',
            desParam: ['uname' => $uname],
            subjectType: get_class($user),
            subjectId: $user->id,
            userId: $user->id,
            tenantId: $user->id,
            properties: ['method' => $method]
        );
    }

    /**
     * Log user logout.
     */
    public static function logLogout(?User $user = null): ?ActivityLog
    {
        $user = $user ?? Auth::user();
        $uname = $user?->name ?? $user?->username ?? 'User';

        return self::log(
            event: 'logout',
            des: 'act.logout',
            desParam: ['uname' => $uname],
            subjectType: $user ? get_class($user) : null,
            subjectId: $user?->id,
            userId: $user?->id,
            tenantId: $user?->id
        );
    }

    /**
     * Log model creation.
     */
    public static function logCreated($model, ?string $label = null): ?ActivityLog
    {
        $user = Auth::user();
        $uname = $user?->name ?? 'User';
        $modelName = self::getModelDisplayName($model);
        $displayName = $label ?? self::getItemLabel($model);

        return self::log(
            event: 'created',
            des: 'act.cr',
            desParam: [
                'uname' => $uname,
                'model' => "{$modelName}: {$displayName}",
            ],
            subjectType: get_class($model),
            subjectId: $model->id,
            userId: $user?->id,
            tenantId: $model->tenant_id ?? ($user?->id ?? 1),
            properties: $model->toArray()
        );
    }

    /**
     * Log model update.
     */
    public static function logUpdated($model, ?string $label = null, ?array $changes = null): ?ActivityLog
    {
        $user = Auth::user();
        $uname = $user?->name ?? 'User';
        $modelName = self::getModelDisplayName($model);
        $displayName = $label ?? self::getItemLabel($model);

        return self::log(
            event: 'updated',
            des: 'act.up',
            desParam: [
                'uname' => $uname,
                'model' => "{$modelName}: {$displayName}",
            ],
            subjectType: get_class($model),
            subjectId: $model->id,
            userId: $user?->id,
            tenantId: $model->tenant_id ?? ($user?->id ?? 1),
            properties: $changes ?? $model->getChanges()
        );
    }

    /**
     * Log model deletion.
     */
    public static function logDeleted($model, ?string $label = null): ?ActivityLog
    {
        $user = Auth::user();
        $uname = $user?->name ?? 'User';
        $modelName = self::getModelDisplayName($model);
        $displayName = $label ?? self::getItemLabel($model);

        return self::log(
            event: 'deleted',
            des: 'act.del',
            desParam: [
                'uname' => $uname,
                'model' => "{$modelName}: {$displayName}",
            ],
            subjectType: get_class($model),
            subjectId: $model->id,
            userId: $user?->id,
            tenantId: $model->tenant_id ?? ($user?->id ?? 1),
            properties: $model->toArray()
        );
    }

    /**
     * Helper to get human-friendly model name.
     */
    private static function getModelDisplayName($model): string
    {
        $base = class_basename($model);

        return match ($base) {
            'Account' => 'Account',
            'Category' => 'Category',
            'Transaction' => 'Transaction',
            'Budget' => 'Budget',
            'SavingsGoal' => 'Savings Goal',
            'SavingsContribution' => 'Savings Contribution',
            'Debt' => 'Debt',
            'DebtPayment' => 'Debt Payment',
            'User' => 'User',
            'Role' => 'Role',
            'AppSetting' => 'Setting',
            default => $base,
        };
    }

    /**
     * Helper to get representative item label.
     */
    private static function getItemLabel($model): string
    {
        if (isset($model->title) && ! empty($model->title)) {
            return (string) $model->title;
        }
        if (isset($model->name) && ! empty($model->name)) {
            return (string) $model->name;
        }
        if (isset($model->creditor_name) && ! empty($model->creditor_name)) {
            return (string) $model->creditor_name;
        }
        if (isset($model->description) && ! empty($model->description)) {
            return (string) $model->description;
        }

        return '#'.($model->id ?? 'unknown');
    }
}
