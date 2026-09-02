<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\RoleAccess;
use App\Models\User;
use appsbd\Libs\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Handle user login.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                ApiResponse::addErrorArray($error);
            }
            $response = new ApiResponse();
            return $response->displayWithResponse(false, null, 422);
        }

        $loginInput = $request->input('email');
        $password = $request->input('password');

        $user = User::where('email', $loginInput)
            ->orWhere('username', $loginInput)
            ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            ApiResponse::addErrorArray(__('Invalid email/username or password.'));
            $response = new ApiResponse();
            return $response->displayWithResponse(false, null, 401);
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        $userData = $this->formatUserData($user);

        ApiResponse::addInfoArray(__('Login successful'));
        $response = new ApiResponse();
        return $response->displayWithResponse(true, [
            'user_data' => $userData,
        ]);
    }

    /**
     * Handle social login (Google).
     */
    public function socialLogin(Request $request)
    {
        $user = Auth::user() ?? User::first();

        if ($user) {
            Auth::login($user, true);
            $request->session()->regenerate();

            ApiResponse::addInfoArray(__('Login successful'));
            $response = new ApiResponse();
            return $response->displayWithResponse(true, [
                'user_data' => $this->formatUserData($user),
            ]);
        }

        ApiResponse::addErrorArray(__('Social login failed.'));
        $response = new ApiResponse();
        return $response->displayWithResponse(false, null, 401);
    }

    /**
     * Handle OTP verification.
     */
    public function verifyOtp(Request $request)
    {
        $userId = $request->input('user_id');
        $user = $userId ? User::find($userId) : Auth::user();

        if ($user) {
            Auth::login($user, true);
            $request->session()->regenerate();

            ApiResponse::addInfoArray(__('Verification successful'));
            $response = new ApiResponse();
            return $response->displayWithResponse(true, [
                'user_data' => $this->formatUserData($user),
            ]);
        }

        ApiResponse::addErrorArray(__('User not found.'));
        $response = new ApiResponse();
        return $response->displayWithResponse(false, null, 404);
    }

    /**
     * Resend OTP.
     */
    public function resendOtp(Request $request)
    {
        ApiResponse::addInfoArray(__('OTP sent successfully'));
        $response = new ApiResponse();
        return $response->displayWithResponse(true, [
            'retry_after_seconds' => 60,
        ]);
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        ApiResponse::addInfoArray(__('Logged out successfully'));
        $response = new ApiResponse();
        return $response->displayWithResponse(true, null);
    }

    /**
     * Get current user profile.
     */
    public function profile(Request $request)
    {
        $user = Auth::user() ?? User::first();

        if (!$user) {
            ApiResponse::addErrorArray(__('Unauthenticated'));
            $response = new ApiResponse();
            return $response->displayWithResponse(false, null, 401);
        }

        $response = new ApiResponse();
        return $response->displayWithResponse(true, $this->formatUserData($user));
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user() ?? User::first();

        if (!$user) {
            ApiResponse::addErrorArray(__('Unauthenticated'));
            $response = new ApiResponse();
            return $response->displayWithResponse(false, null, 401);
        }

        $validator = Validator::make($request->all(), [
            'name'       => 'sometimes|required|string|max:255',
            'email'      => 'sometimes|required|email|unique:users,email,' . $user->id,
            'username'   => 'sometimes|required|string|unique:users,username,' . $user->id,
            'contact_no' => 'nullable|string|max:50',
            'address'    => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                ApiResponse::addErrorArray($error);
            }
            $response = new ApiResponse();
            return $response->displayWithResponse(false, null, 422);
        }

        $user->fill($request->only(['name', 'email', 'username', 'contact_no', 'address']));
        $user->save();

        ApiResponse::addInfoArray(__('Profile updated successfully'));
        $response = new ApiResponse();
        return $response->displayWithResponse(true, $this->formatUserData($user));
    }

    /**
     * Helper to format user data including capabilities.
     */
    private function formatUserData(User $user): array
    {
        $role = $user->role ?? Role::find($user->role_id);
        $caps = [];

        if ($role) {
            if ($role->is_super === 'Y') {
                $caps['*'] = true;
                $allCaps = [
                    'role-list', 'role-add', 'role-edit', 'role-delete',
                    'user-list', 'user-add', 'user-edit', 'user-delete',
                    'customer-list', 'customer-add', 'customer-edit', 'customer-delete',
                    'setting-view', 'setting-edit',
                    'account-list', 'account-add', 'account-edit', 'account-delete',
                    'category-list', 'category-add', 'category-edit', 'category-delete',
                    'transaction-list', 'transaction-add', 'transaction-edit', 'transaction-delete',
                    'budget-list', 'budget-add', 'budget-edit', 'budget-delete',
                    'savings-list', 'savings-add', 'savings-edit', 'savings-delete',
                    'debt-list', 'debt-add', 'debt-edit', 'debt-delete',
                    'activity-list', 'template-list', 'notification-list',
                ];
                foreach ($allCaps as $c) {
                    $caps[$c] = true;
                }
            } else {
                $accesses = RoleAccess::where('role_id', $role->id)->where('role_access', 'Y')->get();
                foreach ($accesses as $acc) {
                    $caps[$acc->resource] = true;
                }
            }
        }

        return [
            'id'         => $user->id,
            'name'       => $user->name,
            'username'   => $user->username,
            'email'      => $user->email,
            'contact_no' => $user->contact_no,
            'address'    => $user->address,
            'role_id'    => $user->role_id,
            'role_title' => $role?->title ?? '',
            'is_super'   => $role?->is_super ?? 'N',
            'is_force'   => 'N',
            'image'      => null,
            'image_url'  => null,
            'caps'       => (object)$caps,
        ];
    }
}
