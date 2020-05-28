<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
     * @param  User  $user
     * @param  Request  $request
     * @return LengthAwarePaginator
     */
    public function paginateByUser(User $user, Request $request): LengthAwarePaginator
    {
        return $user
            ->projects()
            ->with($request->relations ?? [])
            ->orderBy($request->sort ?? 'id', $request->direction ?? 'asc')
            ->paginate($request->per_page);
    }

    /**
     * @param  Team  $team
     * @param  Request  $request
     * @return LengthAwarePaginator
     */
    public function paginateByTeam(Team $team, Request $request): LengthAwarePaginator
    {
        return $team
            ->projects()
            ->when($request->q, function ($query, $q) {
                $query->where('name', 'LIKE', '%'.$q.'%');
            })
            ->with($request->relations ?? [])
            ->orderBy($request->sort ?? 'id', $request->direction ?? 'asc')
            ->paginate($request->per_page);
    }

    /**
     * @param  Team  $team
     * @param  Request  $request
     * @return Model
     */
    public function storeByTeam(Team $team, Request $request): Model
    {
        $request->merge([
            'api_keys' => json_encode([
                'public_key' => Str::random(36),
                'secret_key' => Str::random(36),
            ]),
        ]);

        return $team->projects()->create($request->all());
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
     * @param  Project  $project
     * @param  array  $user_ids
     * @return bool
     */
    public function attachUser(Project $project, array $user_ids): bool
    {
        $changes = $project->users()->syncWithoutDetaching($user_ids);

        return count($changes['attached']) > 0;
    }

    /**
     * @param  Project  $project
     * @param  int  $user_id
     * @return bool
     */
    public function detachUser(Project $project, int $user_id): bool
    {
        $detached = $project->users()->detach($user_id);

        return $detached > 0;
    }

    /**
     * @param  Project  $project
     * @param  array  $language_ids
     * @return bool
     */
    public function attachLanguage(Project $project, array $language_ids): bool
    {
        $changes = $project->languages()->syncWithoutDetaching($language_ids);

        return count($changes['attached']) > 0;
    }

    /**
     * @param  Project  $project
     * @param  int  $language_id
     * @return bool
     */
    public function detachLanguage(Project $project, int $language_id): bool
    {
        $detached = $project->languages()->detach($language_id);

        return $detached > 0;
    }
}
