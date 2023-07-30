<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Rest;
use App\Models\Attendance;
use App\Http\Requests\RegisterRequest;
use Carbon\Carbon;

class RestController extends Controller
{   
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function startRest()
    {

        $attendance = Attendance::where('user_id', Auth::user()->id)
                        ->whereNotNull('start_time')
                        ->whereNull('end_time')
                        ->latest()
                        ->first();

        if ($attendance) {
            $rest = new Rest();
            $rest->attendance_id = $attendance->id;
            $rest->start_time = now();
            $rest->save();

            Session::put('restStarted', true);
            $restStarted = Session::get('restStarted');

            Session::put('attendanceStarted', true);
            $attendanceStarted = Session::get('attendanceStarted');
        }

        return redirect('/')->with(['attendanceStarted' => $attendanceStarted, 'restStarted' => $restStarted]);
    }

    public function endRest()
    {
        $attendance = Attendance::where('user_id', Auth::user()->id)
                ->whereNotNull('start_time')
                ->whereNull('end_time')
                ->latest()
                ->first();

        if ($attendance) {
        $rest = Rest::whereHas('attendance', function ($query) use ($attendance) {
                        $query->where('attendance_id', $attendance->id);
                    })->latest()->first();
        
            if ($rest) {
                $rest->end_time = now();
                $rest->save();

                Session::put('restStarted', false);
                $restStarted = Session::get('restStarted');

                Session::put('attendanceStarted', true);
                $attendanceStarted = Session::get('attendanceStarted');
            }
        }
        
        return redirect('/')->with(['attendanceStarted' => $attendanceStarted, 'restStarted' => $restStarted]);
    }
    
}
