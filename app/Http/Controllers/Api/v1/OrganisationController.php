<?php

namespace App\Http\Controllers\Api\v1;

use App\Helpers\Qs;
use Illuminate\Http\Request;
use App\Repositories\UserRepo;
use App\Http\Controllers\Controller;
use App\Repositories\OrganisationRepo;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\APIController; 

class OrganisationController extends APIController
{
    protected $org, $user;

    public function __construct(OrganisationRepo $org, UserRepo $user)
    {
        // $this->middleware('teamSA', ['except' => ['destroy',] ]);
        // $this->middleware('super_admin', ['only' => ['destroy',] ]);

        $this->org = $org;
        $this->user = $user;
    }

    public function index($id=0){
        $data = $this->org->all($id);
        return $this->respond('succes',
            $data
        );
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'subscription_id' => 'required|integer|exists:subscriptions,id',
            'expiry_date' => 'required|date|after:today',
        ]);

        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }

        $data = $request->all();
        $data = $this->org->createOrg($data);
        return $this->respond('succes',
            $data
        );
    }

    public function update(Request $request, $id)
    {
       $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'subscription_id' => 'required|integer|exists:subscriptions,id',
            'expiry_date' => 'required|date|after:today',
        ]);

        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }

        $data = $request->only(['name', 'subscription_id', 'expiry_date']);
        $data = $this->org->updateOrg($id, $data);

        return $this->respond('Organisation Update successfully',
            $data
        );
    }

    public function deleteOrg($id){
        $data = $this->org->deleteOrg($id);
        return $this->respond('succes',
            $data
        );
    }

    public function school_index($id=0){
        $data = $this->org->allSchool($id);
        return $this->respond('succes',
            $data
        );
    }

    public function school_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'organisation_id' => 'required|integer|exists:organisations,id',
        ]);

        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }

        $data = $request->all();
        $data = $this->org->createSchool($data);
        return $this->respond('succes',
            $data
        );
    }

    public function school_update(Request $request, $id)
    {
       $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'organisation_id' => 'required|integer|exists:organisations,id',
        ]);

        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }

        $data = $request->only(['name', 'organisation_id']);
        $data = $this->org->updateSchool($id, $data);

        return $this->respond('School Update successfully',
            $data
        );
    }

    public function deleteSchool($id){
        $data = $this->org->deleteSchool($id);
        return $this->respond('succes',
            $data
        );
    }
}