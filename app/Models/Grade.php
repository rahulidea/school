<?php

namespace App\Models;

use Eloquent;

class Grade extends Eloquent
{
    protected $fillable = ['name', 'school_id', 'class_type_id', 'mark_from', 'mark_to', 'remark'];

    public function class_type()
    {
        return $this->belongsTo(ClassType::class);
    }
}
