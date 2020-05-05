<?php

namespace App\Models;

use App\Traits\HasForms;
use App\Traits\HasLanguages;
use App\Traits\HasUsers;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasForms;
    use HasLanguages;
    use HasUsers;

    /**
     * Get the projects for the team.
     */
    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
