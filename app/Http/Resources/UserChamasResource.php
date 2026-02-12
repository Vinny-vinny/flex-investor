<?php

namespace App\Http\Resources;

use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserChamasResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "product_id" => $this->product_id,
            "product_name" => $this->product->product_name,
            "saved_amount" => optional($this->user->getWallet($this->product->slug))?->balance,
        ];
    }
}
