<?php

namespace App\Services;

use App\Models\Language;
use App\Models\Project;
use App\Models\Team;
use App\Models\Token;
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
     * @param  int  $id
     * @return Project
     */
    public function find(int $id): Project
    {
        return $this->project->find($id);
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
     * @param  mixed  $ttl
     * @return Model
     */
    public function getCached(Project $project, Request $request, $ttl = null): Model
    {
        $callback = function () use ($project, $request) {
            return $this->get($project, $request);
        };

        return $project->remember($callback, $ttl);
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

    /**
     * @param  Project  $project
     * @return string
     */
    public function createToken(Project $project): string
    {
        return $project->createToken('')->plainTextToken;
    }

    /**
     * @param  Project  $project
     * @param  Token  $token
     * @return int
     */
    public function destroyToken(Project $project, Token $token): int
    {
        return $project->tokens()->where('id', $token->id)->delete();
    }
}
