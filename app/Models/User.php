<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Set the user's password.
     *
     * @param $value
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * The roles that belong to the user.
     *
     * @return BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * The permissions that belong to the user.
     *
     * @return Collection
     */
    public function permissions()
    {
        return $this->roles->flatMap(function ($role) {
            return $role->permissions;
        });
    }

    /**
     * Get all of the teams that are assigned this user.
     *
     * @return MorphToMany
     */
    public function teams()
    {
        return $this->morphedByMany(Team::class, 'model', 'model_has_users');
    }

    /**
     * Get all of the projects that are assigned this user.
     *
     * @return MorphToMany
     */
    public function projects()
    {
        return $this->morphedByMany(Project::class, 'model', 'model_has_users');
    }

    /**
     * Determine if the user has the given role.
     *
     * @param  Role|array|string  $roles
     * @return bool
     */
    public function hasRole(...$roles)
    {
        return collect($roles)
            ->flatten()
            ->some(function ($role) {
                if (is_string($role)) {
                    // TODO: make function
                    $role = Role::where('name', $role)->first();
                }

                return $this->roles->contains($role);
            });
    }

    /**
     * Determine if the user has the given permission.
     *
     * @param  Permission|array|string  $permissions
     * @return bool
     */
    public function hasPermission(...$permissions)
    {
        return collect($permissions)
            ->flatten()
            ->some(function ($permission) {
                if (is_string($permission)) {
                    // TODO: make function
                    $permission = Permission::where('name', $permission)->firstOrNew();
                }

                return $this->hasRole($permission->roles);
            });
    }

    /**
     * Determine if the user has the given team.
     *
     * @param  Team  $team
     * @return bool
     */
    public function hasTeam(Team $team)
    {
        return $this->teams->contains($team);
    }

    /**
     * Determine if the user has the given project.
     *
     * @param  Project  $project
     * @return bool
     */
    public function hasProject(Project $project)
    {
        return $this->projects->contains($project);
    }

    /**
     * Assign the given role to the user.
     *
     * @param  Role|array|string  $roles
     */
    public function assignRoles(...$roles)
    {
        collect($roles)
            ->flatten()
            ->each(function ($role) {
                if (is_string($role)) {
                    // TODO: make function
                    $role = Permission::where('name', $role)->first();
                }

                $this->roles()->attach($role);
            });
    }
}
