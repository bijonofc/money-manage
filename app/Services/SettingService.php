<?php

namespace App\Services;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    /**
     * Default application settings.
     */
    public const DEFAULTS = [
        'general_settings' => [
            'app_name' => 'Money Manage',
            'currency_symbol' => '৳',
            'currency_code' => 'BDT',
            'date_format' => 'YYYY-MM-DD',
            'budget_threshold' => 80,
            'app_logo' => '',
        ],
        'notification_settings' => [
            'overbudget_alerts' => true,
            'debt_reminders' => true,
            'alert_email' => '',
        ],
    ];

    /**
     * Get a setting value by key.
     */
    public function get(string $key, mixed $default = null, ?string $group = null, ?int $tenantId = null): mixed
    {
        $tenantId = $tenantId ?? (auth()->id() ?? 1);
        $cacheKey = "app_setting_{$tenantId}_{$group}_{$key}";

        return Cache::remember($cacheKey, 3600, function () use ($key, $default, $group, $tenantId) {
            $query = AppSetting::where('s_key', $key)
                ->where(function ($q) use ($tenantId) {
                    $q->where('tenant_id', $tenantId)->orWhereNull('tenant_id');
                });

            if ($group) {
                $query->where('group_slug', $group);
            }

            $setting = $query->orderByRaw('tenant_id IS NULL ASC')->orderBy('id', 'desc')->first();

            if ($setting) {
                return $setting->s_value;
            }

            // Check in predefined defaults if none in DB
            if ($group && isset(self::DEFAULTS[$group][$key])) {
                return self::DEFAULTS[$group][$key];
            }

            foreach (self::DEFAULTS as $groupSettings) {
                if (isset($groupSettings[$key])) {
                    return $groupSettings[$key];
                }
            }

            return $default;
        });
    }

    /**
     * Set a single setting.
     */
    public function set(string $key, mixed $value, string $groupSlug = 'general_settings', ?int $tenantId = null): AppSetting
    {
        $tenantId = $tenantId ?? (auth()->id() ?? 1);

        $setting = AppSetting::firstOrNew([
            'tenant_id' => $tenantId,
            'group_slug' => $groupSlug,
            's_key' => $key,
        ]);

        $setting->s_value = $value;
        $setting->save();

        Cache::forget("app_setting_{$tenantId}_{$groupSlug}_{$key}");
        Cache::forget("app_setting_{$tenantId}__all");

        return $setting;
    }

    /**
     * Set multiple settings for a specific group.
     */
    public function setGroup(string $groupSlug, array $settings, ?int $tenantId = null): array
    {
        $tenantId = $tenantId ?? (auth()->id() ?? 1);
        $saved = [];

        foreach ($settings as $key => $value) {
            $saved[] = $this->set($key, $value, $groupSlug, $tenantId);
        }

        return $saved;
    }

    /**
     * Get all settings grouped by group_slug.
     */
    public function getGrouped(?int $tenantId = null): array
    {
        $tenantId = $tenantId ?? (auth()->id() ?? 1);
        $this->seedDefaults($tenantId);

        $settings = AppSetting::where(function ($q) use ($tenantId) {
            $q->where('tenant_id', $tenantId)->orWhereNull('tenant_id');
        })->orderBy('id')->get();

        $grouped = [];
        foreach ($settings as $setting) {
            if (! isset($grouped[$setting->group_slug])) {
                $grouped[$setting->group_slug] = [];
            }
            $grouped[$setting->group_slug][$setting->s_key] = $setting->s_value;
        }

        return $grouped;
    }

    /**
     * Get all settings as a flat array of models/records for API list.
     */
    public function all(?int $tenantId = null)
    {
        $tenantId = $tenantId ?? (auth()->id() ?? 1);
        $this->seedDefaults($tenantId);

        return AppSetting::where(function ($q) use ($tenantId) {
            $q->where('tenant_id', $tenantId)->orWhereNull('tenant_id');
        })->orderBy('group_slug')->orderBy('id')->get();
    }

    /**
     * Seed default settings for a tenant if not present.
     */
    public function seedDefaults(?int $tenantId = null): void
    {
        $tenantId = $tenantId ?? (auth()->id() ?? 1);

        $existingCount = AppSetting::where('tenant_id', $tenantId)->count();
        if ($existingCount > 0) {
            return;
        }

        foreach (self::DEFAULTS as $groupSlug => $items) {
            foreach ($items as $key => $value) {
                $setting = new AppSetting([
                    'tenant_id' => $tenantId,
                    'group_slug' => $groupSlug,
                    's_key' => $key,
                ]);
                $setting->s_value = $value;
                $setting->save();
            }
        }
    }
}
