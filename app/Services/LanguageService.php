<?php

namespace App\Services;

use App\Models\Language;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

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
     * @param  array  $request
     * @return Model
     */
    public function get(Language $language, array $request): Model
    {
        return $this->language
            ->with(Arr::get($request, 'relations', []))
            ->find($language->id);
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
        $language->forms()->sync($form_ids, $sync);
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
