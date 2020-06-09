<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectLanguageStoreRequest;
use App\Models\Language;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;

class ProjectLanguageController extends Controller
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
     * Assign the given language to the project.
     *
     * @param  ProjectLanguageStoreRequest  $request
     * @param  Project  $project
     * @return JsonResponse
     */
    public function store(ProjectLanguageStoreRequest $request, Project $project)
    {
        $changes = $this->projectService->attachLanguage($project, $request->input('language_ids'));

        $success = count($changes['attached']) > 0;

        return response()->json([
            'success' => $success,
        ]);
    }

    /**
     * Revoke the given language from the project.
     *
     * @param  Project  $project
     * @param  Language  $language
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(Project $project, Language $language)
    {
        $this->authorize('update', $project);

        $count = $this->projectService->detachLanguage($project, $language);

        $success = $count > 0;

        return response()->json([
            'success' => $success,
        ]);
    }
}
