<?php

namespace App\Http\Resources\Project;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Project
 */
class ProjectResource extends JsonResource
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
            'languages' => LanguageResource::collection($this->whenLoaded('languages')),
            'keys' => KeyResource::collection($this->whenLoaded('keys')),
        ];
    }
}
