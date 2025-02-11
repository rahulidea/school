<?php

namespace App\Models;

use App\Models\Organisation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class School extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'organisation_id', 'address', 'email', 'phone', 'website', 'logo', 'medium_of_instruction', 'affiliation', 'accreditation', 'facility', 'aanual_event'];

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }
}
