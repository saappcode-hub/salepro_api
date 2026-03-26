<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\LoginResource;
use App\Http\Requests\LoginRequest;
use App\Http\Traits\ApiResponse;
use App\Models\UserApp;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * Handle user login and return API token.
     *
     * @param  LoginRequest  $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        // Retrieve user by username
        $user = UserApp::where('username', $request->username)->first();

        // Check if user exists and password matches
        if (!$user || !Hash::check($request->password, $user->password)) {
            // Optional: log failed login attempt
            return $this->errorResponse(__('messages.invalid_credentials'), 401);
        }

        // Generate a personal access token
        $token = $user->createToken('api_token')->plainTextToken;

        // Prepare resource for response
        $data = new LoginResource($user, $token);

        // Return standardized success response
        return $this->successResponse(
            $data, __('messages.login_success')
        );
    }
}
