<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
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
     * @param  string  $email
     * @param  string  $password
     * @param  string  $device
     * @return mixed
     */
    public function getToken(string $email, string $password, string $device): ?string
    {
        $user = $this->user->firstWhere('email', $email);

        if (! $user || ! Hash::check($password, $user->password)) {
            return null;
        }

        $abilities = $user->roles
            ->flatMap(function ($role) {
                return $role->permissions;
            })
            ->unique()
            ->pluck('name')
            ->toArray();

        return $user->createToken($device, $abilities)->plainTextToken;
    }

    /**
     * @param  Request  $request
     * @return Model
     */
    public function getUser(Request $request): Model
    {
        return $this->user
            ->with($request->relations ?? [])
            ->find(Auth::id());
    }

    /**
     * @param  array  $data
     * @return Model
     */
    public function storeUser(array $data): Model
    {
        return $this->user->create($data);
    }

    /**
     * @param  array  $data
     * @return Model
     */
    public function updateUser(array $data): Model
    {
        $user = Auth::guard()->user();

        $user->update($data);

        return $user;
    }

    /**
     * @return int
     */
    public function destroyTokens(): int
    {
        return Auth::guard()->user()->tokens()->delete();
    }
}
