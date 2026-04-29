<?php
use App\Services\SettingService;
if (! function_exists('app_setting')) {
    function app_setting($key, $default = null, $group = null) {
        $query = \App\Models\AppSetting::where('s_key', $key);
        if ($group) {
            $query->where('group_slug', $group);
        }
        $setting = $query->first();
        return $setting ? $setting->setting_value : $default;
    }
}
if (!function_exists('get_option')) {
    function get_option(string $key, $default = null)
    {
        $service = new SettingService();
        return $service->get($key, $default);
    }
}

if (!function_exists('set_option')) {
    function set_option(string $key, $value, string $groupSlug = 'basic_settings')
    {
        $service = new SettingService();
        $service->set($key, $value, $groupSlug);
    }
}
if (!function_exists('appsbd_get_remote_ip')) {
    function appsbd_get_remote_ip() {
        $request = request();
        if (! \Illuminate\Support\Facades\App::environment( 'production' ) ) {
            return '118.179.63.17';
        }
        if ($request->server('HTTP_CF_CONNECTING_IP')) {
            return $request->server('HTTP_CF_CONNECTING_IP');
        }elseif ($request->server('HTTP_X_REAL_IP')) {
            return $request->server('HTTP_X_REAL_IP');
        } elseif ($request->server('HTTP_CLIENT_IP')) {
            return $request->server('HTTP_CLIENT_IP');
        } elseif ($request->server('HTTP_X_FORWARDED_FOR')) {
            return $request->server('HTTP_X_FORWARDED_FOR');
        }  else {
            return $request->server('REMOTE_ADDR', '-');
        }
    }
}
if (!function_exists('appsbd_get_remote_ipinfo')) {
    function appsbd_get_remote_ipinfo() {
       $remoteIp = appsbd_get_remote_ip();
       $ipinfo = \Illuminate\Support\Facades\Http::withOptions([
           'verify' => false,
       ])->get("https://free.freeipapi.com/api/json/{$remoteIp}")->json();
       return $ipinfo;
    }
}
if (!function_exists('turnstile_verify')) {
    /**
     * Verify Cloudflare Turnstile token
     *
     * @param string $token
     * @param string|null $ip
     * @return bool
     */
    function turnstile_verify(string $token, ?string $ip = null): bool
    {
        if (empty(env('TURNSTILE_SECRET_KEY'))) {
            // Allow by default if Turnstile not configured
            return true;
        }

        try {
            $res = \Illuminate\Support\Facades\Http::withoutVerifying()->post(
                'https://challenges.cloudflare.com/turnstile/v0/siteverify',
                [
                    'secret' => env('TURNSTILE_SECRET_KEY'),
                    'response' => $token,
                    'remoteip' => $ip,
                ]
            );

            $result = $res->json();

            return $result['success'] ?? false;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Turnstile verification failed: " . $e->getMessage());
            return false;
        }
    }
}
