<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\APIController;
use App\Helpers\Qs;
use App\Http\Requests\MyClass\ClassCreate;
use App\Http\Requests\MyClass\ClassUpdate;
use App\Repositories\MyClassRepo;
use App\Repositories\UserRepo;
use Throwable;
use Illuminate\Database\QueryException;

class MyClassController extends APIController
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
        $school_id = request()->header('school_id');
        
        $mc = $this->my_class->all();

        $d['my_classes'] = $mc;
        $d['class_types'] = $this->my_class->getTypes();
        $d['schools'] = Qs::getSchool();

        return $this->respond('success',$d);
    }

    public function store(ClassCreate $req)
    {
        $data = $req->all();
        $data['school_id'] = $school_id = QS::getHeaderSchoolId()[0];

        try{
            $mc = $this->my_class->create($data);

            // Create Default Section
            $s =['my_class_id' => $mc->id,
                'name' => 'A',
                'school_id' => $school_id,
                'active' => 1,
                'teacher_id' => NULL,
            ];

            $this->my_class->createSection($s);

            return $this->respond(__('msg.store_ok'),$mc);
        }  catch (Throwable $e) {
            $error_code = $e->errorInfo[1];
            $this->setStatusCode(500);
            if($error_code == 1062){
                return $this->respondWithError('Duplicate entry : '.$this->my_class->getTypesName($data['class_type_id']).'-'.$data['name']);
            }else{
                return $this->respondWithError($e->getMessage());
            }
        }
        // return Qs::jsonStoreOk();
    }

    public function edit($id)
    {
        $d['my_class'] = $c = $this->my_class->find($id);
        $d['schools'] = Qs::getSchool();
        
        return $this->respond('success',$d);
    }

    public function update(ClassUpdate $req, $id)
    {
        $data = $req->only(['name']);
        $this->my_class->update($id, $data);

        return $this->respondMessage(__('msg.update_ok'));
    }

    public function destroy($id)
    {
        $this->my_class->delete($id);
        return $this->respondMessage(__('msg.del_ok'));
    }

}
