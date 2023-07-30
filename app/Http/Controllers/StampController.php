<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Work;
use App\Models\Rest;
use App\Http\Requests\RegisterRequest;

class StampController extends Controller
{

    public function create()
    {
        $workStarted = Session::get('workStarted', false);
        $restStarted = Session::get('restStarted', false);
        return view('stamp', ['workStarted' => $workStarted, 'restStarted' => $restStarted]);
    }

    public function punch(Request $request)
    {

        $inputs = $request->all();

        if ($request->has('start_work')) {

            $work = new Work();
            $work->employee_id = Auth::user()->id;
            $work->start_work = now();
            $work->save();
            Session::put('workStarted', true);

        } elseif ($request->has('end_work')) {

            $work = Work::where('employee_id', Auth::user()->id)
                        ->whereNull('end_work')
                        ->latest()
                        ->first();
            
            if ($work) {
                $work->end_work = now();
                $work->save();
            }

            Session::put('workStarted', false);

        } elseif ($request->has('start_rest')) {

            $rest = new Rest();
            $rest->job_id = Auth::user()->id;
            $rest->start_rest = now();
            $rest->save();
            Session::put('restStarted', true);

        } elseif ($request->has('end_rest')) {

            $rest = Rest::where('job_id', Auth::user()->id)
                        ->whereNull('end_rest')
                        ->latest()
                        ->first();
            
            if ($rest) {
                $rest->end_rest = now();
                $rest->save();
            }

            Session::put('restStarted', false);

        }

        return view('stamp', ['workStarted' => Session::get('workStarted'), 'restStarted' => Session::get('restStarted')]);
    }
    
}
