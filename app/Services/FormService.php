<?php

namespace App\Services;

use App\Models\Form;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

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
     * @param  array  $request
     * @return Model
     */
    public function get(Form $form, array $request): Model
    {
        return $this->form
            ->with(Arr::get($request, 'relations', []))
            ->find($form->id);
    }

    /**
     * @param  Form  $form
     * @param  array  $data
     * @return Model
     */
    public function update(Form $form, array $data): Model
    {
        $form = $this->form->find($form->id);

        $form->update($data);

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
