@section('main')
    @if(isset($users) && $users -> count() > 0 )
        @foreach($users as $user)

                <td>{{$user -> name_ar}}</td>
                <td><img src="{{$user -> photo}}" height="40px;"></td>
                <td>{{$user -> academy -> name_ar}}</td>
                <td>{{$user -> category -> name_ar}}</td>
                <td>{{$user -> team -> name_ar }}</td>
                <td>{{$user -> team -> level_ar }}</td>
                <td>{{$user -> mobile }}</td>
                 <td>
                    <div class="btn-group" role="group"
                         aria-label="Basic example">
                        <input type="checkbox"  value="{{$user -> id}}"  name="status"
                               id="switcheryColor4"
                               class="switchery userAttendace" data-color="success"
                               />
                    </div>
                </td>
         @endforeach
    @endif
@stop
