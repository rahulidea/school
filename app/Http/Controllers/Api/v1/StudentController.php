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
        // $data['my_class'] = $mc = $this->my_class->getMC(['id' => $class_id])->first();
        $data['students'] = $this->student->findStudentsByClass($class_id);
        // $data['sections'] = $this->my_class->getClassSections($class_id);


        return $this->respond('succes',
            $data
        );
    }

    public function studentDetails($sr_id)
    {
        if(!$sr_id){return Qs::goWithDanger();}

        $data = $this->student->getRecordByUserIDs([$sr_id])->first();

        /* Prevent Other Students/Parents from viewing Profile of others */
        if(Auth::user()->id != $data->user_id && !Qs::userIsTeamSAT() && !Qs::userIsMyChild($data->user_id, Auth::user()->id)){
            return $this->throwValidation("Record Not Found",422);
        }

        return $this->respond('Record Found',$data);
    }


    public function edit($sr_id)
    {
        if(!$sr_id){return Qs::goWithDanger();}

        $data['sr'] = $this->student->getRecord(['id' => $sr_id])->first();
        $data['my_classes'] = $this->my_class->all();
        $data['parents'] = $this->user->getUserByType('parent');
        $data['dorms'] = $this->student->getAllDorms();
        $data['states'] = $this->loc->getStates();
        $data['nationals'] = $this->loc->getAllNationals();
      
        return $this->respond('Record Found',$data);
    }


    public function update(StudentRecordUpdate $req, $sr_id)
    {
        if(!$sr_id){return Qs::goWithDanger();}

        $sr = $this->student->getRecord(['id' => $sr_id])->first();
        $d =  $req->only(Qs::getUserRecord());
        $d['name'] = ucwords($req->name);

        if($req->hasFile('photo')) {
            $photo = $req->file('photo');
            $f = Qs::getFileMetaData($photo);
            $f['name'] = 'photo.' . $f['ext'];
            $f['path'] = $photo->storeAs(Qs::getUploadPath('student').$sr->user->code, $f['name']);
            $d['photo'] = asset('storage/' . $f['path']);
        }

        $this->user->update($sr->user->id, $d); // Update User Details

        $srec = $req->only(Qs::getStudentData());
       
        $this->student->updateRecord($sr_id, $srec); // Update St Rec
       
        /*** If Class/Section is Changed in Same Year, Delete Marks/ExamRecord of Previous Class/Section ****/
        Mk::deleteOldRecord($sr->user->id, $srec['my_class_id']);

        return Qs::jsonUpdateOk();
    }
}
