<?php

namespace App\Http\Resources;

use App\Models\Hook;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Hook
 */
class HookResource extends JsonResource
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
            'url' => $this->url,
            'events' => $this->events,
            'project' => new ProjectResource($this->getCachedProject()),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
