@if(Session::has('success'))
    <div class="row">
        <div class="col-sm-12">
            <button type="text" class="btn btn-lg btn-block btn-outline-success mb-2"
                    id="type-error">{{Session::get('success')}}
            </button>
        </div>
    </div>
@endif
