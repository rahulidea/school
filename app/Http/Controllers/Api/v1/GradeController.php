<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\APIController;

use App\Helpers\Qs;
use App\Helpers\MK;
use App\Http\Requests\Grade\GradeCreate;
use App\Http\Requests\Grade\GradeUpdate;
use App\Repositories\ExamRepo;
use App\Http\Controllers\Controller;
use App\Repositories\MyClassRepo;
use Illuminate\Http\Request;

class GradeController extends APIController
{
    protected $exam, $my_class;

    public function __construct(ExamRepo $exam, MyClassRepo $my_class)
    {
        $this->exam = $exam;
        $this->my_class = $my_class;

        $this->middleware('teamSA', ['except' => ['destroy',] ]);
        $this->middleware('super_admin', ['only' => ['destroy',] ]);
    }

    public function index(Request $req)
    {
         $d['grades'] = $this->exam->allGrades($req->school_id);
         $d['class_types'] = $this->my_class->getTypes();
         $d['schools'] = Qs::getSchool();

        return $this->respond('success',$d);
    }

    public function store(GradeCreate $req)
    {
        $data = $req->all();

        try {
            $this->exam->createGrade($data);
            return $this->respondMessage(__('msg.store_ok'));
        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            $error_message = $e->errorInfo[2];
            if($error_code == 1062){
            
                // Return a error response
                return $this->respondInternalError('Duplicate entry');
            }else{
                return $this->respondInternalError($error_message);
            }
        }
        catch (Throwable $e) {
            DB::rollBack();

            // Return a error response
            return $this->respondInternalError('There was an error creating the user.');
        }
    }

    public function edit($id)
    {
        $d['class_types'] = $this->my_class->getTypes();
        $d['schools'] = Qs::getSchool();
        $d['remarks'] = MK::getRemarks();
        $d['gr'] = $this->exam->findGrade($id);
        return $this->respond('success', $d);
    }

    public function update(GradeUpdate $req, $id)
    {
        $data = $req->all();

        $this->exam->updateGrade($id, $data);
        return $this->respondMessage(__('msg.update_ok'));
    }

    public function destroy($id)
    {
        $this->exam->deleteGrade($id);
        return $this->respondMessage(__('msg.del_ok'));
    }
}
