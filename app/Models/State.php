<?php

namespace App\Models;

use Eloquent;
use App\Models\Lga;

class State extends Eloquent
{
    public function ministry()
    {
       // return $this->hasMany(Ministry::class);
    }
    public function cities(){
        return $this->hasMany(Lga::class);
    }
}
