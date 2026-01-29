<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class ProductsResource extends JsonResource
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
            "product_name" => $this->product_name." @".number_format($this->target_amount),
            "target_amount" => $this->target_amount,
            "base_amount" =>  $this->base_amount,
            "end_date" => Carbon::now()->addYear()->format('d-m-Y'),
        ];
    }
}
