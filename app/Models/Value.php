<?php

namespace App\Models;

use App\Traits\HasForms;
use App\Traits\HasLanguages;
use Illuminate\Database\Eloquent\Model;

class Value extends Model
{
    use HasForms;
    use HasLanguages;

    /**
     * Get the key that owns the value.
     */
    public function key()
    {
        return $this->belongsTo(Key::class);
    }
}
