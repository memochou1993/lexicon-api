<?php

namespace App\Services;

use App\Models\Language;
use App\Models\Team;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class LanguageService
{
    /**
     * @var Language
     */
    private Language $language;

    /**
     * Instantiate a new service instance.
     *
     * @param  Language  $language
     */
    public function __construct(
        Language $language
    ) {
        $this->language = $language;
    }

    /**
     * @param  Language  $language
     * @param  Request  $request
     * @return Model
     */
    public function get(Language $language, Request $request): Model
    {
        return $this->language
            ->with($request->relations ?? [])
            ->find($language->id);
    }

    /**
     * @param  Team  $team
     * @param  Request  $request
     * @return Model
     */
    public function storeByTeam(Team $team, Request $request): Model
    {
        $language = $team->languages()->create($request->all());

        if ($request->form_ids) {
            $language->forms()->sync($request->form_ids);
        }

        return $language;
    }

    /**
     * @param  Language  $language
     * @param  Request  $request
     * @return Model
     */
    public function update(Language $language, Request $request): Model
    {
        $language->update($request->all());

        if ($request->form_ids) {
            $changed = $language->forms()->sync($request->form_ids);

            $this->destroyValuesByFormIds($language, $changed['detached']);
        }

        return $language;
    }

    /**
     * @param  Language  $language
     * @return bool
     */
    public function destroy(Language $language): bool
    {
        $language->values()->delete();

        return $this->language->destroy($language->id);
    }

    /**
     * @param  Language  $language
     * @param  array  $form_ids
     * @return int
     */
    protected function destroyValuesByFormIds(Language $language, array $form_ids): int
    {
        if (! $form_ids) {
            return 0;
        }

        return $language->values()->whereHas('forms', function ($query) use ($form_ids) {
            $query->whereIn('form_id', $form_ids);
        })->delete();
    }
}
