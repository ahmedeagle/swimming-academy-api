@section('main')
    <optgroup label="من فضلك أختر القسم ">
        @if(isset($categories) && $categories -> count() > 0)
            @foreach($categories as $category)
                <option
                    value="{{$category -> id }}">{{$category -> name_ar}}</option>
            @endforeach
        @endif
    </optgroup>
@stop
