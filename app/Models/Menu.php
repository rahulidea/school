<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $casts = [
        'user_type_ids' => 'array', // Casts the JSON column to an array
    ];

    // Self-referential relationship for parent-child hierarchy
    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id');
    }

    // Many-to-Many relationship with UserType
    public function userTypes()
    {
        return $this->belongsToMany(UserType::class, 'menu_user_type', 'menu_id', 'user_type_id');
    }
}
