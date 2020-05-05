<?php

namespace App\Models;

use App\Traits\HasLanguages;
use App\Traits\HasUsers;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasLanguages;
    use HasUsers;
}
