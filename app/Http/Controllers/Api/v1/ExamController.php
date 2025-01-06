<?php


namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\APIController;

use App\Helpers\Qs;
use App\Http\Requests\Exam\ExamCreate;
use App\Http\Requests\Exam\ExamUpdate;
use App\Repositories\ExamRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class ExamController extends APIController
{
    protected $exam;
    public function __construct(ExamRepo $exam)
    {
        $this->middleware('teamSA', ['except' => ['destroy',] ]);
        $this->middleware('super_admin', ['only' => ['destroy',] ]);

        $this->exam = $exam;
    }

    public function index()
    {
        $d = $this->exam->all();
        return $this->respond('success',$d);
    }

    // public function store(ExamCreate $req)
    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'name' => 'required|string',
            'term' => 'required|numeric',
            'school_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }

        $data = $req->only(['name', 'school_id', 'term']);
        
        $data['year'] = Qs::getSetting('current_session');
        try {
            
            $this->exam->create($data);
            return $this->respondMessage(__('msg.store_ok'));
        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            $error_message = $e->errorInfo[2];
            if($error_code == 1062){
                
                // Return a error response
                return $this->respondInternalError('Duplicate entry');
            }
        }
        catch (Throwable $e) {
            DB::rollBack();
    
            // Return a error response
            return $this->respondInternalError('There was an error creating the user.');
        }

        
    }

    public function edit(Request $req, $id)
    {
        $school_id = $req->all()['school_id'];

        $d = $this->exam->find($id);

        return $this->respond('success', $d);
    }

    public function update(ExamUpdate $req, $id)
    {
        $data = $req->only(['name', 'term']);

        $this->exam->update($id, $data);
        return $this->respondMessage(__('msg.update_ok'));
    }

    public function destroy($id)
    {
        $this->exam->delete($id);
        return $this->respondMessage(__('msg.del_ok'));
    }
}
