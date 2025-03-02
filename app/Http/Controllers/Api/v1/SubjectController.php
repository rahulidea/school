<?php

namespace App\Http\Controllers\Api\v1;

use App\User;
use App\Helpers\Qs;
use App\Models\Subject;
use Illuminate\Http\Request;
use App\Repositories\UserRepo;
use App\Repositories\MyClassRepo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\APIController;
use App\Http\Requests\Subject\SubjectCreate;
use App\Http\Requests\Subject\SubjectUpdate;
use App\Models\Section;
use Exception;

class SubjectController extends APIController
{
    protected $my_class, $user;

    public function __construct(MyClassRepo $my_class, UserRepo $user)
    {
        $this->middleware('teamSA', ['only' => ['edit','update', 'reset_pass', 'create', 'store', 'graduated'] ]);
        $this->middleware('super_admin', ['only' => ['destroy',] ]);

        $this->my_class = $my_class;
        $this->user = $user;
    }

    public function show()
    {
        $d['my_classes'] = $this->my_class->all();
        $d['teachers'] = $this->user->getUserByType('teacher');
        $d['schools'] = Qs::getSchool();
        
        return $this->respond('success',$d);
    }

    public function allSubjects($class_id)
    {
        $d = $this->my_class->allSubjectByClass($class_id);        
        return $this->respond('success',$d);
    }

    public function store(SubjectCreate $req)
    {
        $data = $req->all();
        $data['school_id'] = QS::getHeaderSchoolId()[0];
        $d = $this->my_class->createSubject($data);

        return $this->respond('succes',
            $d
        );
    }

    public function edit($id)
    {
        $sub = $this->my_class->findSubject($id);
        if(!is_null($sub)){
            $d['subject'] = $sub;
        }
        $d['my_classes'] = $this->my_class->all();
        $d['teachers'] = $this->user->getUserByType('teacher');

        return $this->respond('succes',
            $d
        );
    }

    public function update(SubjectUpdate $req, $id)
    {
        $data = $req->all();
        $this->my_class->updateSubject($id, $data);

        return $this->respond('succes',
        __('msg.update_ok')
        );
    }

    public function destroy($id)
    {
        $this->my_class->deleteSubject($id);
        return $this->respond('succes',
        __('msg.del_ok')
        );
    }

    public function getAssignedDetail(){
        $d['class'] = $this->my_class->getClassSection()->select('id','name')->get();
        $d['subject'] = Subject::where('school_id', Qs::getSchoolId())->select('id','name','slug','my_class_id')->get();
        $d['teacher'] = User::where('school_id', Qs::getSchoolId())->where('user_type','teacher')->select('id','name')->get();
        $d['section'] = Section::where('school_id', Qs::getSchoolId())->select('id','name')->get();
        $d['currently_assigned'] = DB::table('class_teacher')->where('school_id', Qs::getSchoolId())->get();
        return $this->respond('success',$d);
    }

    public function getAssignedTeacher()
    {
        //$d['class'] = DB::table('class_teacher')::where('school_id', Qs::getSchoolId()->select('section_id', 'subject_id', 'school_id'));
        //$d['subject'] = Subject::where('school_id', Qs::getSchoolId())->select('id','name','slug')->get();
        
        //return $this->respond('success',$d);
    }
        
    public function setAssignedTeacher(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'section_id' => 'required|integer|min:1',
            'class_id' => 'required|integer|min:1',
            'subject_id' => 'required|integer|min:1',
            'teacher_id' => 'required|integer|min:1',
        ]);
        
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors()->all());
        }
        try{
            $data = $request->only(['section_id', 'class_id', 'subject_id']);

                $existingRecord = DB::table('class_teacher')
                    ->where($data)
                    ->first();

                if ($existingRecord) {
                    DB::table('class_teacher')
                        ->where($data)
                        ->update($request->except(['section_id', 'class_id', 'subject_id']));
                } else {
                    DB::table('class_teacher')->insert($request->all());
                }
        }catch(Exception $e){
            return $this->respondWithError($e);
        }

        return $this->respond('Teacher Assigned Succesfully',[]);
    }
}
