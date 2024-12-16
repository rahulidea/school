<?php

namespace App\Models;

use Eloquent;

class Dorm extends Eloquent
{
    protected $fillable = ['name', 'school_id', 'description'];
}
