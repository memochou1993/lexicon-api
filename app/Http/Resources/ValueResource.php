<?php

namespace App\Http\Resources;

use App\Models\Value;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Value
 */
class ValueResource extends JsonResource
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
            'id' => $this->id,
            'text' => $this->text,
            'key' => new KeyResource($this->whenLoaded('key')),
            'language' => new LanguageResource($this->getCachedLanguage()),
            'form' => new FormResource($this->getCachedForm()),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
