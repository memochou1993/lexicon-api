<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LanguageShowRequest;
use App\Http\Requests\LanguageUpdateRequest;
use App\Http\Requests\LanguageStoreRequest;
use App\Http\Resources\LanguageResource as Resource;
use App\Models\Language;
use App\Models\Team;
use App\Services\LanguageService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class LanguageController extends Controller
{
    /**
     * @var LanguageService
     */
    private LanguageService $languageService;

    /**
     * Instantiate a new controller instance.
     *
     * @param  LanguageService  $languageService
     */
    public function __construct(
        LanguageService $languageService
    ) {
        $this->authorizeResource(Language::class);

        $this->languageService = $languageService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  LanguageStoreRequest  $request
     * @param  Team  $team
     * @return Resource
     */
    public function store(LanguageStoreRequest $request, Team $team)
    {
        $language = $this->languageService->store($team, $request);

        return new Resource($language);
    }

    /**
     * Display the specified resource.
     *
     * @param  LanguageShowRequest  $request
     * @param  Language  $language
     * @return Resource
     */
    public function show(LanguageShowRequest $request, Language $language)
    {
        $language = $this->languageService->get($language, $request);

        return new Resource($language);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  LanguageUpdateRequest  $request
     * @param  Language  $language
     * @return Resource
     */
    public function update(LanguageUpdateRequest $request, Language $language)
    {
        $language = $this->languageService->update($language, $request);

        return new Resource($language);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Language  $language
     * @return JsonResponse
     */
    public function destroy(Language $language)
    {
        $this->languageService->destroy($language);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
