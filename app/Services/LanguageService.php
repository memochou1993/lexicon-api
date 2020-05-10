<?php

namespace App\Services;

use App\Models\Language;
use App\Models\Team;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

class LanguageService
{
    /**
     * @var Team
     */
    private Team $team;

    /**
     * @var Language
     */
    private Language $language;

    /**
     * Instantiate a new service instance.
     *
     * @param  Team  $team
     * @param  Language  $language
     */
    public function __construct(
        Team $team,
        Language $language
    ) {
        $this->team = $team;
        $this->language = $language;
    }

    /**
     * @param  int  $team_id
     * @param  array  $relations
     * @param  int  $per_page
     * @return LengthAwarePaginator
     */
    public function getByTeam(int $team_id, array $relations, int $per_page): LengthAwarePaginator
    {
        return $this->team->find($team_id)->languages()->with($relations)->paginate($per_page);
    }

    /**
     * @param  int  $team_id
     * @param  array  $data
     * @return Model
     */
    public function storeByTeam(int $team_id, array $data): Model
    {
        return $this->team->find($team_id)->languages()->create($data);
    }

    /**
     * @param  Language  $language
     * @param  array  $relations
     * @return Model
     */
    public function get(Language $language, array $relations): Model
    {
        return $this->language->with($relations)->find($language->id);
    }

    /**
     * @param  Language  $language
     * @param  array  $data
     * @return Model
     */
    public function update(Language $language, array $data): Model
    {
        $language = $this->language->find($language->id);

        $language->update($data);

        return $language;
    }

    /**
     * @param  Language  $language
     * @return bool
     */
    public function destroy(Language $language): bool
    {
        return $this->language->destroy($language->id);
    }

    /**
     * @param  Language  $language
     * @param  array  $form_ids
     * @param  bool  $sync
     */
    public function attachForm(Language $language, array $form_ids, bool $sync): void
    {
        if ($sync) {
            $language->forms()->sync($form_ids);
            return;
        }

        $language->forms()->syncWithoutDetaching($form_ids);
    }

    /**
     * @param  Language  $language
     * @param  int  $form_id
     */
    public function detachForm(Language $language, int $form_id): void
    {
        $language->forms()->detach($form_id);
    }
}
