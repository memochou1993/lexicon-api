<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\HookStoreRequest;
use App\Http\Requests\HookShowRequest;
use App\Http\Requests\HookUpdateRequest;
use App\Http\Resources\HookResource;
use App\Models\Hook;
use App\Models\Project;
use App\Services\HookService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class HookController extends Controller
{
    /**
     * @var HookService
     */
    private HookService $hookService;

    /**
     * Instantiate a new controller instance.
     *
     * @param  HookService  $hookService
     */
    public function __construct(
        HookService $hookService
    ) {
        $this->hookService = $hookService;

        // TODO: use policy
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  HookStoreRequest  $request
     * @param  Project  $project
     * @return HookResource
     */
    public function store(HookStoreRequest $request, Project $project)
    {
        $hook = $this->hookService->storeByProject($project, $request);

        return new HookResource($hook);
    }

    /**
     * Display the specified resource.
     *
     * @param  HookShowRequest  $request
     * @param  Hook  $hook
     * @return HookResource
     */
    public function show(HookShowRequest $request, Hook $hook)
    {
        $hook = $this->hookService->get($hook, $request);

        return new HookResource($hook);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  HookUpdateRequest  $request
     * @param  Hook  $hook
     * @return HookResource
     */
    public function update(HookUpdateRequest $request, Hook $hook)
    {
        $hook = $this->hookService->update($hook, $request);

        return new HookResource($hook);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Hook  $hook
     * @return JsonResponse
     */
    public function destroy(Hook $hook)
    {
        $this->hookService->destroy($hook);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
