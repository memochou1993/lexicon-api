<?php

namespace App\Services;

use App\Models\Form;
use App\Models\Team;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

class FormService
{
    /**
     * @var Team
     */
    private Team $team;

    /**
     * @var Form
     */
    private Form $form;

    /**
     * Instantiate a new service instance.
     *
     * @param  Team  $team
     * @param  Form  $form
     */
    public function __construct(
        Team $team,
        Form $form
    ) {
        $this->team = $team;
        $this->form = $form;
    }

    /**
     * @param  int  $team_id
     * @param  array  $relations
     * @param  int  $per_page
     * @return LengthAwarePaginator
     */
    public function getByTeam(int $team_id, array $relations, int $per_page): LengthAwarePaginator
    {
        return $this->team->find($team_id)->forms()->with($relations)->paginate($per_page);
    }

    /**
     * @param  int  $team_id
     * @param  array  $data
     * @return Model
     */
    public function storeByTeam(int $team_id, array $data): Model
    {
        return $this->team->find($team_id)->forms()->create($data);
    }

    /**
     * @param  Form  $form
     * @param  array  $relations
     * @return Model
     */
    public function get(Form $form, array $relations): Model
    {
        return $this->form->with($relations)->find($form->id);
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
