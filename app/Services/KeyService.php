<?php

namespace App\Services;

use App\Models\Key;
use App\Models\Project;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

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
     * @return Model|Key
     */
    public function get(Key $key, Request $request): Key
    {
        return $this->key
            ->with($request->input('relations', []))
            ->find($key->id);
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
            ->when($request->input('q'), function ($query, $q) {
                $query
                    ->where('name', 'LIKE', '%'.$q.'%')
                    ->orWhereHas('values', function ($query) use ($q) {
                        $query->where('text', $q);
                    });
            })
            ->with($request->input('relations', []))
            ->orderBy($request->input('sort', 'id'), $request->input('direction', 'asc'))
            ->paginate($request->input('per_page'));
    }

    /**
     * @param  Project  $project
     * @param  Request  $request
     * @return Model|Key
     */
    public function store(Project $project, Request $request): Key
    {
        return $project->keys()->create($request->all());
    }

    /**
     * @param  Key  $key
     * @param  Request  $request
     * @return Model|Key
     */
    public function update(Key $key, Request $request): Key
    {
        $key->update($request->all());

        return $key->withoutRelations();
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
