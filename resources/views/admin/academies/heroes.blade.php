@if(isset($teams) && $teams -> count() > 0 )
    @foreach($teams as $_team )
        <h4 class="form-section"><i
                class="ft-user"></i> {{$_team -> name}}
        </h4>
        <fieldset class="form-group">
            @foreach($_team -> users as $user)
                <label class="btn">
                    <input type="checkbox" name="studentIds[]"
                           id="{{$user -> id}}"
                           value="{{$user -> id}}"
                           class="hidden">
                    <img data-toggle="tooltip" data-placement="top"
                         data-original-title="{{$user -> name}}"
                         style="max-width: 100px; max-height: 100px;"
                         src="{{$user -> photo}}"
                         alt="..." class="check img-thumbnail">
                </label>
            @endforeach
        </fieldset>
    @endforeach
@else
    <!-- no data here -->
@endif
