<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\StudentRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\Holiday\HolidayRequest;
use App\Http\Controllers\Api\APIController;

class AttendanceController extends APIController
{
    public function index(Request $request)
    {
        $class_id = (isset($request->class_id) && $request->class_id>0)?$request->class_id:0;
        $section_id = (isset($request->section_id) && $request->section_id>0)?$request->section_id:0;

        $students = StudentRecord::with('user:id,name,email');  

        if($class_id){
            $students->where('my_class_id', $class_id);
        }

        if($section_id){
            $students->where('section_id', $section_id);
        }

        $students = $students->get()->pluck('user')->toArray();
        $holidays = Holiday::pluck('date')->toArray();
        $weekends = [Carbon::SUNDAY];

        return response()->json([
            'message' => "Attendance record.",
            'data' => [
                'students' => $students,
                'holidays' => $holidays,
                'weekends' => $weekends
            ]
        ], 200);
    }

    public function store(Request $request)
    {
        $attendanceData = [];

        foreach ($request->attendances as $attendance) {
            $attendanceData[] = [
                'student_id' => $attendance['student_id'],
                'class_id' => $attendance['class_id'],
                'section_id' => $attendance['section_id'],
                'attendee' => $attendance['attendee'],
                'date' => $attendance['date'],
                'status' => $attendance['status'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

     //   return ($attendanceData);
        Attendance::insert($attendanceData);

        return response()->json([
            'message' => "Attendance recorded successfully.",
        ], 200);
    }

    public function storeOrUpdateHoliday(HolidayRequest $request){
        try {
            $holiday = Holiday::where('date', $request->input('date'))->first();
    
            if ($holiday) {
                $holiday->update([
                    'name' => $request->input('name')
                ]);
                $message = 'Holiday updated successfully!';
            } else {
                Holiday::create([
                    'date' => $request->input('date'),
                    'name' => $request->input('name')
                ]);
                $message = 'Holiday created successfully!';
            }    
            return response()->json([
                'message' => $message,
            ], 200);
    
        } catch (\Exception $e) {
            // Return the exact exception message
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
        
    }
}
