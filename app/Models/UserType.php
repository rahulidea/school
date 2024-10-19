<?php

namespace App\Models;

use Eloquent;
use App\Helpers\Qs;

class UserType extends Eloquent
{
    protected $appends = ['hashed_id'];

    public function getHashedIdAttribute()
    {
        return QS::hash($this->id);
    }
}
