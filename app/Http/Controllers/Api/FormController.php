<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FormIndexRequest;
use App\Http\Requests\FormShowRequest;
use App\Http\Requests\FormStoreRequest;
use App\Http\Requests\FormUpdateRequest;
use App\Http\Resources\FormResource as Resource;
use App\Models\Form;
use App\Services\FormService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
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
        $this->formService = $formService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  FormIndexRequest  $request
     * @return AnonymousResourceCollection
     */
    public function index(FormIndexRequest $request)
    {
        $form = $this->formService->getByTeam(
            $request->team_id,
            $request->relations,
            $request->per_page
        );

        return Resource::collection($form);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  FormStoreRequest  $request
     * @return Resource
     */
    public function store(FormStoreRequest $request)
    {
        $form = $this->formService->storeByTeam(
            $request->team_id,
            $request->all()
        );

        return new Resource($form);
    }

    /**
     * Display the specified resource.
     * @param  FormShowRequest  $request
     * @param  Form  $form
     * @return Resource
     */
    public function show(FormShowRequest $request, Form $form)
    {
        $form = $this->formService->get($form, $request->relations);

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
        $form = $this->formService->update($form, $request->all());

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
