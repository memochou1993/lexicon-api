<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

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
     * @param  array  $relations
     * @param  int  $per_page
     * @return LengthAwarePaginator
     */
    public function getAll(array $relations, int $per_page): LengthAwarePaginator
    {
        return $this->user->with($relations)->paginate($per_page);
    }

    /**
     * @param  User  $user
     * @param  array  $relations
     * @return Model
     */
    public function get(User $user, array $relations): Model
    {
        return $this->user->with($relations)->find($user->id);
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
}
