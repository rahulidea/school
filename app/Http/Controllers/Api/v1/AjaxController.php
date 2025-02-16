<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\APIController;
use App\Helpers\Qs;
use App\Repositories\LocationRepo;
use App\Repositories\MyClassRepo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AjaxController extends APIController
{
    protected $loc, $my_class;

    public function __construct(LocationRepo $loc, MyClassRepo $my_class)
    {
        $this->loc = $loc;
        $this->my_class = $my_class;
    }

    public function get_lga($state_id)
    {
//        $state_id = Qs::decodeHash($state_id);
//        return ['id' => Qs::hash($q->id), 'name' => $q->name];

        $lgas = $this->loc->getLGAs($state_id);
        $data = $lgas->map(function($q){
            return ['id' => $q->id, 'name' => $q->name];
        })->all();
        return $this->respond('success',$data);
    }

    public function get_class_sections(Request $req, $class_id)
    {
       //$req->school_id
       
        $sections = $this->my_class->getClassSections($class_id);
        $sections = $sections->map(function($q){
            return ['id' => $q->id, 'name' => $q->name];
        })->all();

        return $this->respond('success',$sections);
    }

    public function get_class_subjects(Request $req, $class_id)
    {
        //$req->school_id

        $sections = $this->my_class->getClassSections($class_id);
        $subjects = $this->my_class->findSubjectByClass($class_id);

        if(Qs::userIsTeacher()){
            $subjects = $this->my_class->findSubjectByTeacher(Auth::user()->id)->where('my_class_id', $class_id);
        }

        $d['sections'] = $sections->map(function($q){
            return ['id' => $q->id, 'name' => $q->name];
        })->all();
        $d['subjects'] = $subjects->map(function($q){
            return ['id' => $q->id, 'name' => $q->name];
        })->all();

        return $this->respond('success',$d);
    }

    public function citys($state_id){
        $data['citys'] = $this->loc->getLGAs($state_id);
        return $this->respond('Record Found', $data);
    }
}
