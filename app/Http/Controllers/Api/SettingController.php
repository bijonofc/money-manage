<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Services\ActivityLogger;
use App\Services\SettingService;
use appsbd\Libs\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    protected SettingService $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    /**
     * List all settings for grid / store (supports GET and POST).
     */
    public function list(Request $request)
    {
        $tenantId = auth()->id() ?? 1;
        $settings = $this->settingService->all($tenantId);

        $rowdata = $settings->map(function ($item) {
            return [
                'id' => $item->id,
                'group_slug' => $item->group_slug,
                's_key' => $item->s_key,
                's_value' => $item->s_value,
                's_val' => $item->s_val,
                's_type' => $item->s_type,
            ];
        })->values()->toArray();

        $response = new ApiResponse;

        return $response->displayWithResponse(true, [
            'page' => 1,
            'limit' => count($rowdata),
            'records' => count($rowdata),
            'total' => 1,
            'rowdata' => $rowdata,
            'recordsTotal' => count($rowdata),
            'recordsFiltered' => count($rowdata),
        ]);
    }

    /**
     * API resource index alias.
     */
    public function index(Request $request)
    {
        return $this->list($request);
    }

    /**
     * Save settings (handles group-based or individual key-value).
     */
    public function save(Request $request)
    {
        $tenantId = auth()->id() ?? 1;
        $groupSlug = $request->input('group_slug', 'general_settings');
        $settingsData = $request->input('settings');

        // Handle uploaded app_logo if present
        if ($request->hasFile('app_logo')) {
            $path = $request->file('app_logo')->store('settings', 'public');
            $this->settingService->set('app_logo', Storage::url($path), $groupSlug, $tenantId);
        }

        if (is_array($settingsData)) {
            $this->settingService->setGroup($groupSlug, $settingsData, $tenantId);
        } elseif ($request->has('s_key')) {
            $key = $request->input('s_key');
            $val = $request->input('s_value', $request->input('s_val'));
            $this->settingService->set($key, $val, $groupSlug, $tenantId);
        } else {
            // Check for direct key-value inputs excluding metadata
            $allInputs = $request->except(['_token', '_method', 'group_slug', 'app_logo']);
            if (! empty($allInputs)) {
                $this->settingService->setGroup($groupSlug, $allInputs, $tenantId);
            }
        }

        ActivityLogger::log(
            event: 'updated',
            des: 'act.up',
            desParam: [
                'uname' => auth()->user()?->name ?? 'User',
                'model' => "Settings ({$groupSlug})",
            ],
            subjectType: AppSetting::class,
            properties: ['group_slug' => $groupSlug]
        );

        ApiResponse::addInfoArray(__('Settings saved successfully'));
        $response = new ApiResponse;

        $updatedGrouped = $this->settingService->getGrouped($tenantId);

        return $response->displayWithResponse(true, [
            'settings' => $updatedGrouped,
            'group_slug' => $groupSlug,
        ]);
    }

    /**
     * Store alias for resource routing.
     */
    public function store(Request $request)
    {
        return $this->save($request);
    }

    /**
     * Show single setting.
     */
    public function show($id)
    {
        $tenantId = auth()->id() ?? 1;

        $setting = is_numeric($id)
            ? AppSetting::where('tenant_id', $tenantId)->find($id)
            : AppSetting::where('tenant_id', $tenantId)->where('s_key', $id)->first();

        if (! $setting) {
            ApiResponse::addErrorArray(__('Setting not found'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 404);
        }

        $response = new ApiResponse;

        return $response->displayWithResponse(true, $setting);
    }

    /**
     * Update single setting.
     */
    public function update(Request $request, $id)
    {
        return $this->save($request);
    }

    /**
     * Delete setting.
     */
    public function destroy($id)
    {
        $tenantId = auth()->id() ?? 1;
        $setting = AppSetting::where('tenant_id', $tenantId)->find($id);

        if (! $setting) {
            ApiResponse::addErrorArray(__('Setting not found'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 404);
        }

        ActivityLogger::logDeleted($setting, $setting->s_key);
        $setting->delete();

        ApiResponse::addInfoArray(__('Setting deleted successfully'));
        $response = new ApiResponse;

        return $response->displayWithResponse(true, null);
    }
}
