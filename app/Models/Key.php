<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Key extends Model
{
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
