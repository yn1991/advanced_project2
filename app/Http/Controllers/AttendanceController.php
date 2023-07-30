<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use App\Http\Requests\RegisterRequest;
use Carbon\Carbon;
use Carbon\CarbonInterval;

class AttendanceController extends Controller
{    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getindex()
    {
        if (Session::has('attendanceStarted')) {
            $attendanceStarted = Session::get('attendanceStarted');
        } else {
            Session::put('attendanceStarted', false);
            $attendanceStarted = Session::get('attendanceStarted', false);
        }

        if (Session::has('restStarted')) {
            $restStarted = Session::get('restStarted');
        } else {
            Session::put('restStarted', false);
            $restStarted = Session::get('restStarted', false);
        }

        return view('index', ['attendanceStarted' => $attendanceStarted, 'restStarted' => $restStarted]);
    }

    public function startAttendance(Request $request)
    {
        $inputs = $request->all();

        if ($request->has('start_attendance')) {
            $attendance = new Attendance();
            $attendance->user_id = Auth::user()->id;
            $attendance->date = Carbon::now()->toDateString();;
            $attendance->start_time = now();
            $attendance->save();

            Session::put('attendanceStarted', true);
            $attendanceStarted = Session::get('attendanceStarted');
            $restStarted = Session::get('restStarted');
        } 

        return redirect('/')->with(['attendanceStarted' => $attendanceStarted, 'restStarted' => $restStarted]);

    }

    public function endAttendance(Request $request)
    {
        $attendance = Attendance::where('user_id', Auth::user()->id)
                    ->whereNull('end_time')
                    ->latest()
                    ->first();
        
        if ($attendance) {
            $attendance->end_time = now();
            $attendance->save();

            Session::put('attendanceStarted', false);
            $attendanceStarted = Session::get('attendanceStarted');
            $restStarted = Session::get('restStarted');
        }

        return redirect('/')->with(['attendanceStarted' => $attendanceStarted, 'restStarted' => $restStarted]);
    }

    public function getAttendance(Request $request)
    {

        if ($request->input('date')) {
            if ($request->input('id') === 'previousDate') { 
                $previousDate = Carbon::parse($request->input('date'))->subDay()->format('Y-m-d');
                $date = $request->input('date');
                $nextDate = Carbon::parse($request->input('date'))->addDay()->format('Y-m-d');
            } else if ($request->input('id') === 'nextDate') {
                $previousDate = Carbon::parse($request->input('date'))->subDay()->format('Y-m-d');
                $nextDate = Carbon::parse($request->input('date'))->addDay()->format('Y-m-d');
                $date =  $request->input('date');
            }
        } else {
            $previousDate = Carbon::today()->subDay()->format('Y-m-d');
            $date = date('Y-m-d');
            $nextDate = Carbon::today()->addDay()->format('Y-m-d');
        }
        
        // テーブルの結合と必要なカラムの選択
        $attendances = User::join('Attendances', 'Users.id', '=', 'Attendances.user_id')
            ->select('Attendances.id', 'Users.name', 'Attendances.start_time', 'Attendances.end_time', 'Attendances.date')
            ->selectRaw('SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(Attendances.end_time, Attendances.start_time)))) AS worktime')
            ->whereDate('Attendances.date', '=', $date) // 日付の条件を追加
            ->groupBy('Attendances.id', 'Users.name', 'Attendances.start_time', 'Attendances.end_time', 'Attendances.date')
            ->paginate(5);

        $rest = Rest::groupBy('attendance_id')
            ->select('attendance_id', DB::raw('SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(end_time, start_time)))) AS duration'))
            ->whereHas('attendance', function ($query) use ($date) {
                $query->whereDate('created_at', '=', $date); // 日付の条件を追加
            })
            ->get()
            ->keyBy('attendance_id');

        $data = $attendances->map(function ($attendances) use ($rest) {
            $rest = $rest->get($attendances->id);

            $duration = $rest ? $rest->duration : '00:00:00';
            $worktime = $attendances->worktime;
            $difference = null;

            $durationInSeconds = strtotime($duration) - strtotime('00:00:00');
            $worktimeInSeconds = strtotime($worktime) - strtotime('00:00:00');
            $differenceInSeconds = $worktimeInSeconds - $durationInSeconds;
            $difference = gmdate('H:i:s', $differenceInSeconds);

            return [
                'name' => $attendances->name,
                'start_time' => $attendances->start_time,
                'end_time' => $attendances->end_time,
                'duration' => $rest ? $rest->duration : '00:00:00',
                'worktime' => $difference,
            ];
        });

        return view('attendance')->with([
            'previousDate' => $previousDate,
            'date' => $date,
            'nextDate' => $nextDate,
            'data' => $data,
            'attendances' => $attendances,
        ]);
    }
    
}
