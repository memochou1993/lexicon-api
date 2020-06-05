<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @property int $id
 * @property Collection $teams
 */
class Form extends Model
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
        'range_min',
        'range_max',
    ];

    /**
     * Get all of the teams that are assigned this form.
     *
     * @return MorphToMany
     */
    public function teams()
    {
        return $this->morphedByMany(Team::class, 'model', 'model_has_forms');
    }

    /**
     * Get all of the values that are assigned this form.
     *
     * @return MorphToMany
     */
    public function values()
    {
        return $this->morphedByMany(Value::class, 'model', 'model_has_forms');
    }
}
