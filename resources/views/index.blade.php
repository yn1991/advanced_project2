@extends('layouts.index')

@section('index')
    <div class="form-inner">
        <div class="form-cont">
            <div class="mb-b_40">
                <p class="title_name">{{ Auth::user()->name }}さんお疲れ様です！</p>
            </div>
            @csrf

            <div class="attendance">
                <form method='get' action="{{ route('startAttendance') }}" class="start_attendance">
                    @if ($attendanceStarted)
                        <input type="submit" name="start_attendance" value="勤務開始" class="start_attendance_btn_disabled" disabled>
                    @else
                        <input type="submit" name="start_attendance" value="勤務開始" class="start_attendance_btn">
                    @endif
                </form>
                <form method='get' action="{{ route('endAttendance') }}" class="end_attendance">
                    @if ($attendanceStarted == true && $restStarted == false)
                        <input type="submit" name="end_attendance" value="勤務終了" class="end_attendance_btn">
                    @else
                        @if ($restStarted == true || $attendanceStarted == false && $restStarted == false)
                            <input type="submit" name="end_attendance" value="勤務終了" class="end_attendance_btn_disabled" disabled>
                        @else
                            <input type="submit" name="end_attendance" value="勤務終了" class="end_attendance_btn">
                        @endif
                    @endif
                </form>
            </div>
            
            <div class="rest">
                <form method='get' action="{{ route('startRest') }}" class="start_rest">
                    @if ($attendanceStarted == false)
                        <input type="submit" name="start_rest" value="休憩開始" class="start_rest_btn_disabled" disabled>
                    @else
                        @if ($restStarted == true)
                            <input type="submit" name="start_rest" value="休憩開始" class="start_rest_btn_disabled" disabled>
                        @else
                            <input type="submit" name="start_rest" value="休憩開始" class="start_rest_btn">
                        @endif
                    @endif
                </form>
                <form method='get' action="{{ route('endRest') }}" class="end_rest">               
                    @if ($attendanceStarted == false)
                        <input type="submit" name="end_rest" value="休憩終了" class="end_rest_btn_disabled" disabled>
                    @else
                        @if ($restStarted == false)
                            <input type="submit" name="end_rest" value="休憩終了" class="end_rest_btn_disabled" disabled>
                        @else
                            <input type="submit" name="end_rest" value="休憩終了" class="end_rest_btn">
                        @endif
                    @endif
                </form>
            </div>

        </div>
    </div>
@endsection

