<?php

namespace App\Repositories;

use App\Models\BloodGroup;
use App\Models\StaffRecord;
use App\Models\UserType;
use App\User;
use App\Helpers\Qs;


class UserRepo {


    public function update($id, $data)
    {
        return User::find($id)->update($data);
    }

    public function delete($id)
    {
        return User::destroy($id);
    }

    public function create($data)
    {
        return User::create($data);
    }

    public function getUserByType($type)
    {
        dd("asdas");
        return User::where(['user_type' => $type, 'school_id' => QS::getSchoolId()[0]])->orderBy('name', 'asc')->get()->map(function ($user) {
            $user->hashed_id = QS::hash($user->id);
            return $user;
        });
    }

    public function getUserByTypeApi($type)
    {
        return User::wherein('user_type' , $type)->where('school_id' , QS::getSchoolId()[0])->orderBy('name', 'asc')->get()->map(function ($user) {
            $user->hashed_id = QS::hash($user->id);
            return $user;
        });
    }

    public function getAllTypes()
    {
        return UserType::all();
    }

    public function getAllTypesWithHashedId()
    {
        return UserType::orderBy('id', 'asc')->get()->map(function ($userType) {
            $userType->hashed_id = QS::hash($userType->id);
            return $userType;
        });
    }

    public function findType($id)
    {
        return UserType::find($id);
    }

    public function find($id)
    {
        return User::find($id);
    }

    public function getAll()
    {
        return User::orderBy('name', 'asc')->get();
    }

    public function getPTAUsers()
    {
        return User::where('user_type', '<>', 'student')->wherein('school_id',QS::getSchoolId())->orderBy('name', 'asc')->get();
    }

    /********** STAFF RECORD ********/
    public function createStaffRecord($data)
    {
        return StaffRecord::create($data);
    }

    public function updateStaffRecord($where, $data)
    {
        return StaffRecord::where($where)->update($data);
    }

    /********** BLOOD GROUPS ********/
    public function getBloodGroups()
    {
        return BloodGroup::orderBy('name')->get();
    }
}