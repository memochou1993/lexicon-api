<?php

namespace App\Models;

use App\Models\Traits\HasForms;
use App\Models\Traits\HasLanguages;
use App\Models\Traits\HasUsers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Team extends Model
{
    use HasUsers;
    use HasLanguages;
    use HasForms;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Get the projects for the team.
     *
     * @return HasMany
     */
    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
