<?php

namespace App\Services;

use App\Models\Key;
use App\Models\Project;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

class KeyService
{
    /**
     * @var Project
     */
    private Project $project;

    /**
     * @var Key
     */
    private Key $key;

    /**
     * Instantiate a new service instance.
     *
     * @param  Project  $project
     * @param  Key  $key
     */
    public function __construct(
        Project $project,
        Key $key
    ) {
        $this->project = $project;
        $this->key = $key;
    }

    /**
     * @param  int  $project_id
     * @param  array  $relations
     * @param  int  $per_page
     * @return LengthAwarePaginator
     */
    public function getByProject(int $project_id, array $relations, int $per_page): LengthAwarePaginator
    {
        return $this->project->find($project_id)->keys()->with($relations)->paginate($per_page);
    }

    /**
     * @param  Key  $key
     * @param  array  $relations
     * @return Model
     */
    public function get(Key $key, array $relations): Model
    {
        return $this->key->with($relations)->find($key->id);
    }

    /**
     * @param  int  $projectId
     * @param  array  $data
     * @return Model
     */
    public function storeByProject(int $projectId, array $data): Model
    {
        return $this->project->find($projectId)->keys()->create($data);
    }

    /**
     * @param  Key  $key
     * @param  array  $data
     * @return Model
     */
    public function update(Key $key, array $data): Model
    {
        $key = $this->key->find($key->id);

        $key->update($data);

        return $key;
    }

    /**
     * @param  Key  $key
     * @return bool
     */
    public function destroy(Key $key): bool
    {
        return $this->key->destroy($key->id);
    }
}
