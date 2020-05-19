<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class UserService
{
    /**
     * @var User
     */
    private User $user;

    /**
     * Instantiate a new service instance.
     *
     * @param  User  $user
     */
    public function __construct(
        User $user
    ) {
        $this->user = $user;
    }

    /**
     * @param  array  $request
     * @return LengthAwarePaginator
     */
    public function getAll(array $request): LengthAwarePaginator
    {
        return $this->user
            ->with(Arr::get($request, 'relations', []))
            ->paginate(Arr::get($request, 'per_page'));
    }

    /**
     * @param  User  $user
     * @param  array  $request
     * @return Model
     */
    public function get(User $user, array $request): Model
    {
        return $this->user
            ->with(Arr::get($request, 'relations', []))
            ->find($user->id);
    }

    /**
     * @param  User  $user
     * @param  array  $data
     * @return Model
     */
    public function update(User $user, array $data): Model
    {
        $user = $this->user->find($user->id);

        $user->update($data);

        return $user;
    }

    /**
     * @param  User  $user
     * @return bool
     */
    public function destroy(User $user): bool
    {
        return $this->user->destroy($user->id);
    }

    /**
     * @param  User  $user
     * @param  array  $request
     * @return LengthAwarePaginator
     */
    public function getTeams(User $user, array $request): LengthAwarePaginator
    {
        return $user
            ->teams()
            ->with(Arr::get($request, 'relations', []))
            ->paginate(Arr::get($request, 'per_page'));
    }

    /**
     * @param  User  $user
     * @param  array  $data
     * @return Model
     */
    public function storeTeam(User $user, array $data): Model
    {
        return $user->teams()->create($data);
    }
}
