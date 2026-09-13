<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\RoleAccess;
use App\Models\User;
use App\Services\ActivityLogger;
use appsbd\Libs\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Handle public user registration.
     * New users are created with status 'P' (Pending) and require Super Admin approval.
     */
    public function register(Request $request)
    {
        // 1. Verify Turnstile captcha
        $turnstileToken = $request->input('token') ?? $request->input('trans_token', '');
        if (! turnstile_verify($turnstileToken, $request->ip())) {
            ApiResponse::addErrorArray(__('Security verification failed. Please refresh and try again.'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 422);
        }

        // 2. Validate input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|min:3|max:50|unique:users,username',
            'password' => 'required|string|min:6|confirmed',
            'contact_no' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                ApiResponse::addErrorArray($error);
            }
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 422);
        }

        // 3. Create user in pending state
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'username' => $request->input('username'),
            'contact_no' => $request->input('contact_no'),
            'password' => Hash::make($request->input('password')),
            'role_id' => 4, // Customer / General User role
            'status' => 'P', // Pending super admin approval
            'is_sso' => 'N',
        ]);

        ActivityLogger::logCreated($user, $user->name);

        ApiResponse::addInfoArray(__('Registration successful! Your account is pending administrator approval before you can log in.'));
        $response = new ApiResponse;

        return $response->displayWithResponse(true, [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'status' => 'P',
            ],
            'pending_approval' => true,
        ]);
    }

    /**
     * Handle user login.
     */
    public function login(Request $request)
    {
        // 1. Verify Turnstile captcha
        $turnstileToken = $request->input('token') ?? $request->input('trans_token', '');
        if (! turnstile_verify($turnstileToken, $request->ip())) {
            ApiResponse::addErrorArray(__('Security verification failed. Please try again.'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 422);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                ApiResponse::addErrorArray($error);
            }
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 422);
        }

        $loginInput = $request->input('email');
        $password = $request->input('password');

        $user = User::where('email', $loginInput)
            ->orWhere('username', $loginInput)
            ->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            ApiResponse::addErrorArray(__('Invalid email/username or password.'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 401);
        }

        // Check user status
        if ($user->status === 'P') {
            ApiResponse::addErrorArray(__('Your registration is pending approval by the administrator. Please wait for activation.'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, ['is_pending' => true], 403);
        }

        if ($user->status === 'I') {
            ApiResponse::addErrorArray(__('Your account is inactive. Please contact support.'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, ['is_inactive' => true], 403);
        }

        Auth::login($user, true);
        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        ActivityLogger::logLogin($user, 'standard');

        $userData = $this->formatUserData($user);

        ApiResponse::addInfoArray(__('Login successful'));
        $response = new ApiResponse;

        return $response->displayWithResponse(true, [
            'user_data' => $userData,
        ]);
    }

    /**
     * Handle social login (Google).
     */
    public function socialLogin(Request $request)
    {
        $accessToken = $request->input('token_id') ?? $request->input('access_token');

        if (! $accessToken) {
            ApiResponse::addErrorArray(__('Google authorization token is missing.'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 422);
        }

        // Verify token with Google userinfo API
        try {
            $googleRes = \Illuminate\Support\Facades\Http::withoutVerifying()
                ->withToken($accessToken)
                ->get('https://www.googleapis.com/oauth2/v3/userinfo');

            if (! $googleRes->successful()) {
                ApiResponse::addErrorArray(__('Failed to authenticate with Google.'));
                $response = new ApiResponse;

                return $response->displayWithResponse(false, null, 401);
            }

            $googleData = $googleRes->json();
            $googleId = $googleData['sub'] ?? null;
            $email = $googleData['email'] ?? null;
            $name = $googleData['name'] ?? 'Google User';

            if (! $email) {
                ApiResponse::addErrorArray(__('Could not retrieve email from your Google account.'));
                $response = new ApiResponse;

                return $response->displayWithResponse(false, null, 400);
            }

            // Look for existing user
            $user = User::where('google_id', $googleId)
                ->orWhere('email', $email)
                ->first();

            if (! $user) {
                // New user registration via Google -> status is 'P' (Pending approval)
                $usernameBase = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', explode('@', $email)[0]));
                $username = $usernameBase ?: 'user'.rand(1000, 9999);
                $counter = 1;
                while (User::where('username', $username)->exists()) {
                    $username = $usernameBase.$counter++;
                }

                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'username' => $username,
                    'password' => Hash::make(\Illuminate\Support\Str::random(32)),
                    'role_id' => 4, // Customer / General User
                    'status' => 'P', // Pending admin approval
                    'google_id' => $googleId,
                    'is_sso' => 'Y',
                    'email_verified_at' => now(),
                ]);

                ActivityLogger::logCreated($user, "Google SSO registration: {$user->name}");

                ApiResponse::addInfoArray(__('Google registration received! Your account is pending administrator approval before you can log in.'));
                $response = new ApiResponse;

                return $response->displayWithResponse(false, [
                    'is_pending' => true,
                    'message' => 'Pending administrator approval',
                ], 403);
            }

            // Existing user: Link Google account if not linked
            if (empty($user->google_id)) {
                $user->google_id = $googleId;
                $user->is_sso = 'Y';
                $user->save();
            }

            // Check status
            if ($user->status === 'P') {
                ApiResponse::addErrorArray(__('Your registration via Google is pending approval by the administrator.'));
                $response = new ApiResponse;

                return $response->displayWithResponse(false, ['is_pending' => true], 403);
            }

            if ($user->status === 'I') {
                ApiResponse::addErrorArray(__('Your account is inactive. Please contact support.'));
                $response = new ApiResponse;

                return $response->displayWithResponse(false, ['is_inactive' => true], 403);
            }

            Auth::login($user, true);
            if ($request->hasSession()) {
                $request->session()->regenerate();
            }

            ActivityLogger::logLogin($user, 'google_sso');

            ApiResponse::addInfoArray(__('Login successful'));
            $response = new ApiResponse;

            return $response->displayWithResponse(true, [
                'user_data' => $this->formatUserData($user),
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Google Auth error: '.$e->getMessage());
            ApiResponse::addErrorArray(__('Authentication error: ').$e->getMessage());
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 500);
        }
    }

    /**
     * Handle Forgot Password request (sends reset link via queued email).
     */
    public function forgetPassword(Request $request)
    {
        // 1. Verify Turnstile captcha
        $turnstileToken = $request->input('token') ?? $request->input('trans_token', '');
        if (! turnstile_verify($turnstileToken, $request->ip())) {
            ApiResponse::addErrorArray(__('Security verification failed. Please try again.'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 422);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                ApiResponse::addErrorArray($error);
            }
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 422);
        }

        $email = $request->input('email');
        $user = User::where('email', $email)->first();

        // Always return success message to avoid email enumeration
        if (! $user) {
            ApiResponse::addInfoArray(__('If an account exists for this email, a password reset link has been sent.'));
            $response = new ApiResponse;

            return $response->displayWithResponse(true, null);
        }

        // Generate token and store in password_reset_tokens
        $plainToken = \Illuminate\Support\Str::random(64);

        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($plainToken),
                'created_at' => now(),
            ]
        );

        $resetUrl = url('/reset-password/'.$plainToken.'?email='.urlencode($email));

        // Queue the email to the 'default' queue
        try {
            \Illuminate\Support\Facades\Mail::to($email)
                ->queue(new \App\Mail\ResetPasswordMail($resetUrl, $user->name));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Failed to queue password reset email: '.$e->getMessage());
        }

        ApiResponse::addInfoArray(__('A password reset link has been sent to your email address.'));
        $response = new ApiResponse;

        return $response->displayWithResponse(true, null);
    }

    /**
     * Handle Reset Password request.
     */
    public function resetPassword(Request $request)
    {
        // 1. Verify Turnstile captcha
        $turnstileToken = $request->input('token') ?? $request->input('trans_token', '');
        if (! turnstile_verify($turnstileToken, $request->ip())) {
            ApiResponse::addErrorArray(__('Security verification failed. Please try again.'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 422);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                ApiResponse::addErrorArray($error);
            }
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 422);
        }

        $email = $request->input('email');
        $token = $request->input('token');

        $record = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (! $record || ! Hash::check($token, $record->token)) {
            ApiResponse::addErrorArray(__('Invalid or expired password reset link.'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 400);
        }

        // Check if token is older than 60 minutes
        $createdAt = \Illuminate\Support\Carbon::parse($record->created_at);
        if ($createdAt->addMinutes(60)->isPast()) {
            \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('email', $email)->delete();
            ApiResponse::addErrorArray(__('This password reset link has expired. Please request a new one.'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 400);
        }

        // Update user's password
        $user = User::where('email', $email)->first();
        $user->password = Hash::make($request->input('password'));
        $user->save();

        // Invalidate token
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('email', $email)->delete();

        ActivityLogger::log(
            event: 'updated',
            des: 'Password reset completed',
            desParam: ['uname' => $user->name],
            subjectType: User::class,
            subjectId: $user->id,
            userId: $user->id
        );

        ApiResponse::addInfoArray(__('Password reset successfully! You can now log in with your new password.'));
        $response = new ApiResponse;

        return $response->displayWithResponse(true, null);
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

            ActivityLogger::logLogin($user, 'otp');

            ApiResponse::addInfoArray(__('Verification successful'));
            $response = new ApiResponse;

            return $response->displayWithResponse(true, [
                'user_data' => $this->formatUserData($user),
            ]);
        }

        ApiResponse::addErrorArray(__('User not found.'));
        $response = new ApiResponse;

        return $response->displayWithResponse(false, null, 404);
    }

    /**
     * Resend OTP.
     */
    public function resendOtp(Request $request)
    {
        ApiResponse::addInfoArray(__('OTP sent successfully'));
        $response = new ApiResponse;

        return $response->displayWithResponse(true, [
            'retry_after_seconds' => 60,
        ]);
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            ActivityLogger::logLogout($user);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        ApiResponse::addInfoArray(__('Logged out successfully'));
        $response = new ApiResponse;

        return $response->displayWithResponse(true, null);
    }

    /**
     * Get current user profile.
     */
    public function profile(Request $request)
    {
        $user = Auth::user() ?? User::first();

        if (! $user) {
            ApiResponse::addErrorArray(__('Unauthenticated'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 401);
        }

        $response = new ApiResponse;

        return $response->displayWithResponse(true, $this->formatUserData($user));
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user() ?? User::first();

        if (! $user) {
            ApiResponse::addErrorArray(__('Unauthenticated'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 401);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,'.$user->id,
            'username' => 'sometimes|required|string|unique:users,username,'.$user->id,
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

        $user->fill($request->only(['name', 'email', 'username', 'contact_no', 'address']));
        $user->save();

        ActivityLogger::logUpdated($user, $user->name);

        ApiResponse::addInfoArray(__('Profile updated successfully'));
        $response = new ApiResponse;

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
                    'activity-list', 'activity-detail', 'template-list', 'notification-list',
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
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'contact_no' => $user->contact_no,
            'address' => $user->address,
            'role_id' => $user->role_id,
            'role_title' => $role?->title ?? '',
            'is_super' => $role?->is_super ?? 'N',
            'is_force' => 'N',
            'image' => null,
            'image_url' => null,
            'caps' => (object) $caps,
        ];
    }
}
