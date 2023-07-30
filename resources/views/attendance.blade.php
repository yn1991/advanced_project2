@extends('layouts.index')

@section('index')
    <div class="form-inner">
        <div class="form-cont">
            <div class="mb-b_40">
                <a href="{{ route('getAttendance', ['date' => $previousDate, 'id' => 'previousDate']) }}" class="previousday" id="previousday">&lt;</a>
                <p class="title_name">{{ $date }}</p>
                <a href="{{ route('getAttendance', ['date' => $nextDate, 'id' => 'nextDate']) }}" class="nextday">&gt;</a>
            </div>

            <hr>
            <table>
                <thead>
                    <tr>
                        <th class="name">名前</th>
                        <th class="start_work">勤務開始</th>
                        <th class="end_work">勤務終了</th>
                        <th class="rest_time">休憩時間</th>
                        <th class="work_time">勤務時間</th>
                    </tr>
                </thead>
            </table>
            <hr>
            <table>
                <tbody>
                    @foreach($data as $item)
                    <tr>
                        <td class="name">{{ $item['name'] }}</td>
                        <td class="start_work">{{ $item['start_time'] }}</td>
                        <td class="end_work">{{ $item['end_time'] }}</td>
                        <td tdclass="rest_time">{{ $item['duration'] }}</td>
                        <td class="work_time">{{ $item['worktime'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
             
        </div>
    <div class="pagination pagination-sm">
        {{$attendances->links()}}
    </div> 
    </div>

@endsection

