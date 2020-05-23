<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FormShowRequest;
use App\Http\Requests\FormUpdateRequest;
use App\Http\Resources\FormResource as Resource;
use App\Models\Form;
use App\Services\FormService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class FormController extends Controller
{
    /**
     * @var FormService
     */
    private FormService $formService;

    /**
     * Instantiate a new controller instance.
     *
     * @param  FormService  $formService
     */
    public function __construct(
        FormService $formService
    ) {
        $this->authorizeResource(Form::class);

        $this->formService = $formService;
    }

    /**
     * Display the specified resource.
     *
     * @param  FormShowRequest  $request
     * @param  Form  $form
     * @return Resource
     */
    public function show(FormShowRequest $request, Form $form)
    {
        $form = $this->formService->get($form, $request);

        return new Resource($form);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  FormUpdateRequest  $request
     * @param  Form  $form
     * @return Resource
     */
    public function update(FormUpdateRequest $request, Form $form)
    {
        $form = $this->formService->update($form, $request);

        return new Resource($form);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Form  $form
     * @return JsonResponse
     */
    public function destroy(Form $form)
    {
        $this->formService->destroy($form);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
