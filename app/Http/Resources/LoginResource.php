<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{
    /**
     * The authentication token.
     *
     * @var string
     */
    protected string $token;

    /**
     * Create a new resource instance.
     *
     * @param  mixed  $resource The User model instance
     * @param  string $token
     * @return void
     */
    public function __construct($resource, string $token)
    {
        parent::__construct($resource);
        $this->token = $token;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'user_type' => $this->user_type,
            'token'     => $this->token,
        ];
    }
}