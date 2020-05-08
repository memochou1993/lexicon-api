<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Key extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'project_id',
    ];

    /**
     * Get the project that owns the key.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the values for the key.
     */
    public function values()
    {
        return $this->hasMany(Value::class);
    }
}
