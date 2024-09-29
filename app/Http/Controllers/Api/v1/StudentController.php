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
use App\Models\BloodGroup;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Helpers\FireBasePushNotification;
use App\Models\State;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Http;

class StudentController extends APIController
{
    protected $loc, $my_class, $user, $student, $push_notification;

    public function __construct(LocationRepo $loc, MyClassRepo $my_class, UserRepo $user, StudentRepo $student, FireBasePushNotification $push_notification)
    {
        $this->middleware('teamSA', ['only' => ['edit','update', 'reset_pass', 'create', 'store', 'graduated'] ]);
        $this->middleware('super_admin', ['only' => ['destroy',] ]);
 
         $this->loc = $loc;
         $this->my_class = $my_class;
         $this->user = $user;
         $this->student = $student;

         $this->push_notification = $push_notification;
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
        $data['sections'] = $this->my_class->getClassSections($class_id);


        return $this->respond('succes',
            $data
        );
    }

    public function listBySection($section_id)
    {
        $data['students'] = $this->student->findStudentsBySection($section_id);


        return $this->respond('succes',
            $data
        );
    }

    public function studentDetails($sr_id)
    {
        if(!$sr_id){return $this->respondWithError("Student ID required");}

        $data = $this->student->getRecordByUserIDs([$sr_id])->first();

        if(!$data){
            return $this->respondWithError("User Not Found");
        }
        /* Prevent Other Students/Parents from viewing Profile of others */
        if(Auth::user()->id != $data->user_id && !Qs::userIsTeamSAT() && !Qs::userIsMyChild($data->user_id, Auth::user()->id)){
            return $this->throwValidation("Record Not Found",422);
        }

        return $this->respond('Record Found',$data);
    }

    public function edit($sr_id, $is_grad=0)
    {   
        return State::with(['cities:id,name,state_id'])->get();        
        if($sr_id!=0){
            $sr_id = Qs::decodeHash($sr_id);
            if(!$sr_id){return $this->respondWithError("Student ID required");}

            if($is_grad){
                $data['sr'] = $this->student->getGradRecord(['id' => $sr_id])->first();
            }else{
                $data['sr'] = $this->student->getRecord(['id' => $sr_id])->first();
            }
            
            if(!$data['sr']){
                return $this->respondError("Student Record Not Found");
            }
        }

        $data['my_classes'] = $this->my_class->all();
        $data['parents'] = $this->user->getUserByType('parent');
        $data['dorms'] = $this->student->getAllDorms();
        $data['states'] = $this->loc->getStates();
        $data['nationals'] = $this->loc->getAllNationals();
        $data['hashed_id'] = Qs::hash($sr_id);
        $data['blood_groups']=BloodGroup::all();
        return $this->respond('Record Found',$data);
        
    }

    public function update(StudentRecordUpdate $req, $sr_id, $is_grad=0)
    {   
        $sr_id = Qs::decodeHash($sr_id);
        if(!$sr_id){return $this->respondWithError("Student ID required");}

        if($is_grad){
            $sr = $this->student->getGradRecord(['id' => $sr_id])->first();
        }else{
            $sr = $this->student->getRecord(['id' => $sr_id])->first();
        }
        if(!$sr){
            return $this->respondError("Student Record Not Found");
        }

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
       
        $data = $this->student->updateRecord($sr_id, $srec); // Update St Rec
       
        /*** If Class/Section is Changed in Same Year, Delete Marks/ExamRecord of Previous Class/Section ****/
        Mk::deleteOldRecord($sr->user->id, $srec['my_class_id']);

        return $this->respond('Record Updated',$data);
    }

    public function store(StudentRecordCreate $req)
    {
       $data =  $req->only(Qs::getUserRecord());
       $sr =  $req->only(Qs::getStudentData());
        $ct = $this->my_class->findTypeByClass($req->my_class_id)->code;
       /* $ct = ($ct == 'J') ? 'JSS' : $ct;
        $ct = ($ct == 'S') ? 'SS' : $ct;*/

        $data['user_type'] = 'student';
        $data['name'] = ucwords($req->name);
        $data['code'] = strtoupper(Str::random(10));
        $data['password'] = Hash::make('student');
        $data['photo'] = Qs::getDefaultUserImage();
        $adm_no = $req->adm_no;
        $data['username'] = strtoupper(Qs::getAppCode().'/'.$ct.'/'.$sr['year_admitted'].'/'.($adm_no ?: mt_rand(1000, 99999)));

        if($req->hasFile('photo')) {
            $photo = $req->file('photo');
            $f = Qs::getFileMetaData($photo);
            $f['name'] = 'photo.' . $f['ext'];
            $f['path'] = $photo->storeAs(Qs::getUploadPath('student').$data['code'], $f['name']);
            $data['photo'] = asset('storage/' . $f['path']);
        }

        $user = $this->user->create($data); // Create User

        $sr['adm_no'] = $data['username'];
        $sr['user_id'] = $user->id;
        $sr['session'] = Qs::getSetting('current_session');
        $this->student->createRecord($sr); // Create Student
    
        return $this->respond('Student Record Added', $data);
    }
    
