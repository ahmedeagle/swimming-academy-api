@extends('admin.layouts.login')

@section('title')
    تأكيد الكود
@stop

@section("content")

    <section class="flexbox-container">
        <div class="col-12 d-flex align-items-center justify-content-center">
            <div class="col-md-4 col-10 box-shadow-2 p-0">
                <div class="card border-grey border-lighten-3 px-2 py-2 m-0">
                    <div class="card-header border-0 pb-0">
                        <div class="card-title text-center">
                            <img src="{{asset('assets/admin/images/logo/logo-dark.png')}}" alt="branding logo">
                        </div>
                        <h6 class="card-subtitle line-on-side text-muted text-center font-small-3 pt-2">
                            <span> من فضلك ادخل الكود المرسل الي بريدك الالكتروني  </span>
                        </h6>
                    </div>
                    @include('admin.includes.alerts.errors')
                    @include('admin.includes.alerts.success')
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form-horizontal" method="POST" action="{{route('admin.confirmcode')}}" novalidate>
                                @csrf
                                <fieldset class="form-group position-relative has-icon-left">
                                    <input type="text" name="activation_code"  value="{{isset($code) ? $code:''}}" class="form-control form-control-lg input-lg"
                                           id="user-code"
                                           placeholder="أدخل الكود ">
                                    <div class="form-control-position">
                                        <i class="ft-mail"></i>
                                    </div>
                                    @error('activation_code')
                                    <span class="text-danger">{{$message}} </span>
                                    @enderror
                                </fieldset>
                                <button type="submit" class="btn btn-outline-info btn-lg btn-block"><i
                                        class="ft-unlock"></i> تأكيد  الكود
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="card-footer border-0">
                        <p class="float-sm-left text-center"><a href="{{route('admin.login')}}" class="card-link">
                                    الدخول   </a></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection



