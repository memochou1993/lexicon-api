<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectLanguageStoreRequest;
use App\Models\Language;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

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
     * Assign the given form to the language.
     *
     * @param  ProjectLanguageStoreRequest  $request
     * @param  Project  $project
     * @return JsonResponse
     */
    public function store(ProjectLanguageStoreRequest $request, Project $project)
    {
        $this->projectService->attachLanguage(
            $project,
            $request->language_ids,
            $request->sync
        );

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Revoke the given language from the project.
     *
     * @param  Project  $project
     * @param  Language  $language
     * @return JsonResponse
     */
    public function destroy(Project $project, Language $language)
    {
        $this->projectService->detachLanguage($project, $language->id);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
