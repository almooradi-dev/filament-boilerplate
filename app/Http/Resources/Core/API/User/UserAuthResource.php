<?php

namespace App\Http\Resources\Core\API\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserAuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'country_code' => '+' . $this->country_code,
            'phone' => $this->phone,
            'account_verified' => $this->account_verified,
            'email' => $this->email,
            'username' => $this->username,
            'avatar' => $this->avatar_url,
        ];
    }
}