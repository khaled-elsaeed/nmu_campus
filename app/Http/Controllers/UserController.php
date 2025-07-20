<?php

namespace App\Http\Controllers;

use Illuminate\Http\{Request, JsonResponse};
use Illuminate\View\View;
use App\Services\UserService;
use App\Models\User;
use Illuminate\Validation\Rule;
use App\Exceptions\BusinessValidationException;
use Exception;

class UserController extends Controller
{
    /**
     * UserController constructor.
     *
     * @param UserService $userService
     */
    public function __construct(protected UserService $userService)
    {}

    /**
     * Display the user management page.
     *
     * @return View
     */
    public function index(): View
    {
        return view('user.index');
    }

    /**
     * Get user statistics.
     *
     * @return JsonResponse
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = $this->userService->getStats();
            return successResponse('Stats fetched successfully.', $stats);
        } catch (Exception $e) {
            logError('UserController@stats', $e);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Get user data for DataTables.
     *
     * @return JsonResponse
     */
    public function datatable(): JsonResponse
    {
        try {
            return $this->userService->getDatatable();
        } catch (Exception $e) {
            logError('UserController@datatable', $e);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Store a new user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'array|exists:roles,name',
            'gender' => 'required|in:male,female',
        ]);

        try {
            $validated = $request->all();
            $user = $this->userService->createUser($validated);
            return successResponse('User created successfully.', $user);
        } catch (Exception $e) {
            logError('UserController@store', $e, ['request' => $request->all()]);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Show user details.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $user = $this->userService->getUser($id);
            return successResponse('User details fetched successfully.', $user);
        } catch (Exception $e) {
            logError('UserController@show', $e, ['user_id' => $id]);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Update user.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($id)],
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'array|exists:roles,name',
            'gender' => 'required|in:male,female',
        ]);

        try {
            $validated = $request->all();
            $user = $this->userService->updateUser($id, $validated);
            return successResponse('User updated successfully.', $user);
        } catch (Exception $e) {
            logError('UserController@update', $e, ['user_id' => $id, 'request' => $request->all()]);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Delete user.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $this->userService->deleteUser($id);
            return successResponse('User deleted successfully.');
        } catch (BusinessValidationException $e) {
            return errorResponse($e->getMessage(), [], $e->getCode());
        } catch (Exception $e) {
            logError('UserController@destroy', $e, ['user_id' => $id]);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Get all roles for dropdown.
     *
     * @return JsonResponse
     */
    public function getRoles(): JsonResponse
    {
        try {
            $roles = $this->userService->getRoles();
            return successResponse('Roles fetched successfully.', $roles);
        } catch (Exception $e) {
            logError('UserController@getRoles', $e);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Get all users (for dropdown and forms).
     *
     * @return JsonResponse
     */
    public function all(): JsonResponse
    {
        try {
            $users = $this->userService->getAll();
            return successResponse('Users fetched successfully.', $users);
        } catch (Exception $e) {
            logError('UserController@all', $e);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Find user by national ID (for reservation create step).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function findByNationalId(Request $request): JsonResponse
    {
        $request->validate([
            'national_id' => 'required|string',
        ]);
        try {
            $user = $this->userService->findByNationalId($request->input('national_id'));
            if (!$user) {
                return errorResponse('User not found.', [], 404);
            }
            return successResponse('User found.', $user);
        } catch (Exception $e) {
            logError('UserController@findByNationalId', $e, ['national_id' => $request->input('national_id')]);
            return errorResponse('Internal server error.', [], 500);
        }
    }
} 