<?php

namespace App\Http\Controllers\Api\v1;

use Carbon\Carbon;
use App\Helpers\Qs;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Repositories\UserRepo;
use App\Http\Controllers\Controller;
use App\Repositories\OrganisationRepo;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\APIController;
use Illuminate\Support\Facades\Hash;

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

    public function store_org(Request $request)
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
        return $data;
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
            'name' => 'required|string|min:6|max:150',
            'password' => 'nullable|string|min:3|max:50',
            'email' => 'sometimes|nullable|email|max:100|unique:users',
            'phone' => 'required|nullable|string|min:6|max:20',
            'school_name' => 'required|string|max:255',
            // 'organisation_id' => 'exists:organisations,id',
        ]);

        if ($validator->fails()) {
            return $this->respondWithError($validator->errors());
        }

        $data = $request->all();

        if($data['default_org_school']){
            $org_request = new Request([
                'name'   => $data['school_name'],
                'subscription_id' => ($request->filled('subscription_id'))?$request->subscription_id:"3",
                'expiry_date' => $request->filled('expiry_date') ? $request->expiry_date : Carbon::now()->addYear()->toDateString(),
            ]);
            $org_data = $this->store_org($org_request);
        }
       
        $school_data["name"] = $data['school_name'];
        $school_data["organisation_id"] = $org_data->id;
        
        $school = $this->org->createSchool($school_data);

        if($data['default_org_school']){
            $user_request = new Request([
                'name'   => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'user_type' => "super_admin",
                'organisation_id' => $org_data->id,
                'phone' => $data['phone'],
                'school_id' => $school->id,
                'code' => strtoupper(Str::random(10)),
                'photo' => Qs::getDefaultUserImage(),
                'username' => strtoupper(Str::random(10))
            ]);
            
            $user_data = $this->user->create($user_request->all());
        }

        $response['user'] = $user_data;
        $response['school_data'] = $school_data;
        $response['org_data'] = $org_data;
        return $this->respond('succes',
            $response
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
