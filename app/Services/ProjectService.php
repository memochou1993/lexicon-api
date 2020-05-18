<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class ProjectService
{
    /**
     * @var Project
     */
    private Project $project;

    /**
     * Instantiate a new service instance.
     *
     * @param  Project  $project
     */
    public function __construct(
        Project $project
    ) {
        $this->project = $project;
    }

    /**
     * @param  Project  $project
     * @param  array  $relations
     * @return Model
     */
    public function get(Project $project, array $relations): Model
    {
        return $this->project->with($relations)->find($project->id);
    }

    /**
     * @param  Project  $project
     * @param  array  $data
     * @return Model
     */
    public function update(Project $project, array $data): Model
    {
        $project = $this->project->find($project->id);

        $project->update($data);

        return $project;
    }

    /**
     * @param  Project  $project
     * @return bool
     */
    public function destroy(Project $project): bool
    {
        return $this->project->destroy($project->id);
    }

    /**
     * @param  Project  $project
     * @param  array  $request
     * @return LengthAwarePaginator
     */
    public function getKeys(Project $project, array $request): LengthAwarePaginator
    {
        $keys = $project->keys();

        if (Arr::has($request, 'q')) {
            $keys->where('name', 'LIKE', '%'.$request['q'].'%')
                ->orWhereHas('values', function ($query) use ($request) {
                    $query->where('text', Arr::get($request, 'q'));
                });
        }

        return $keys->with(Arr::get($request, 'relations'))
            ->paginate(Arr::get($request, 'per_page'));
    }

    /**
     * @param  Project  $project
     * @param  array  $data
     * @return Model
     */
    public function storeKey(Project $project, array $data): Model
    {
        return $project->keys()->create($data);
    }

    /**
     * @param  Project  $project
     * @param  array  $user_ids
     * @param  bool  $sync
     */
    public function attachUser(Project $project, array $user_ids, bool $sync): void
    {
        $project->users()->sync($user_ids, $sync);
    }

    /**
     * @param  Project  $project
     * @param  int  $user_id
     */
    public function detachUser(Project $project, int $user_id): void
    {
        $project->users()->detach($user_id);
    }

    /**
     * @param  Project  $project
     * @param  array  $language_ids
     * @param  bool  $sync
     */
    public function attachLanguage(Project $project, array $language_ids, bool $sync): void
    {
        $project->languages()->sync($language_ids, $sync);
    }

    /**
     * @param  Project  $project
     * @param  int  $language_id
     */
    public function detachLanguage(Project $project, int $language_id): void
    {
        $project->languages()->detach($language_id);
    }
}
