<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Requests\GetUserByEmailRequest;
use App\Modules\User\Requests\StoreUserRequest;
use App\Modules\User\Requests\UpdateUserRequest;
use App\Modules\User\Requests\UserChangePasswordRequest;
use App\Modules\User\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * @group Users
 *
 * APIs for managing users
 */
class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Get a user by ID
     */
    public function getUser($id)
    {
        try {
            $user = $this->userService->getUser($id);
            if (!$user) {
                return $this->notFound('User not found');
            }

            return $this->success($user);
        } catch (\Exception $e) {
            Log::error('Error fetching user', ['exception' => $e]);

            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Get a user by email address
     */
    public function getUserByEmail(GetUserByEmailRequest $request)
    {
        try {
            $user = $this->userService->getUserByEmail($request->email);
            if (!$user) {
                return $this->notFound('User not found');
            }

            return $this->success($user);
        } catch (\Exception $e) {
            Log::error('Error fetching user by email', ['exception' => $e]);

            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Create a new user
     */
    public function createUser(StoreUserRequest $request)
    {
        try {
            $user = $this->userService->createUser($request->validated());

            return $this->created($user, 'User created successfully');
        } catch (\Exception $e) {
            Log::error('Error creating user', ['exception' => $e]);

            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Update an existing user
     */
    public function updateUser(UpdateUserRequest $request, $id)
    {
        try {
            $user = $this->userService->updateUser($id, $request->validated());
            if (!$user) {
                return $this->notFound('User not found');
            }

            return $this->success($user, 'User updated successfully');
        } catch (\Exception $e) {
            Log::error('Error updating user', ['exception' => $e]);

            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Delete a user
     */
    public function deleteUser($id)
    {
        try {
            $result = $this->userService->deleteUser($id);
            if (!$result) {
                return $this->notFound('User not found');
            }

            return $this->deleted('User deleted successfully');
        } catch (\Exception $e) {
            Log::error('Error deleting user', ['exception' => $e]);

            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Get all users with optional filters
     */
    public function getAllUsers(Request $request)
    {
        try {
            $filters = $request->only(['name', 'email', 'role', 'status']);
            $users = $this->userService->getAllUsers($filters);

            return $this->success($users);
        } catch (\Exception $e) {
            Log::error('Error fetching users', ['exception' => $e]);

            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Get paginated list of users
     */
    public function getUsersPaginated(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 15);
            $filters = $request->except(['per_page']);
            $users = $this->userService->getUsersPaginated($perPage, $filters);

            return $this->paginated($users);
        } catch (\Exception $e) {
            Log::error('Error fetching paginated users', ['exception' => $e]);

            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Change user password
     */
    public function changePassword(UserChangePasswordRequest $request)
    {
        try {
            $user = $this->userService->changePassword(
                Auth::id(),
                $request->current_password,
                $request->new_password
            );

            return $this->success($user, 'Password changed successfully');
        } catch (ValidationException $e) {
            return $this->error('Validation failed', 400, $e->errors());
        } catch (\Exception $e) {
            Log::error('Error changing password', ['exception' => $e]);

            return $this->error('Internal server error', 500);
        }
    }

    /**
     * Get authenticated user profile
     */
    public function getProfile()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return $this->error('User not authenticated', 401);
            }

            return $this->success($user);
        } catch (\Exception $e) {
            Log::error('Error fetching profile', ['exception' => $e]);

            return $this->error('Internal server error', 500);
        }
    }
}
