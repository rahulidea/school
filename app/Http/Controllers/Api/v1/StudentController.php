<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\APIController;
use Illuminate\Http\Request;

use App\Helpers\Qs;
use App\Helpers\Mk;
use App\Http\Requests\Student\StudentRecordCreate;
use App\Http\Requests\Student\StudentRecordUpdate;
use App\Repositories\LocationRepo;
use App\Repositories\MyClassRepo;
use App\Repositories\StudentRepo;
use App\Repositories\UserRepo;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StudentController extends APIController
{
    protected $loc, $my_class, $user, $student;

    public function __construct(LocationRepo $loc, MyClassRepo $my_class, UserRepo $user, StudentRepo $student)
    {
        $this->middleware('teamSA', ['only' => ['edit','update', 'reset_pass', 'create', 'store', 'graduated'] ]);
        $this->middleware('super_admin', ['only' => ['destroy',] ]);
 
         $this->loc = $loc;
         $this->my_class = $my_class;
         $this->user = $user;
         $this->student = $student;
    }


    public function listClass()
    {
        $data['class'] = $mc = $this->my_class->all();
       
        return $this->respond('success',$data);
    }


    public function listByClass($class_id)
    {
        $data['my_class'] = $mc = $this->my_class->getMC(['id' => $class_id])->first();
        $data['students'] = $this->student->findStudentsByClass($class_id);
        $data['sections'] = $this->my_class->getClassSections($class_id);


        return $this->respond('succes',
            $data
        );
    }
}
