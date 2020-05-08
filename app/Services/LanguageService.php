<?php

namespace App\Services;

use App\Models\Language;
use App\Models\Team;
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
     * @param  array  $data
     * @return Model
     */
    public function storeByTeam(int $team_id, array $data): Model
    {
        return $this->team->find($team_id)->languages()->create($data);
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
}
