<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\CacheDestroyRequest;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CacheController extends Controller
{
    /**
     * Remove the specified resource from the cache.
     *
     * @param  CacheDestroyRequest  $request
     * @param  Project  $project
     * @return JsonResponse
     */
    public function destroy(CacheDestroyRequest $request, Project $project)
    {
        $cacheKey = sprintf('projects:%s:%s', $project->id, $request->cache);

        Cache::forget($cacheKey);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
