<?php

namespace App\Http\Controllers\Api\Project;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CacheController extends Controller
{
    /**
     * @var ProjectService
     */
    private ProjectService $projectService;

    /**
     * Instantiate a new controller instance.
     *
     * @param  ProjectService  $projectService
     */
    public function __construct(
        ProjectService $projectService
    ) {
        $this->projectService = $projectService;
    }

    /**
     * Remove the specified resource from the cache.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function destroy(Request $request)
    {
        /** @var Project $project */
        $project = $request->user();

        $success = $this->projectService->destroyCached($project);

        return response()->json([
            'success' => $success,
        ]);
    }
}
