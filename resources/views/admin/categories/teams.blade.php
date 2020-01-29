@section('main')
    <optgroup label=" الفرق ">
        @if(isset($teams) && $teams -> count() > 0)
            @foreach($teams as $team)
                <option
                    value="{{$team -> id}}">{{$team -> name_ar}}</option>
            @endforeach
        @endif
    </optgroup>
@stop
