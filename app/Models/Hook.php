<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $id
 * @property string $url
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Project $project
 */
class Hook extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'url',
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
     * Get the project that owns the hook.
     *
     * @return BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @return Project
     */
    public function getProject(): Project
    {
        $tag = sprintf('%s:%d', $this->getTable(), $this->getKey());

        return Cache::tags($tag)->sear('project', fn() => $this->project);
    }
}
