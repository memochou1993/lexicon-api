<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

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
     * @param  Request  $request
     * @return Model
     */
    public function get(Project $project, Request $request): Model
    {
        return $this->project
            ->with($request->relations ?? [])
            ->find($project->id);
    }

    /**
     * @param  Project  $project
     * @param  Request  $request
     * @return Model
     */
    public function update(Project $project, Request $request): Model
    {
        $project->update($request->all());

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
     * @param  User  $user
     * @param  Request  $request
     * @return LengthAwarePaginator
     */
    public function getByUser(User $user, Request $request): LengthAwarePaginator
    {
        return $user
            ->projects()
            ->with($request->relations ?? [])
            ->orderBy($request->sort ?? 'id', $request->direction ?? 'asc')
            ->paginate($request->per_page);
    }

    /**
     * @param  Project  $project
     * @param  Request  $request
     * @return LengthAwarePaginator
     */
    public function getKeys(Project $project, Request $request): LengthAwarePaginator
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
    public function storeKey(Project $project, Request $request): Model
    {
        return $project->keys()->create($request->all());
    }

    /**
     * @param  Project  $project
     * @param  array  $user_ids
     */
    public function attachUser(Project $project, array $user_ids): void
    {
        $project->users()->syncWithoutDetaching($user_ids);
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
     */
    public function attachLanguage(Project $project, array $language_ids): void
    {
        $project->languages()->syncWithoutDetaching($language_ids);
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
