<?php

namespace App\Http\Resources\Client;

use App\Models\Key;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Key
 */
class KeyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'values' => ValueResource::collection($this->whenLoaded('values')),
        ];
    }
}
