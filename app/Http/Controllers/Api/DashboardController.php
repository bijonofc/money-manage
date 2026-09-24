<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use appsbd\Libs\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService
    ) {}

    public function initialData(Request $request): JsonResponse
    {
        $userId = auth()->id();
        if (! $userId) {
            ApiResponse::addErrorArray(__('Unauthorized'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 401);
        }

        $data = $this->dashboardService->getInitialData($userId);

        $response = new ApiResponse;

        return $response->displayWithResponse(true, $data);
    }

    public function notifications(Request $request): JsonResponse
    {
        $response = new ApiResponse;

        return $response->displayWithResponse(true, [
            'unread_count' => 0,
            'list' => [],
        ]);
    }

    public function notificationList(Request $request): JsonResponse
    {
        $response = new ApiResponse;

        return $response->displayWithResponse(true, [
            'rowdata' => [],
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
        ]);
    }
}
