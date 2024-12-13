<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\APIController;
use App\Helpers\Qs;
use App\Http\Requests\Subject\SubjectCreate;
use App\Http\Requests\Subject\SubjectUpdate;
use App\Repositories\MyClassRepo;
use App\Repositories\UserRepo;

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
        // $d['subjects'] = $this->my_class->getAllSubjects();
        
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
        $data['school_id'] = QS::getSchoolId()[0];
        $d = $this->my_class->createSubject($data);

        return $this->respond('success',$d);
    }

    public function edit($id)
    {
        $sub = $this->my_class->findSubject($id);
        if(!is_null($sub)){
            $d['subject'] = $sub;
        }
        $d['my_classes'] = $this->my_class->all();
        $d['teachers'] = $this->user->getUserByType('teacher');

        return $this->respond('success',$d);
    }

    public function update(SubjectUpdate $req, $id)
    {
        $data = $req->all();
        $this->my_class->updateSubject($id, $data);

        return $this->respondMessage(__('msg.update_ok'));
    }

    public function destroy($id)
    {
        $this->my_class->deleteSubject($id);
        return $this->respondMessage(__('msg.del_ok'));
    }
}
