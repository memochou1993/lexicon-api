<?php

namespace App\Services;

use App\Models\Form;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class FormService
{
    /**
     * @var Form
     */
    private Form $form;

    /**
     * Instantiate a new service instance.
     *
     * @param  Form  $form
     */
    public function __construct(
        Form $form
    ) {
        $this->form = $form;
    }

    /**
     * @param  Form  $form
     * @param  Request  $request
     * @return Model
     */
    public function get(Form $form, Request $request): Model
    {
        return $this->form
            ->with($request->relations ?? [])
            ->find($form->id);
    }

    /**
     * @param  Form  $form
     * @param  Request  $request
     * @return Model
     */
    public function update(Form $form, Request $request): Model
    {
        $form->update($request->all());

        return $form;
    }

    /**
     * @param  Form  $form
     * @return bool
     */
    public function destroy(Form $form): bool
    {
        return $this->form->destroy($form->id);
    }
}
