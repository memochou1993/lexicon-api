<?php

namespace App\Services;

use App\Models\Form;
use App\Models\Team;
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
     * @param  Team  $team
     * @param  Request  $request
     * @return Model
     */
    public function storeByTeam(Team $team, Request $request): Model
    {
        return $team->forms()->create($request->all());
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
        $form->values()->delete();

        return $this->form->destroy($form->id);
    }
}
