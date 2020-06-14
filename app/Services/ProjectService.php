<?php

namespace App\Services;

use App\Models\Language;
use App\Models\Project;
use App\Models\Team;
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
     * @param  int  $id
     * @return Model|Project
     */
    public function find(int $id): Project
    {
        return $this->project->query()->find($id);
    }

    /**
     * @param  Project  $project
     * @param  Request  $request
     * @return Model|Project
     */
    public function get(Project $project, Request $request): Project
    {
        return $this->project
            ->with($request->input('relations', []))
            ->find($project->id);
    }

    /**
     * @param  Project  $project
     * @param  Request  $request
     * @return Model|Project
     */
    public function getCached(Project $project, Request $request): Project
    {
        $callback = function () use ($project, $request) {
            return $this->get($project, $request);
        };

        return $project->remember($callback);
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
            ->with($request->input('relations', []))
            ->orderBy($request->input('sort', 'id'), $request->input('direction', 'asc'))
            ->paginate($request->input('per_page'));
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
            ->when($request->input('q'), function ($query, $q) {
                $query->where('name', 'LIKE', '%'.$q.'%');
            })
            ->with($request->input('relations', []))
            ->orderBy($request->input('sort', 'id'), $request->input('direction', 'asc'))
            ->paginate($request->input('per_page'));
    }

    /**
     * @param  Team  $team
     * @param  Request  $request
     * @return Model|Project
     */
    public function store(Team $team, Request $request): Project
    {
        return $team->projects()->create($request->all());
    }

    /**
     * @param  Project  $project
     * @param  Request  $request
     * @return Model|Project
     */
    public function update(Project $project, Request $request): Project
    {
        $project->update($request->all());

        return $project->withoutRelations();
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
     * @return bool
     */
    public function destroyCached(Project $project): bool
    {
        return $project->forget();
    }

    /**
     * @param  Project  $project
     * @param  array  $user_ids
     * @return array
     */
    public function attachUser(Project $project, array $user_ids): array
    {
        return $project->users()->syncWithoutDetaching($user_ids);
    }

    /**
     * @param  Project  $project
     * @param  User  $user
     * @return int
     */
    public function detachUser(Project $project, User $user): int
    {
        return $project->users()->detach($user);
    }

    /**
     * @param  Project  $project
     * @param  array  $language_ids
     * @return array
     */
    public function attachLanguage(Project $project, array $language_ids): array
    {
        return $project->languages()->syncWithoutDetaching($language_ids);
    }

    /**
     * @param  Project  $project
     * @param  Language  $language
     * @return int
     */
    public function detachLanguage(Project $project, Language $language): int
    {
        $this->destroyValuesByLanguageIds($project, (array) $language->id);

        return $project->languages()->detach($language);
    }

    /**
     * @param  Project  $project
     * @param  array  $language_ids
     * @return int
     */
    protected function destroyValuesByLanguageIds(Project $project, array $language_ids): int
    {
        if (! $language_ids) {
            return 0;
        }

        return $project->values()->whereHas('languages', function ($query) use ($language_ids) {
            $query->whereIn('language_id', $language_ids);
        })->delete();
    }
}
