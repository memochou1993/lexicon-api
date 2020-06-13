<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\Hook;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class EventController extends Controller
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
     * Dispatch events to client.
     *
     * @param  Project  $project
     * @return JsonResponse
     */
    public function index(Project $project)
    {
        $project->hooks->each(function (/** @var Hook $hook */ $hook) use ($project) {
            Http::retry(3, 500)
                ->withHeaders([
                    'X-Localize-API-Key' => $project->getSetting('api_key'),
                ])
                ->post($hook->url, [
                    'events' => $hook->events,
                ])
                ->throw();
        });

        return response()->json(null, Response::HTTP_ACCEPTED);
    }
}
