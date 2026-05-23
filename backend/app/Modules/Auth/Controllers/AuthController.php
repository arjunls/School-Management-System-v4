<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Auth\Requests\ChangePasswordRequest;
use App\Modules\Auth\Requests\ForgotPasswordRequest;
use App\Modules\Auth\Requests\LoginRequest;
use App\Modules\Auth\Requests\RegisterRequest;
use App\Modules\Auth\Requests\ResetPasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

/**
 * @group Authentication
 *
 * APIs for managing authentication
 */
class AuthController extends Controller
{
    /**
     * Login user and return token
     *
     * @unauthenticated
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        /** @var User|null $user */
        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return $this->error('Invalid credentials', 401);
        }

        if (! $user->isActive()) {
            return $this->error('Account is not active', 403);
        }

        $tokenName = 'api-token';
        $token = $user->createToken($tokenName)->plainTextToken;

        return $this->success([
            'user' => $this->formatUser($user),
            'token' => $token,
        ]);
    }

    /**
     * Logout user and invalidate token
     */
    public function logout(Request $request)
    {
        try {
            /** @var User|null $user */
            $user = $request->user();
            if ($user && $user->currentAccessToken()) {
                $user->currentAccessToken()->delete();
            }

            return $this->success(null, 'Logged out successfully');
        } catch (\Exception $e) {
            Log::error('Error during logout', ['exception' => $e]);

            return $this->error();
        }
    }

    /**
     * Refresh authentication token
     */
    public function refresh(Request $request)
    {
        try {
            /** @var User|null $user */
            $user = $request->user();

            if (! $user) {
                return $this->error('User not authenticated', 401);
            }

            if ($user->currentAccessToken()) {
                $user->currentAccessToken()->delete();
            }

            $newToken = $user->createToken('api-token')->plainTextToken;

            return $this->success([
                'user' => $this->formatUser($user),
                'token' => $newToken,
            ]);
        } catch (\Exception $e) {
            Log::error('Error refreshing token', ['exception' => $e]);

            return $this->error();
        }
    }

    /**
     * Get authenticated user profile
     */
    public function profile(Request $request)
    {
        try {
            /** @var User|null $user */
            $user = $request->user();

            if (! $user) {
                return $this->error('User not authenticated', 401);
            }

            return $this->success($this->formatUser($user));
        } catch (\Exception $e) {
            Log::error('Error fetching auth profile', ['exception' => $e]);

            return $this->error();
        }
    }

    /**
     * Change authenticated user password
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            /** @var User $user */
            $user = $request->user();

            $data = $request->validated();

            if (! Hash::check($data['current_password'], $user->password)) {
                return $this->error('Current password is incorrect', 422);
            }

            $user->password = Hash::make($data['password']);
            $user->save();

            return $this->success(null, 'Password changed successfully');
        } catch (\Exception $e) {
            Log::error('Error changing password', ['exception' => $e]);

            return $this->error();
        }
    }

    /**
     * Register a new user
     *
     * @unauthenticated
     */
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'] ?? 'student',
            'status' => 'active',
        ]);

        try {
            $user->assignRole($user->role);
        } catch (\Spatie\Permission\Exceptions\RoleDoesNotExist $e) {
            // Role may not be seeded yet; skip gracefully
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return $this->created([
            'user' => $this->formatUser($user),
            'token' => $token,
        ]);
    }

    /**
     * Send password reset link
     *
     * @unauthenticated
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? $this->success(null, 'Password reset link sent to your email')
            : $this->error('Unable to send reset link');
    }

    /**
     * Reset user password with token
     *
     * @unauthenticated
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? $this->success(null, 'Password has been reset successfully')
            : $this->error('Invalid or expired reset token', 400);
    }

    protected function formatUser(User $user): array
    {
        $data = $user->toArray();

        try {
            $data['spatie_roles'] = $user->getRoleNames();
            $data['permissions'] = $user->getAllPermissions()->pluck('name');
        } catch (\Exception) {
            $data['spatie_roles'] = [];
            $data['permissions'] = [];
        }

        return $data;
    }
}
