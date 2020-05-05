<?php

namespace App\Models;

use App\Traits\HasLanguages;
use App\Traits\HasUsers;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasLanguages;
    use HasUsers;

    /**
     * Get the keys for the project.
     */
    public function keys()
    {
        return $this->hasMany(Key::class);
    }

    /**
     * Get all of the values for the project.
     */
    public function values()
    {
        return $this->hasManyThrough(Value::class, Key::class);
    }
}
