<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Role;
use appsbd\Libs\ApiDataResponse;
use appsbd\Libs\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityLogController extends Controller
{
    /**
     * List activity logs for grid with pagination, filtering and sorting.
     */
    public function list(Request $request)
    {
        $user = auth()->user();
        $isSuper = false;

        if ($user) {
            $role = $user->role ?? Role::find($user->role_id);
            $isSuper = ($role && $role->is_super === 'Y');
        }

        $where = [];
        if (! $isSuper && $user) {
            $where = ['tenant_id' => $user->id];
        }

        $response = new ApiDataResponse;
        $response->setDefaultSortData('id', 'desc');
        $response->searchFromRequest(
            $request,
            ActivityLog::class,
            JsonResource::class,
            ['user'],
            [],
            $where
        );

        return $response->display();
    }

    /**
     * Show single activity log details.
     */
    public function show($id)
    {
        $user = auth()->user();
        $isSuper = false;

        if ($user) {
            $role = $user->role ?? Role::find($user->role_id);
            $isSuper = ($role && $role->is_super === 'Y');
        }

        $query = ActivityLog::with('user');
        if (! $isSuper && $user) {
            $query->where('tenant_id', $user->id);
        }

        $log = $query->find($id);

        if (! $log) {
            ApiResponse::addErrorArray(__('Activity log not found'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 404);
        }

        $data = [
            'id' => $log->id,
            'event' => $log->event,
            'log_type' => $log->log_type,
            'des' => $log->des,
            'des_param' => $log->des_param ?? (object) [],
            'ip_address' => $log->ip_address,
            'user_agent' => $log->user_agent,
            'url' => $log->url,
            'properties' => $log->properties,
            'created_at' => $log->created_at?->toISOString() ?? (string) $log->created_at,
            'human_time' => $log->human_time,
            'user' => $log->user ? [
                'id' => $log->user->id,
                'name' => $log->user->name,
                'email' => $log->user->email,
                'username' => $log->user->username,
            ] : null,
        ];

        $response = new ApiResponse;

        return $response->displayWithResponse(true, $data);
    }

    /**
     * Delete an activity log record.
     */
    public function destroy($id)
    {
        $user = auth()->user();
        $isSuper = false;

        if ($user) {
            $role = $user->role ?? Role::find($user->role_id);
            $isSuper = ($role && $role->is_super === 'Y');
        }

        $query = ActivityLog::query();
        if (! $isSuper && $user) {
            $query->where('tenant_id', $user->id);
        }

        $log = $query->find($id);

        if (! $log) {
            ApiResponse::addErrorArray(__('Activity log not found'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 404);
        }

        $log->delete();

        ApiResponse::addInfoArray(__('Activity log deleted successfully'));
        $response = new ApiResponse;

        return $response->displayWithResponse(true, null);
    }
}
