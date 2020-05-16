<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LanguageFormStoreRequest;
use App\Models\Form;
use App\Models\Language;
use App\Services\LanguageService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class LanguageFormController extends Controller
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
     * Assign the given form to the language.
     *
     * @param  LanguageFormStoreRequest  $request
     * @param  Language  $language
     * @return JsonResponse
     */
    public function store(LanguageFormStoreRequest $request, Language $language)
    {
        $this->languageService->attachForm(
            $language,
            $request->form_ids,
            $request->sync
        );

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Revoke the given form from the language.
     *
     * @param  Language  $language
     * @param  Form  $form
     * @return JsonResponse
     */
    public function destroy(Language $language, Form $form)
    {
        $this->languageService->detachForm($language, $form->id);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
