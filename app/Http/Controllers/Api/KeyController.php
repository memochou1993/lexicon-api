<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\KeyIndexRequest;
use App\Http\Requests\KeyShowRequest;
use App\Http\Resources\KeyResource as Resource;
use App\Models\Key;
use App\Services\KeyService;
use Illuminate\Http\Request;
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // TODO
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
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // TODO
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // TODO
    }
}
