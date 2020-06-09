<?php

namespace App\Http\Resources;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Team
 */
class TeamResource extends JsonResource
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
            'name' => $this->name,
            'owner' => $this->whenLoaded('owners', function () {
                return new UserResource($this->owners()->first());
            }),
            'users' => UserResource::collection($this->whenLoaded('users')),
            'projects' => ProjectResource::collection($this->whenLoaded('projects')),
            'languages' => LanguageResource::collection($this->whenLoaded('languages')),
            'forms' => FormResource::collection($this->whenLoaded('forms')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
