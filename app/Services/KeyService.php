<?php

namespace App\Services;

use App\Models\Key;
use App\Models\Project;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class KeyService
{
    /**
     * @var Key
     */
    private Key $key;

    /**
     * Instantiate a new service instance.
     *
     * @param  Key  $key
     */
    public function __construct(
        Key $key
    ) {
        $this->key = $key;
    }

    /**
     * @param  Key  $key
     * @param  Request  $request
     * @return Model
     */
    public function get(Key $key, Request $request): Model
    {
        return $this->key
            ->with($request->relations ?? [])
            ->find($key->id);
    }

    /**
     * @param  Project  $project
     * @param  Request  $request
     * @return Collection
     */
    public function getCachedByProject(Project $project, Request $request): Collection
    {
        $cacheKey = sprintf('projects:%s:keys', $project->id);

        return Cache::rememberForever($cacheKey, function () use ($project, $request) {
            return $project
                ->keys()
                ->with($request->relations ?? [])
                ->get();
        });
    }

    /**
     * @param  Project  $project
     * @param  Request  $request
     * @return LengthAwarePaginator
     */
    public function paginateByProject(Project $project, Request $request): LengthAwarePaginator
    {
        return $project
            ->keys()
            ->when($request->q, function ($query, $q) {
                $query
                    ->where('name', 'LIKE', '%'.$q.'%')
                    ->orWhereHas('values', function ($query) use ($q) {
                        $query->where('text', $q);
                    });
            })
            ->with($request->relations ?? [])
            ->orderBy($request->sort ?? 'id', $request->direction ?? 'asc')
            ->paginate($request->per_page);
    }

    /**
     * @param  Project  $project
     * @param  Request  $request
     * @return Model
     */
    public function storeByProject(Project $project, Request $request): Model
    {
        return $project->keys()->create($request->all());
    }

    /**
     * @param  Key  $key
     * @param  Request  $request
     * @return Model
     */
    public function update(Key $key, Request $request): Model
    {
        $key->update($request->all());

        return $key;
    }

    /**
     * @param  Key  $key
     * @return bool
     */
    public function destroy(Key $key): bool
    {
        return $this->key->destroy($key->id);
    }
}
