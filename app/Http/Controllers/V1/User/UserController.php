<?php

namespace App\Http\Controllers\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\UserApp;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    use ApiResponse;

    /**
     * Display the specified user's profile information.
     *
     * @param User $user The User model instance, automatically resolved by route-model binding.
     * @return JsonResponse
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();

        $responseData = [
            'id' => $user->id,
            'first_name' => $user?->first_name ?? '',
            'last_name' => $user?->last_name ?? '',
            'status' => $user->status,
            'user_type' => $user->user_type,
            'joined_on' => jDateTimeFormat($user->created_at),
        ];

        return $this->successResponse($responseData, __('messages.user_retrieved_successfully'));
    }
}