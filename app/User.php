<?php

namespace App;

use App\Helpers\Qs;
use App\Models\BloodGroup;
use App\Models\Lga;
use App\Models\Nationality;
use App\Models\StaffRecord;
use App\Models\State;
use App\Models\StudentRecord;
use App\Models\Organisation;
use App\Models\UserType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'email', 'phone', 'phone2', 'dob', 'gender', 'photo', 'address', 'bg_id', 'password', 'nal_id', 'state_id', 'lga_id', 'code', 'user_type', 'email_verified_at', 'school_id', 'organisation_id'
    ];

    protected $appends = ['hashed_id'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    public function student_record()
    {
        return $this->hasOne(StudentRecord::class);
    }

    public function lga()
    {
        return $this->belongsTo(Lga::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function nationality()
    {
        return $this->belongsTo(Nationality::class, 'nal_id');
    }

    public function blood_group()
    {
        return $this->belongsTo(BloodGroup::class, 'bg_id');
    }

    public function staff()
    {
        return $this->hasMany(StaffRecord::class);
    }

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function getUserTypeId()
    {
        return UserType::where('title', $this->user_type)->value('id');
    }

    public function getHashedIdAttribute()
    {
        return QS::hash($this->id);
    }
}
