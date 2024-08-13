<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Organisation;

class Subscription extends Model
{
    use HasFactory;

    public function organisations()
    {
        return $this->hasMany(Organisation::class);
    }
}