    public function show($sr_id, $is_grad=0)
    {

        $sr_id = Qs::decodeHash($sr_id);
     
        if(!$sr_id){return $this->respondWithError("Student ID required");}

        if($is_grad){
            $data['sr'] = $this->student->getGradRecord(['id' => $sr_id])->first();
        }else{
            $data['sr'] = $this->student->getRecord(['id' => $sr_id])->first();
        }

        if(!$data['sr']){
            return $this->respondError("Student Record Not Found");
        }

        /* Prevent Other Students/Parents from viewing Profile of others */
        if(Auth::user()->id != $data['sr']->user_id && !Qs::userIsTeamSAT() && !Qs::userIsMyChild($data['sr']->user_id, Auth::user()->id)){
            return redirect(route('dashboard'))->with('pop_error', __('msg.denied'));
        }

        return $this->respond('Record Found', $data);
    }

    public function reset_pass($st_id, Request $req)
    {
        $st_id = Qs::decodeHash($st_id);
        $data['password'] = Hash::make($req->password);
        $this->user->update($req->id, $data);
        return $this->respond('Password Updated', []);
    }

    public function destroy($st_id, $is_grad=0)
    {   
        $st_id = Qs::decodeHash($st_id);     
        $sr = $this->student->getRecord(['user_id' => $st_id])->first();
        if($is_grad){
            $sr = $this->student->getGradRecord(['user_id' => $st_id])->first();
        }else{
            $sr = $this->student->getRecord(['user_id' => $st_id])->first();
        }
        if(!$sr){
            return $this->respondError("Student Record Not Found");
        }
        $path = Qs::getUploadPath('student').$sr->user->code;
        Storage::exists($path) ? Storage::deleteDirectory($path) : false;
        $data = $this->user->delete($sr->user->id);

        return $this->respond('User Deleted', $data);
    }

    public function graduated()
    {
        $data['my_classes'] = $this->my_class->all();
        $data['students'] = $this->student->allGradStudents();

        return $this->respond('User Record', $data);
    }

    public function show_graduate($sr_id)
    {

        $sr_id = Qs::decodeHash($sr_id);
     
        if(!$sr_id){return $this->respondWithError("Student ID required");}

        $data['sr'] = $this->student->getGradRecord(['id' => $sr_id])->first();

        /* Prevent Other Students/Parents from viewing Profile of others */
        if(Auth::user()->id != $data['sr']->user_id && !Qs::userIsTeamSAT() && !Qs::userIsMyChild($data['sr']->user_id, Auth::user()->id)){
            return redirect(route('dashboard'))->with('pop_error', __('msg.denied'));
        }

        return $this->respond('Record Found', $data);
    }

    public function citys($state_id){
        $data['citys'] = $this->loc->getLGAs($state_id);
        return $this->respond('Record Found', $data);
    }

    public function classSections($class_id){
        $data['sections'] = $this->my_class->getClassSections($class_id);
        return $this->respond('Record Found', $data);
    }

    public function sendPusgNotification(Request $request){
        $request->validate([
            'fc_token' => 'required',
            'title' => 'required|string',
            'body' => 'required|string',
        ]);

        // $user = \App\Models\User::find($request->user_id);
        // $fcm = $user->fcm_token;

        $response=  $this->push_notification->toDevice($request->fc_token,$request->body,$request->title);

        if ($response) {
            // return response()->json([
            //     'message' => 'Curl Error: ' . $err
            // ], 500);
            // return $this->respondError('Curl Error: ' . $err);
        } else {
            // return response()->json([
            //     'message' => 'Notification has been sent',
            //     'response' => json_decode($response, true)
            // ]);

            // return $this->respond('Notification has been sent', $response);
        }

        return $this->respond('Notification has been sent', $response);
    }
}
