<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Helpers\Qs;

use Illuminate\Support\Facades\DB;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\APIController;

class UserHomeController extends APIController
{
    public function userHome(Request $request)
    {

        try {
           $userCounts= User::select('user_type', DB::raw('count(*) as count'))
            ->groupBy('user_type')
            ->get();
        } catch (Exception $e) {
            return $this->respondInternalError($e->getMessage());
        }

        return $this->respond('succes',
             $userCounts
        );
    }
    public function getSchools() {
        $d = QS::getSchool();

        return $this->respond('succes',$d);
    }
}
