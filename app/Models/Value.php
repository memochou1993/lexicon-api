<?php

namespace App\Models;

use App\Traits\HasForms;
use App\Traits\HasLanguages;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Value extends Model
{
    use HasLanguages;
    use HasForms;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'text',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'key_id',
        'laravel_through_key',
    ];

    /**
     * Get the key that owns the value.
     *
     * @return BelongsTo
     */
    public function key()
    {
        return $this->belongsTo(Key::class);
    }
}
