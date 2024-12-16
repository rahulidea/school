<?php

namespace App\Repositories;

use App\Models\Dorm;
use App\Helpers\Qs;

class DormRepo
{

    public function create($data)
    {
        return Dorm::create($data);
    }

    public function getAll($order = 'name')
    {
        return Dorm::where(['school_id' => QS::getSchoolId()])->orderBy($order)->get();
    }

    public function getDorm($data)
    {
        return Dorm::where($data)->get();
    }

    public function update($id, $data)
    {
        return Dorm::find($id)->update($data);
    }

    public function find($id)
    {
        return Dorm::find($id);
    }


}