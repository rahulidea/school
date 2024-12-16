<?php

namespace App\Http\Controllers\Api\v1;

use App\Helpers\Qs;
use App\Http\Controllers\Api\APIController;
use App\Http\Requests\Dorm\DormCreate;
use App\Http\Requests\Dorm\DormUpdate;
use App\Repositories\DormRepo;

class DormController extends APIController
{
    protected  $dorm;

    public function __construct(DormRepo $dorm)
    {
        $this->middleware('teamSA', ['except' => ['destroy',] ]);
        $this->middleware('super_admin', ['only' => ['destroy',] ]);

        $this->dorm = $dorm;
    }

    public function index()
    {
        $d = $this->dorm->getAll();
        return $this->respond('success',$d);
    }

    public function store(DormCreate $req)
    {
        $data = $req->only(['name', 'description']);
        $data['school_id'] = QS::getSchoolId();
        
        $this->dorm->create($data);

        return $this->respond(__('msg.store_ok'),$data);
    }

    public function edit($id)
    {
        $d['dorm'] = $dorm = $this->dorm->find($id);

        return !is_null($dorm) ?  $this->respondWithError("Record not found") 
        : $this->respond(__('msg.store_ok'),$d);
    }

    public function update(DormUpdate $req, $id)
    {
        $data = $req->only(['name', 'description']);
        $this->dorm->update($id, $data);

        return $this->respondMessage(__('msg.update_ok'));
    }

    public function destroy($id)
    {
        $this->dorm->find($id)->delete();
        return $this->respondMessage(__('msg.del_ok'));
    }
}
