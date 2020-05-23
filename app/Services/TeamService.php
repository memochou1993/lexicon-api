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
     * @param  array  $data
     * @return Model
     */
    public function update(Team $team, array $data): Model
    {
        $team = $this->team->find($team->id);

        $team->update($data);

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
     * @param  array  $data
     * @return Model
     */
    public function storeProject(Team $team, array $data): Model
    {
        return $team->projects()->create($data);
    }

    /**
     * @param  Team  $team
     * @param  array  $data
     * @param  array|null  $form_ids
     * @return Model
     */
    public function storeLanguage(Team $team, array $data, ?array $form_ids = []): Model
    {
        $language = $team->languages()->create($data);

        $language->forms()->sync($form_ids);

        return $language;
    }

    /**
     * @param  Team  $team
     * @param  array  $data
     * @return Model
     */
    public function storeForm(Team $team, array $data): Model
    {
        return $team->forms()->create($data);
    }

    /**
     * @param  Team  $team
     * @param  array  $user_ids
     * @param  bool  $detaching
     */
    public function attachUser(Team $team, array $user_ids, bool $detaching): void
    {
        $team->users()->sync($user_ids, $detaching);
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
