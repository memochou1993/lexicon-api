<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ValueStoreRequest;
use App\Http\Requests\ValueShowRequest;
use App\Http\Requests\ValueUpdateRequest;
use App\Http\Resources\ValueResource as Resource;
use App\Models\Key;
use App\Models\Value;
use App\Services\ValueService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ValueController extends Controller
{
    /**
     * @var ValueService
     */
    private ValueService $valueService;

    /**
     * Instantiate a new controller instance.
     *
     * @param  ValueService  $valueService
     */
    public function __construct(
        ValueService $valueService
    ) {
        $this->authorizeResource(Value::class);

        $this->valueService = $valueService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ValueStoreRequest  $request
     * @param  Key  $key
     * @return Resource
     */
    public function store(ValueStoreRequest $request, Key $key)
    {
        $value = $this->valueService->storeByKey($key, $request);

        return new Resource($value);
    }

    /**
     * Display the specified resource.
     *
     * @param  ValueShowRequest  $request
     * @param  Value  $value
     * @return Resource
     */
    public function show(ValueShowRequest $request, Value $value)
    {
        $value = $this->valueService->get($value, $request);

        return new Resource($value);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ValueUpdateRequest  $request
     * @param  Value  $value
     * @return Resource
     */
    public function update(ValueUpdateRequest $request, Value $value)
    {
        $value = $this->valueService->update($value, $request);

        return new Resource($value);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Value  $value
     * @return JsonResponse
     */
    public function destroy(Value $value)
    {
        $this->valueService->destroy($value);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
