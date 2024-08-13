<?php

namespace App\Models;

use App\User;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Organisation extends Model
{
    use HasFactory;

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
