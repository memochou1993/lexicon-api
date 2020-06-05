<?php

namespace App\Http\Resources\Client;

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
            'text' => $this->text,
            'language' => $this->whenLoaded('languages', function () {
                return new LanguageResource($this->languages->first());
            }),
            'form' => $this->whenLoaded('forms', function () {
                return new FormResource($this->forms->first());
            }),
        ];
    }
}
