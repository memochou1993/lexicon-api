<?php

namespace App\Services;

use App\Models\Team;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class TeamService
{
    /**
     * @var Team
     */
    private Team $team;

    /**
     * Instantiate a new service instance.
     *
     * @param  Team  $team
     */
    public function __construct(
        Team $team
    ) {
        $this->team = $team;
    }

    /**
     * @param  Team  $team
     * @param  Request  $request
     * @return Model
     */
    public function get(Team $team, Request $request): Model
    {
        return $this->team
            ->with($request->relations ?? [])
            ->find($team->id);
    }

    /**
     * @param  Team  $team
     * @param  Request  $request
     * @return Model
     */
    public function update(Team $team, Request $request): Model
    {
        $team->update($request->all());

        return $team;
    }

    /**
     * @param  Team  $team
     * @return bool
     */
    public function destroy(Team $team): bool
    {
        return $this->team->destroy($team->id);
    }

    /**
     * @param  Team  $team
     * @param  Request  $request
     * @return LengthAwarePaginator
     */
    public function getProjects(Team $team, Request $request): LengthAwarePaginator
    {
        return $team
            ->projects()
            ->when($request->q, function ($query, $q) {
                $query->where('name', 'LIKE', '%'.$q.'%');
            })
            ->with($request->relations ?? [])
            ->paginate($request->per_page);
    }

    /**
     * @param  Team  $team
     * @param  Request  $request
     * @return Model
     */
    public function storeProject(Team $team, Request $request): Model
    {
        return $team->projects()->create($request->all());
    }

    /**
     * @param  Team  $team
     * @param  Request  $request
     * @return Model
     */
    public function storeLanguage(Team $team, Request $request): Model
    {
        $language = $team->languages()->create($request->all());

        if ($request->form_ids) {
            $language->forms()->sync($request->form_ids);
        }

        return $language;
    }

    /**
     * @param  Team  $team
     * @param  Request  $request
     * @return Model
     */
    public function storeForm(Team $team, Request $request): Model
    {
        return $team->forms()->create($request->all());
    }

    /**
     * @param  Team  $team
     * @param  array  $user_ids
     */
    public function attachUser(Team $team, array $user_ids): void
    {
        $team->users()->syncWithoutDetaching($user_ids);
    }

    /**
     * @param  Team  $team
     * @param  int  $user_id
     */
    public function detachUser(Team $team, int $user_id): void
    {
        $team->users()->detach($user_id);
    }
}
