<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\KeyIndexRequest;
use App\Http\Resources\Client\KeyResource as Resource;
use App\Models\Project;
use App\Services\KeyService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class KeyController extends Controller
{
    /**
     * @var KeyService
     */
    private KeyService $keyService;

    /**
     * Instantiate a new controller instance.
     *
     * @param  KeyService  $keyService
     */
    public function __construct(
        KeyService $keyService
    ) {
        $this->keyService = $keyService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  KeyIndexRequest  $request
     * @param  Project  $project
     * @return AnonymousResourceCollection
     */
    public function index(KeyIndexRequest $request, Project $project)
    {
        $keys = $this->keyService->getCachedByProject($project, $request);

        return Resource::collection($keys);
    }
}
