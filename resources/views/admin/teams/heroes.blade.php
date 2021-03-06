@section('main')
    @if(isset($users) && $users -> count() > 0)
        <fieldset class="form-group">
            @foreach($users as $user)
                <label class="btn" >
                    <input type="checkbox" name="studentIds[]" id="{{$user -> id}}"
                           value="{{$user -> id}}"
                           class="hidden">
                    <img data-toggle="tooltip"  data-placement="top" title="{{$user -> name_ar}}" data-original-title="{{$user -> name_ar}}"
                         style="max-width: 100px; max-height: 80px;"
                         src="{{$user -> photo}}"
                         alt="..." class="check img-thumbnail">
                </label>
            @endforeach
        </fieldset>
    @else
        <span class="text-center text-danger">{{@$message}}</span>
    @endif
@stop
