<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * The users that belong to the role.
     *
     * @return BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * The permissions that belong to the role.
     *
     * @return BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    /**
     * Assign the given permission to the role.
     *
     * @param  Permission|array|string  $permissions
     */
    public function assignPermissions(...$permissions)
    {
        collect($permissions)
            ->flatten()
            ->each(function ($permission) {
                if (is_string($permission)) {
                    // TODO: make function
                    $permission = Permission::where('name', $permission)->first();
                }

                $this->permissions()->attach($permission);
            });
    }
}
