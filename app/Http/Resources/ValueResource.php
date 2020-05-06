<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ValueResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'text' => $this->text,
            'language' => $this->whenLoaded('languages', function () {
                return new LanguageResource($this->languages->first());
            }),
            'form' => $this->whenLoaded('forms', function () {
                return new FormResource($this->forms->first());
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
