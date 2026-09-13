<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\CategoryService;
use appsbd\Libs\ApiDataResponse;
use appsbd\Libs\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * List users for grid with pagination, filtering and sorting using ApiDataResponse.
     */
    public function list(Request $request)
    {
        $response = new ApiDataResponse;
        $response->searchFromRequest($request, User::class, JsonResource::class, ['role']);

        return $response->display();
    }

    /**
     * Store new user.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:4',
            'role_id' => 'required|exists:roles,id',
            'status' => 'nullable|in:A,I,P',
            'contact_no' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                ApiResponse::addErrorArray($error);
            }
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 422);
        }

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'username' => $request->input('username'),
            'password' => Hash::make($request->input('password')),
            'role_id' => $request->input('role_id'),
            'status' => $request->input('status', 'A'),
            'contact_no' => $request->input('contact_no'),
            'address' => $request->input('address'),
        ]);

        if ($user->status === 'A') {
            CategoryService::seedDefaultCategoriesForUser($user->id);
        }

        ActivityLogger::logCreated($user, $user->name);

        ApiResponse::addInfoArray(__('User created successfully'));
        $response = new ApiResponse;

        return $response->displayWithResponse(true, $user);
    }

    /**
     * Show single user.
     */
    public function show($id)
    {
        $user = User::with('role')->find($id);
        if (! $user) {
            ApiResponse::addErrorArray(__('User not found'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 404);
        }

        $response = new ApiResponse;

        return $response->displayWithResponse(true, $user);
    }

    /**
     * Update user.
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (! $user) {
            ApiResponse::addErrorArray(__('User not found'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,'.$user->id,
            'username' => 'sometimes|required|string|unique:users,username,'.$user->id,
            'role_id' => 'sometimes|required|exists:roles,id',
            'status' => 'sometimes|required|in:A,I,P',
            'contact_no' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                ApiResponse::addErrorArray($error);
            }
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 422);
        }

        $user->fill($request->only(['name', 'email', 'username', 'role_id', 'status', 'contact_no', 'address']));
        $user->save();

        if ($user->status === 'A') {
            CategoryService::seedDefaultCategoriesForUser($user->id);
        }

        ActivityLogger::logUpdated($user, $user->name);

        ApiResponse::addInfoArray(__('User updated successfully'));
        $response = new ApiResponse;

        return $response->displayWithResponse(true, $user);
    }

    /**
     * Approve a pending user (sets status to 'A' and initializes starter categories).
     */
    public function approve($id)
    {
        $user = User::find($id);
        if (! $user) {
            ApiResponse::addErrorArray(__('User not found'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 404);
        }

        $user->status = 'A';
        $user->save();

        // Seed personal default categories for the approved user
        CategoryService::seedDefaultCategoriesForUser($user->id);

        ActivityLogger::log(
            event: 'approved',
            des: 'User approved and activated',
            desParam: ['uname' => $user->name],
            subjectType: User::class,
            subjectId: $user->id,
            userId: auth()->id()
        );

        ApiResponse::addInfoArray(__('User has been approved and activated successfully.'));
        $response = new ApiResponse;

        return $response->displayWithResponse(true, $user);
    }

    /**
     * Delete user.
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if (! $user) {
            ApiResponse::addErrorArray(__('User not found'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 404);
        }

        ActivityLogger::logDeleted($user, $user->name);

        $user->delete();

        ApiResponse::addInfoArray(__('User deleted successfully'));
        $response = new ApiResponse;

        return $response->displayWithResponse(true, null);
    }

    /**
     * Change user password.
     */
    public function changePassword(Request $request)
    {
        $userId = $request->input('user_id', auth()->id());
        $user = User::find($userId);

        if (! $user) {
            ApiResponse::addErrorArray(__('User not found'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 404);
        }

        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:4',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                ApiResponse::addErrorArray($error);
            }
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 422);
        }

        $user->password = Hash::make($request->input('password'));
        $user->save();

        ActivityLogger::log(
            event: 'updated',
            des: 'act.up',
            desParam: [
                'uname' => auth()->user()?->name ?? 'User',
                'model' => "User Password ({$user->name})",
            ],
            subjectType: User::class,
            subjectId: $user->id,
            userId: auth()->id(),
            tenantId: $user->id
        );

        ApiResponse::addInfoArray(__('Password changed successfully'));
        $response = new ApiResponse;

        return $response->displayWithResponse(true, null);
    }
}
