<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\APIController;

use App\Helpers\Qs;
use App\Models\Mark;
use App\Repositories\MyClassRepo;
use App\Repositories\StudentRepo;
use Illuminate\Http\Request;

class PromotionController extends APIController
{
    protected $my_class, $student;

    public function __construct(MyClassRepo $my_class, StudentRepo $student)
    {
        $this->middleware('teamSA');

        $this->my_class = $my_class;
        $this->student = $student;
    }

    public function promotion($fc = NULL, $fs = NULL, $tc = NULL, $ts = NULL)
    {
        $d['old_year'] = $old_yr = Qs::getSetting('current_session');
        if($old_yr){
        $old_yr = explode('-', $old_yr);
        $d['new_year'] = ++$old_yr[0].'-'.++$old_yr[1];
        }
        $d['my_classes'] = $this->my_class->all();
        $d['sections'] = $this->my_class->getAllSections();
        $d['schools'] = Qs::getSchool();
        $d['selected'] = false;
        
        if($fc && $fs && $tc && $ts){
            $d['selected'] = true;
            $d['fc'] = $fc;
            $d['fs'] = $fs;
            $d['tc'] = $tc;
            $d['ts'] = $ts;
            $d['students'] = $sts = $this->student->getRecord(['my_class_id' => $fc, 'section_id' => $fs, 'session' => $d['old_year']])->get();

            if($sts->count() < 1){
                return $this->respondWithError("Student Record Not Found");
            }
        }
        $data = $d;
        return $this->respond('success',$data);
    }

    public function selector(Request $req)
    {
        $data=[$req->fc, $req->fs, $req->tc, $req->ts];   
        return $this->respond('success',$data);
        // return redirect()->route('students.promotion', [$req->fc, $req->fs, $req->tc, $req->ts]);
    }

    public function promote(Request $req, $fc, $fs, $tc, $ts)
    {
        $oy = Qs::getSetting('current_session'); $d = [];
        $old_yr = explode('-', $oy);
        $ny = ++$old_yr[0].'-'.++$old_yr[1];
        $students = $this->student->getRecord(['my_class_id' => $fc, 'section_id' => $fs, 'session' => $oy ])->get()->sortBy('user.name');

        if($students->count() < 1){
            return $this->respondWithError("Student Record Not Found");
        }

        foreach($students as $st){
            $p = 'p-'.$st->id;
            $p = $req->$p;
            if($p === 'P'){ // Promote
                $d['my_class_id'] = $tc;
                $d['section_id'] = $ts;
                $d['session'] = $ny;
            }
            if($p === 'D'){ // Don't Promote
                $d['my_class_id'] = $fc;
                $d['section_id'] = $fs;
                $d['session'] = $ny;
            }
            if($p === 'G'){ // Graduated
                $d['my_class_id'] = $fc;
                $d['section_id'] = $fs;
                $d['grad'] = 1;
                $d['grad_date'] = $oy;
            }
            
            $this->student->updateRecord($st->id, $d);

            // Insert New Promotion Data
            $promote['from_class'] = $fc;
            $promote['from_section'] = $fs;
            $promote['grad'] = ($p === 'G') ? 1 : 0;
            $promote['to_class'] = in_array($p, ['D', 'G']) ? $fc : $tc;
            $promote['to_section'] = in_array($p, ['D', 'G']) ? $fs : $ts;
            $promote['student_id'] = $st->user_id;
            $promote['from_session'] = $oy;
            $promote['to_session'] = $ny;
            $promote['status'] = $p;
            $promote['school_id'] = QS::getHeaderSchoolId()[0];

        //    dd($d, $promote);
            $this->student->createPromotion($promote);
        }
        
        return $this->respondMessage('succes');
    }

    public function manage()
    {
        $data['promotions'] = $this->student->getAllPromotions();
        $data['old_year'] = Qs::getCurrentSession();
        $data['new_year'] = Qs::getNextSession();
        $data['schools'] = Qs::getSchool();

        return $this->respond('success',$data);
    }

    public function reset($promotion_id)
    {
        $data = $this->reset_single($promotion_id);

        return $this->respond('success',$data);
    }

    public function reset_all()
    {
        $next_session = Qs::getNextSession();
        $where = ['from_session' => Qs::getCurrentSession(), 'to_session' => $next_session];
        $proms = $this->student->getPromotions($where);

        if ($proms->count()){
          foreach ($proms as $prom){
              $this->reset_single($prom->id);

              // Delete Marks if Already Inserted for New Session
              $this->delete_old_marks($prom->student_id, $next_session);
          }
        }

        return $this->respondMessage('All promotion delted succesfully');
    }

    protected function delete_old_marks($student_id, $year)
    {
        Mark::where(['student_id' => $student_id, 'year' => $year])->delete();
    }

    protected function reset_single($promotion_id)
    {
        $prom = $this->student->findPromotion($promotion_id);

        $data['my_class_id'] = $prom->from_class;
        $data['section_id'] = $prom->from_section;
        $data['session'] = $prom->from_session;
        $data['grad'] = 0;
        $data['grad_date'] = null;

        $this->student->update(['user_id' => $prom->student_id], $data);

        return $this->student->deletePromotion($promotion_id);
    }
}
