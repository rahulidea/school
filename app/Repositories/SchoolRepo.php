<?php

namespace App\Repositories;

use App\Models\School;

class SchoolRepo
{

    public function create($data)
    {
        return School::create($data);
    }

    public function getAll()
    {
        return School::all();
    }

    public function update($id, $data)
    {
        return School::find($id)->update($data);
    }

    public function find($id)
    {
        return School::find($id);
    }


}