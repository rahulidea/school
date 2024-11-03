<?php

namespace App\Models;

use App\Models\Organisation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class School extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'organisation_id'];

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }
}
