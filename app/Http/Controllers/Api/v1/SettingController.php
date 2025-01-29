<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\APIController;

use App\Helpers\Qs;
use App\Models\Setting;
use App\Repositories\MyClassRepo;
use App\Repositories\SettingRepo;
use App\Http\Requests\SettingUpdate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class SettingController extends APIController
{
    protected $setting, $my_class;

    public function __construct(SettingRepo $setting, MyClassRepo $my_class)
    {
        $this->setting = $setting;
        $this->my_class = $my_class;
    }

    public function index(Request $req)
    {         
        if($req->school_id){
            $s = Setting::where('school_id', $req->school_id)->get();
            $selected_school = $req->school_id;
        }else{
            $s = $this->setting->all();
            $school_id = Auth::user()->school_id;
        }
        
        //  $d['class_types'] = $this->my_class->getTypes();
         $d['settings'] = $s->flatMap(function($s){
            return [$s->type => $s->description];
        });
        $d['schools'] = Qs::getSchool();
        $d['school_id'] = $req->school_id;
        
        return $this->respond('success',$d);
        // return view('pages.super_admin.settings', $d);
    }


    public function edit(Request $req)
    {         
        $session = array();
        if($req->school_id){
            $s = Setting::where('school_id', $req->school_id)->get();
            $selected_school = $req->school_id;

            for($y=date('Y', strtotime('- 3 years')); $y<=date('Y', strtotime('+ 1 years')); $y++){
                $session[] = ($y-=1).'-'.($y+=1);  
            }

        }else{
            $s = $this->setting->all();
            $school_id = Auth::user()->school_id;
        }
        
        //  $d['class_types'] = $this->my_class->getTypes();
         $d['settings'] = $s->flatMap(function($s){
            return [$s->type => $s->description];
        });
        $d['schools'] = Qs::getSchool();
        $d['school_id'] = $req->school_id;
        $d["sessions"] = $session;

        return $this->respond('success',$d);
        // return view('pages.super_admin.settings', $d);
    }

    public function update(SettingUpdate $req)
    {
        $sets = $req->except('_token', '_method', 'logo');
        $sets['lock_exam'] = $sets['lock_exam'] == 1 ? 1 : 0;
        $keys = array_keys($sets);
        $values = array_values($sets);
        for($i=0; $i<count($sets); $i++){
            $this->setting->update($keys[$i], $values[$i], $req->school_id);
        }

        if($req->hasFile('logo')) {
            $logo = $req->file('logo');
            $f = Qs::getFileMetaData($logo);
            $f['name'] = 'logo.' . $f['ext'];
            $f['path'] = $logo->storeAs(Qs::getPublicUploadPath(), $f['name']);
            $logo_path = asset('storage/' . $f['path']);
            $this->setting->update('logo', $logo_path, $req->school_id);
        }

        // return back()->with('flash_success', __('msg.update_ok'));
        return $this->respondMessage(__('msg.update_ok'));
    }
}
