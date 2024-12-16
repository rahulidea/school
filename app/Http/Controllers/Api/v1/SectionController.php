<?php

namespace App\Http\Controllers\Api\v1;

use App\Helpers\Qs;
use App\Http\Controllers\Api\APIController;
use App\Http\Requests\Section\SectionCreate;
use App\Http\Requests\Section\SectionUpdate;
use App\Repositories\MyClassRepo;
use App\Repositories\UserRepo;

class SectionController extends APIController
{
    protected $my_class, $user;

    public function __construct(MyClassRepo $my_class, UserRepo $user)
    {
        $this->middleware('teamSA', ['except' => ['destroy',] ]);
        $this->middleware('super_admin', ['only' => ['destroy',] ]);

        $this->my_class = $my_class;
        $this->user = $user;
    }

    public function index()
    {
        $d['my_classes'] = $this->my_class->all();
        
        $d['teachers'] = $this->user->getUserByType('teacher');

        return $this->respond('success',$d);
    }

    public function getClassSections($class_id){
        $d = $this->my_class->getClassSections($class_id);

        return $this->respond('success',$d);
    }

    public function store(SectionCreate $req)
    {
        $data = $req->all();
        $this->my_class->createSection($data);

        return $this->respond(__('msg.store_ok'),$d);
    }

    public function edit($id)
    {
        $d['s'] = $s = $this->my_class->findSection($id);
        $d['teachers'] = $this->user->getUserByType('teacher');

        return $this->respond(__('msg.store_ok'),$d);

        // return is_null($s) ? Qs::goWithDanger('sections.index') :view('pages.support_team.sections.edit', $d);
    }

    public function update(SectionUpdate $req, $id)
    {
        $data = $req->only(['name', 'teacher_id']);
        $this->my_class->updateSection($id, $data);

        return Qs::jsonUpdateOk();
    }

    public function destroy($id)
    {
        if($this->my_class->isActiveSection($id)){
            
            return $this->respondWithError('Every class must have a default section, You Cannot Delete It');
        }

        $this->my_class->deleteSection($id);
        return $this->respondMessage(__('msg.del_ok'));
    }

}
