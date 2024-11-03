<?php

namespace App\Repositories;

use App\Models\Organisation;
use App\Models\School;
use App\Helpers\Qs;

class OrganisationRepo
{
    public function all($id)
    {
        return Organisation::orderBy('name', 'asc')->where("id",($id>0)?"=":">",$id)->get();
    }

    public function createOrg($data)
    {
        return Organisation::create($data);
    }
    
    public function updateOrg($id, $data)
    {
        return Organisation::find($id)->update($data);
    }

    public function deleteOrg($id){
        return Organisation::destroy($id);
    }

    public function allSchool($id)
    {   
        return School::orderBy('name', 'asc')->where("id",($id>0)?"=":">",$id)->get();
    }

    public function createSchool($data)
    {
        return School::create($data);
    }
    
    public function updateSchool($id, $data)
    {
        return School::find($id)->update($data);
    }

    public function deleteSchool($id){
        return School::destroy($id);
    }
}