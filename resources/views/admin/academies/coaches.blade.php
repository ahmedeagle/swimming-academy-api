@section('main')
    <optgroup label="من فضلك أختر الكابتن ">
        @if(isset($coaches) && $coaches -> count() > 0)
            @foreach($coaches as $coach)
                <option
                    value="{{$coach -> id }}"
                >{{$coach -> name_ar}}</option>
            @endforeach
        @endif
    </optgroup>
@stop
