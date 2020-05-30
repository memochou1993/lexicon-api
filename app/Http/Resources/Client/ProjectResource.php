<?php

namespace App\Http\Resources\Client;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
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
            'name' => $this->name,
            'languages' => LanguageResource::collection($this->whenLoaded('languages')),
            'keys' => KeyResource::collection($this->whenLoaded('keys')),
        ];
    }
}
