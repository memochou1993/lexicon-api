<?php

namespace App\Models;

use App\Models\Traits\HasForms;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $id
 * @property string $name
 */
class Language extends Model
{
    use HasForms;

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
     * Get all of the teams that are assigned this languages.
     *
     * @return MorphToMany
     */
    public function teams()
    {
        return $this->morphedByMany(Team::class, 'model', 'model_has_languages');
    }

    /**
     * Get all of the values that are assigned this languages.
     *
     * @return MorphToMany
     */
    public function values()
    {
        return $this->morphedByMany(Value::class, 'model', 'model_has_languages');
    }

    /**
     * @return Team
     */
    public function getTeam(): Team
    {
        $tag = sprintf('%s:%d', $this->getTable(), $this->getKey());

        return Cache::tags($tag)->sear('team', fn() => $this->teams()->first());
    }
}
