<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\APIController;

use App\Helpers\Qs;
use App\Http\Requests\TimeTable\TSRequest;
use App\Http\Requests\TimeTable\TTRecordRequest;
use App\Http\Requests\TimeTable\TTRequest;
use App\Models\Setting;
use App\Repositories\ExamRepo;
use App\Repositories\MyClassRepo;
use App\Repositories\TimeTableRepo;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TimeTableController extends APIController
{
    protected $tt, $my_class, $exam, $year;

    public function __construct(TimeTableRepo $tt, MyClassRepo $mc, ExamRepo $exam)
    {
        $this->tt = $tt;
        $this->my_class = $mc;
        $this->exam = $exam;
        
    }

    public function index()
    {
        $this->year = Qs::getCurrentSession();
        
        $d['exams'] = $this->exam->getExam(['year' => $this->year]);
        $d['my_classes'] = $this->my_class->all();
        $d['schools'] = Qs::getSchool();
        return $this->respond('success',$d);
    }

    public function time_table_by_class($class_id)
    {   
        $d = $this->tt->getTTRByClassIDs($class_id);

        return $this->respond('success',$d);
    }

    public function manage($ttr_id)
    {
        $d['ttr_id'] = $ttr_id;
        $d['ttr'] = $ttr = $this->tt->findRecord($ttr_id);
        $d['time_slots'] = $this->tt->getTimeSlotByTTR($ttr_id);
        $d['ts_existing'] = $this->tt->getExistingTS($ttr_id);
        $d['subjects'] = $this->my_class->getSubject(['my_class_id' => $ttr->my_class_id])->get();
        $d['my_class'] = $this->my_class->find($ttr->my_class_id);
        $d['schools'] = Qs::getSchool();
        if($ttr->exam_id){
            $d['exam_id'] = $ttr->exam_id;
            $d['exam'] = $this->exam->find($ttr->exam_id);
        }

        $d['tts'] = $this->tt->getTimeTable(['ttr_id' => $ttr_id]);        
        return $this->respond('success',$d);
    }

    public function store(TTRequest $req)
    {
        $data = $req->all();
        $tms = $this->tt->findTimeSlot($req->ts_id);
        $d_date = $req->exam_date ?? $req->day;
        $data['timestamp_from'] = strtotime($d_date.' '.$tms->time_from);
        $data['timestamp_to'] = strtotime($d_date.' '.$tms->time_to);
        $data['school_id'] = QS::getHeaderSchoolId()[0];
        
        $this->tt->create($data);

        return $this->respondMessage(__('msg.store_ok'));
    }

    public function update(TTRequest $req, $tt_id)
    {
        dd("sita ram");
        $data = $req->all();
        $tms = $this->tt->findTimeSlot($req->ts_id);
        $d_date = $req->exam_date ?? $req->day;
        $data['timestamp_from'] = strtotime($d_date.' '.$tms->time_from);
        $data['timestamp_to'] = strtotime($d_date.' '.$tms->time_to);

        $this->tt->update($tt_id, $data);

        return $this->respondMessage(__('msg.update_ok'));

    }

    public function delete($tt_id)
    {
        $this->tt->delete($tt_id);
        return $this->respondMessage(__('msg.delete_ok'));
    }

    /*********** TIME SLOTS *************/

    public function store_time_slot(TSRequest $req)
    {
        $data = $req->all();
        $data['time_from'] = $tf =$req->hour_from.':'.$req->min_from.' '.$req->meridian_from;
        $data['time_to'] = $tt = $req->hour_to.':'.$req->min_to.' '.$req->meridian_to;
        $data['timestamp_from'] = strtotime($tf);
        $data['timestamp_to'] = strtotime($tt);
        $data['full'] = $tf.' - '.$tt;
        $data['school_id'] = QS::getHeaderSchoolId()[0];

        if($tf == $tt){
            return response()->json(['msg' => __('msg.invalid_time_slot'), 'ok' => FALSE]);
        }

        $this->tt->createTimeSlot($data);
        return $this->respondMessage(__('msg.store_ok'));
    }

    public function use_time_slot(Request $req, $ttr_id)
    {
        $this->validate($req, ['ttr_id' => 'required'], [], ['ttr_id' => 'TimeTable Record']);

        $d = [];  //  Empty Current Time Slot Before Adding New
        $this->tt->deleteTimeSlots(['ttr_id' => $ttr_id]);
        $time_slots = $this->tt->getTimeSlotByTTR($req->ttr_id)->toArray();

        foreach($time_slots as $ts){
            $ts['ttr_id'] = $ttr_id;
            $this->tt->createTimeSlot($ts);
        }

        // return redirect()->route('ttr.manage', $ttr_id)->with('flash_success', __('msg.update_ok'));
        return $this->respondMessage(__('msg.update_ok'));
    }

    public function edit_time_slot($ts_id)
    {
        $d['tms'] = $this->tt->findTimeSlot($ts_id);
        return $this->respond('success',$d);
    }

    public function update_time_slot(TSRequest $req, $ts_id)
    {
        $data = $req->all();
        $data['time_from'] = $tf =$req->hour_from.':'.$req->min_from.' '.$req->meridian_from;
        $data['time_to'] = $tt = $req->hour_to.':'.$req->min_to.' '.$req->meridian_to;
        $data['timestamp_from'] = strtotime($tf);
        $data['timestamp_to'] = strtotime($tt);
        $data['full'] = $tf.' - '.$tt;

        if($tf == $tt){
            return back()->with('flash_danger', __('msg.invalid_time_slot'));
        }

        $this->tt->updateTimeSlot($ts_id, $data);
        // return redirect()->route('ttr.manage', $req->ttr_id)->with('flash_success', __('msg.update_ok'));
        return $this->respondMessage(__('msg.update_ok'));
    }

    public function delete_time_slot($ts_id)
    {
        $this->tt->deleteTimeSlot($ts_id);
        return $this->respondMessage(__('msg.delete_ok'));
    }


    /*********** RECORDS *************/

    public function edit_record($ttr_id)
    {
        $d['ttr'] = $ttr = $this->tt->findRecord($ttr_id);
        $d['exams'] = $this->exam->getExam(['year' => $ttr->year]);
        $d['my_classes'] = $this->my_class->all();
        $d['schools'] = Qs::getSchool();

        return $this->respond('success',$d);
    }

    public function show_record($ttr_id)
    {
        $d_time = [];
        $d['ttr'] = $ttr = $this->tt->findRecord($ttr_id);
        $d['ttr_id'] = $ttr_id;
        $d['my_class'] = $this->my_class->find($ttr->my_class_id);

        $d['time_slots'] = $tms = $this->tt->getTimeSlotByTTR($ttr_id);
        $d['tts'] = $tts = $this->tt->getTimeTable(['ttr_id' => $ttr_id]);

        if($ttr->exam_id){
            $d['exam_id'] = $ttr->exam_id;
            $d['exam'] = $this->exam->find($ttr->exam_id);
            $d['days'] = $days = $tts->unique('exam_date')->pluck('exam_date');
            $d_date = 'exam_date';
        }

        else{
            $d['days'] = $days = $tts->unique('day')->pluck('day');
            $d_date = 'day';
        }

        foreach ($days as $day) {
            foreach ($tms as $tm) {
                $d_time[] = ['day' => $day, 'time' => $tm->full, 'subject' => $tts->where('ts_id', $tm->id)->where($d_date, $day)->first()->subject->name ?? NULL ];
            }
        }

        // foreach($d_time as $data){
        //     $d_time[] =  $data->where('day', $data->day);
        // }

        $d['d_time'] = collect($d_time);

        return $this->respond('success',$d);
    }
    public function print_record($ttr_id)
    {
        $d_time = [];
        $d['ttr'] = $ttr = $this->tt->findRecord($ttr_id);
        $d['ttr_id'] = $ttr_id;
        $d['my_class'] = $this->my_class->find($ttr->my_class_id);

        $d['time_slots'] = $tms = $this->tt->getTimeSlotByTTR($ttr_id);
        $d['tts'] = $tts = $this->tt->getTimeTable(['ttr_id' => $ttr_id]);

        if($ttr->exam_id){
            $d['exam_id'] = $ttr->exam_id;
            $d['exam'] = $this->exam->find($ttr->exam_id);
            $d['days'] = $days = $tts->unique('exam_date')->pluck('exam_date');
            $d_date = 'exam_date';
        }

        else{
            $d['days'] = $days = $tts->unique('day')->pluck('day');
            $d_date = 'day';
        }

        foreach ($days as $day) {
            foreach ($tms as $tm) {
                $d_time[] = ['day' => $day, 'time' => $tm->full, 'subject' => $tts->where('ts_id', $tm->id)->where($d_date, $day)->first()->subject->name ?? NULL ];
            }
        }

        $d['d_time'] = collect($d_time);
        $d['s'] = Setting::all()->flatMap(function($s){
            return [$s->type => $s->description];
        });

        return $this->respond('success',$d);
    }

    public function store_record(TTRecordRequest $req)
    {   
        $this->year = Qs::getCurrentSession();

        $data = $req->all();
        $data['year'] = $this->year;
        $this->tt->createRecord($data);

        return $this->respond('success',__('msg.store_ok'));
    }

    public function update_record(TTRecordRequest $req, $id)
    {
        dd("ram");
        $data = $req->all();
        $this->tt->updateRecord($id, $data);

        return $this->respond('success',__('msg.update_ok'));
    }

    public function delete_record($ttr_id)
    {
        $this->tt->deleteRecord($ttr_id);
        return $this->respond('success',__('msg.delete_ok'));
    }
}
