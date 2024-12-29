<?php

namespace App\Models;

use App\User;
use App\Models\School;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Organisation extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name', 'subscription_id', 'expiry_date', 'email'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function schools()
    {
        return $this->hasMany(School::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
