<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LanguageStoreRequest;
use App\Http\Resources\LanguageResource as Resource;
use App\Services\LanguageService;
use Illuminate\Http\Request;

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
        $this->languageService = $languageService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  LanguageStoreRequest  $request
     * @return Resource
     */
    public function store(LanguageStoreRequest $request)
    {
        $language = $this->languageService->storeByProject(
            $request->project_id,
            $request->all()
        );

        return new Resource($language);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
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
