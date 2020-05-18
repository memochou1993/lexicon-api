<?php

namespace App\Models;

use App\Traits\HasForms;
use Illuminate\Database\Eloquent\Model;

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
     */
    public function teams()
    {
        return $this->morphedByMany(Team::class, 'model', 'model_has_languages');
    }
}
