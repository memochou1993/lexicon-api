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
}
