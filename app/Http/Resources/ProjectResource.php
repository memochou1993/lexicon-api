<?php

namespace App\Http\Resources;

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
            'id' => $this->id,
            'name' => $this->name,
            'users' => UserResource::collection($this->whenLoaded('users')),
            'team' => new TeamResource($this->whenLoaded('team')),
            'languages' => LanguageResource::collection($this->whenLoaded('languages')),
            'keys' => KeyResource::collection($this->whenLoaded('keys')),
            'values' => ValueResource::collection($this->whenLoaded('values')),
            'tokens' => TokenResource::collection($this->whenLoaded('tokens')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
