<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\KeyIndexRequest;
use App\Http\Requests\KeyShowRequest;
use App\Http\Requests\KeyStoreRequest;
use App\Http\Requests\KeyUpdateRequest;
use App\Http\Resources\KeyResource as Resource;
use App\Models\Key;
use App\Services\KeyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

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
     * @return AnonymousResourceCollection
     */
    public function index(KeyIndexRequest $request)
    {
        $keys = $this->keyService->getByProject(
            $request->project_id,
            $request->relations,
            $request->per_page
        );

        return Resource::collection($keys);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  KeyStoreRequest  $request
     * @return Resource
     */
    public function store(KeyStoreRequest $request)
    {
        $key = $this->keyService->storeByProject(
            $request->project_id,
            $request->all()
        );

        return new Resource($key);
    }

    /**
     * Display the specified resource.
     *
     * @param  KeyShowRequest  $request
     * @param  Key  $key
     * @return Resource
     */
    public function show(KeyShowRequest $request, Key $key)
    {
        $key = $this->keyService->get($key, $request->relations);

        return new Resource($key);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  KeyUpdateRequest  $request
     * @param  Key  $key
     * @return Resource
     */
    public function update(KeyUpdateRequest $request, Key $key)
    {
        $key = $this->keyService->update($key, $request->all());

        return new Resource($key);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Key  $key
     * @return JsonResponse
     */
    public function destroy(Key $key)
    {
        $this->keyService->destroy($key);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
