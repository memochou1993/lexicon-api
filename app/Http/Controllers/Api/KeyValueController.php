<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\KeyValueStoreRequest;
use App\Http\Resources\ValueResource as Resource;
use App\Models\Key;
use App\Services\KeyService;

class KeyValueController extends Controller
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
     * Store a newly created resource in storage.
     *
     * @param  KeyValueStoreRequest  $request
     * @param  Key  $key
     * @return Resource
     */
    public function store(KeyValueStoreRequest $request, Key $key)
    {
        $value = $this->keyService->storeValue($key, $request);

        return new Resource($value);
    }
}
