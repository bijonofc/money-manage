<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\RoleAccess;
use appsbd\Libs\ApiDataResponse;
use appsbd\Libs\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    /**
     * List roles for grid with pagination, filtering and sorting using ApiDataResponse.
     */
    public function list(Request $request)
    {
        $response = new ApiDataResponse();
        $response->searchFromRequest($request, Role::class, JsonResource::class, [], ['users']);
        return $response->display();
    }

    /**
     * Store new role.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'       => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                ApiResponse::addErrorArray($error);
            }
            $response = new ApiResponse();
            return $response->displayWithResponse(false, null, 422);
        }

        $role = Role::create([
            'title'       => $request->input('title'),
            'slug'        => Str::slug($request->input('title')),
            'description' => $request->input('description', ''),
            'is_super'    => 'N',
            'status'      => 'A',
            'added_by'    => auth()->id() ?? 1,
        ]);

        ApiResponse::addInfoArray(__('Role created successfully'));
        $response = new ApiResponse();
        return $response->displayWithResponse(true, $role);
    }

    /**
     * Show single role.
     */
    public function show($id)
    {
        $role = Role::find($id);
        if (!$role) {
            ApiResponse::addErrorArray(__('Role not found'));
            $response = new ApiResponse();
            return $response->displayWithResponse(false, null, 404);
        }

        $response = new ApiResponse();
        return $response->displayWithResponse(true, $role);
    }

    /**
     * Update role.
     */
    public function update(Request $request, $id)
    {
        $role = Role::find($id);
        if (!$role) {
            ApiResponse::addErrorArray(__('Role not found'));
            $response = new ApiResponse();
            return $response->displayWithResponse(false, null, 404);
        }

        $role->update($request->only(['title', 'description', 'status']));

        ApiResponse::addInfoArray(__('Role updated successfully'));
        $response = new ApiResponse();
        return $response->displayWithResponse(true, $role);
    }

    /**
     * Delete role.
     */
    public function destroy($id)
    {
        $role = Role::find($id);
        if (!$role) {
            ApiResponse::addErrorArray(__('Role not found'));
            $response = new ApiResponse();
            return $response->displayWithResponse(false, null, 404);
        }

        if ($role->is_super === 'Y') {
            ApiResponse::addErrorArray(__('Super admin role cannot be deleted'));
            $response = new ApiResponse();
            return $response->displayWithResponse(false, null, 422);
        }

        $role->delete();

        ApiResponse::addInfoArray(__('Role deleted successfully'));
        $response = new ApiResponse();
        return $response->displayWithResponse(true, null);
    }

    /**
     * List role permissions.
     */
    public function roleAccessList(Request $request)
    {
        $resources = [
            ['res_id' => 'user', 'res_title' => 'User Management', 'action_param' => 'user-list', 'action_title' => 'View Users'],
            ['res_id' => 'user', 'res_title' => 'User Management', 'action_param' => 'user-add', 'action_title' => 'Add User'],
            ['res_id' => 'user', 'res_title' => 'User Management', 'action_param' => 'user-edit', 'action_title' => 'Edit User'],
            ['res_id' => 'user', 'res_title' => 'User Management', 'action_param' => 'user-delete', 'action_title' => 'Delete User'],

            ['res_id' => 'role', 'res_title' => 'Role Management', 'action_param' => 'role-list', 'action_title' => 'View Roles'],
            ['res_id' => 'role', 'res_title' => 'Role Management', 'action_param' => 'role-add', 'action_title' => 'Add Role'],
            ['res_id' => 'role', 'res_title' => 'Role Management', 'action_param' => 'role-edit', 'action_title' => 'Edit Role'],
            ['res_id' => 'role', 'res_title' => 'Role Management', 'action_param' => 'role-delete', 'action_title' => 'Delete Role'],

            ['res_id' => 'account', 'res_title' => 'Accounts', 'action_param' => 'account-list', 'action_title' => 'View Accounts'],
            ['res_id' => 'account', 'res_title' => 'Accounts', 'action_param' => 'account-add', 'action_title' => 'Add Account'],
            ['res_id' => 'account', 'res_title' => 'Accounts', 'action_param' => 'account-edit', 'action_title' => 'Edit Account'],
            ['res_id' => 'account', 'res_title' => 'Accounts', 'action_param' => 'account-delete', 'action_title' => 'Delete Account'],

            ['res_id' => 'transaction', 'res_title' => 'Transactions', 'action_param' => 'transaction-list', 'action_title' => 'View Transactions'],
            ['res_id' => 'transaction', 'res_title' => 'Transactions', 'action_param' => 'transaction-add', 'action_title' => 'Add Transaction'],
            ['res_id' => 'transaction', 'res_title' => 'Transactions', 'action_param' => 'transaction-edit', 'action_title' => 'Edit Transaction'],
            ['res_id' => 'transaction', 'res_title' => 'Transactions', 'action_param' => 'transaction-delete', 'action_title' => 'Delete Transaction'],

            ['res_id' => 'budget', 'res_title' => 'Budgets', 'action_param' => 'budget-list', 'action_title' => 'View Budgets'],
            ['res_id' => 'savings', 'res_title' => 'Savings Goals', 'action_param' => 'savings-list', 'action_title' => 'View Savings Goals'],
            ['res_id' => 'debt', 'res_title' => 'Debts', 'action_param' => 'debt-list', 'action_title' => 'View Debts'],
            ['res_id' => 'setting', 'res_title' => 'Settings', 'action_param' => 'setting-view', 'action_title' => 'View Settings'],
        ];

        $roleAccesses = RoleAccess::all();

        $response = new ApiResponse();
        return $response->displayWithResponse(true, [
            'resources'    => $resources,
            'role_access'  => $roleAccesses,
        ]);
    }

    /**
     * Change permission for a role.
     */
    public function changePermission(Request $request)
    {
        $roleId = $request->input('role_id');
        $resource = $request->input('resource');
        $status = $request->input('status') ? 'Y' : 'N';

        RoleAccess::updateOrCreate(
            ['role_id' => $roleId, 'resource' => $resource],
            ['role_access' => $status]
        );

        ApiResponse::addInfoArray(__('Permission updated'));
        $response = new ApiResponse();
        return $response->displayWithResponse(true, null);
    }
}
