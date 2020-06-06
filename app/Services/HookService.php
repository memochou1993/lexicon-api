<?php

namespace App\Services;

use App\Models\Hook;
use App\Models\Project;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class HookService
{
    /**
     * @var Hook
     */
    private Hook $hook;

    /**
     * Instantiate a new service instance.
     *
     * @param  Hook  $hook
     */
    public function __construct(
        Hook $hook
    ) {
        $this->hook = $hook;
    }

    /**
     * @param  Hook  $hook
     * @param  Request  $request
     * @return Model|Hook
     */
    public function get(Hook $hook, Request $request): Hook
    {
        return $this->hook
            ->with($request->input('relations', []))
            ->find($hook->id);
    }

    /**
     * @param  Project  $project
     * @param  Request  $request
     * @return Model|Hook
     */
    public function store(Project $project, Request $request): Hook
    {
        return $project->hooks()->create($request->all());
    }

    /**
     * @param  Hook  $hook
     * @param  Request  $request
     * @return Model|Hook
     */
    public function update(Hook $hook, Request $request): Hook
    {
        $hook->update($request->all());

        return $hook;
    }

    /**
     * @param  Hook  $hook
     * @return bool
     */
    public function destroy(Hook $hook): bool
    {
        return $this->hook->destroy($hook->id);
    }
}
