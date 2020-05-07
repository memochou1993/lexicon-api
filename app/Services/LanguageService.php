<?php

namespace App\Services;

use App\Models\Language;
use App\Models\Project;
use Illuminate\Database\Eloquent\Model;

class LanguageService
{
    /**
     * @var Project
     */
    private Project $project;

    /**
     * @var Language
     */
    private Language $language;

    /**
     * Instantiate a new service instance.
     *
     * @param  Project  $project
     * @param  Language  $language
     */
    public function __construct(
        Project $project,
        Language $language
    ) {
        $this->project = $project;
        $this->language = $language;
    }

    /**
     * @param  int  $project_id
     * @param  array  $data
     * @return Model
     */
    public function storeByProject(int $project_id, array $data): Model
    {
        $project = $this->project->find($project_id);
        $language = $project->languages()->create($data);
        $project->team->languages()->attach($language->id);

        return $language;
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
}
