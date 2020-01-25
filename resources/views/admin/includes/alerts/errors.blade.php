@if(Session::has('error'))
    <div class="row">
        <div class="col-md-6  offset-3">
            <button type="text" class="btn btn-lg btn-block btn-outline-danger mb-2"
                    id="type-error">{{Session::get('error')}}
            </button>
        </div>
    </div>
@endif
