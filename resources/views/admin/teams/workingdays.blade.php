@extends('admin.layouts.basic')
@section('title')
    ايام الفرقة
@stop
@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-12 mb-2">
                    <h3 class="content-header-title"> أيام فرقه - {{$team -> name_ar}}</h3>
                    <div class="row breadcrumbs-top">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">الرئيسية </a>
                                </li>
                                <li class="breadcrumb-item"><a href="{{route('admin.teams.all')}}"> الفرق </a>
                                </li>
                                <li class="breadcrumb-item active"> أيام الفرقة
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Time dropper section start -->
                <section id="time-dropper">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="card-body">
                                        <div class="options">
                                            <h3>أيام الاسبوع </h3>
                                            <hr>
                                            <form class="form" action="{{route('admin.teams.postworkingdays')}}"
                                                  method="POST">
                                                @csrf
                                                <input type="hidden" name="team_id" value="{{$team -> id}}">
                                                <div class="row">
                                                    <div class="col-md-4 col-sm-12">
                                                        <div class="form-group mb-3">
                                                            <h2>السبت </h2>
                                                            <p> من
                                                            </p>
                                                            <input type="text"
                                                                   @if(isset($time ->  saturday_start_work)) value="{{$time ->  saturday_start_work}}"
                                                                   @endif name="saturday_start_work"
                                                                   class="form-control input-lg"
                                                                   id="meridians1" placeholder="Date Dropper">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-12">
                                                        <div class="form-group mb-3">
                                                            <h2> السبت</h2>
                                                            <p> ألي
                                                            </p>
                                                            <input type="text"
                                                                   @if(isset($time ->  saturday_end_work)) value="{{$time ->  saturday_end_work}}"
                                                                   @endif name="saturday_end_work"
                                                                   class="form-control input-lg"
                                                                   id="meridians2" placeholder="Date Dropper">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 col-sm-12">
                                                        <div class="form-group mb-3">
                                                            <h2>الحالة </h2>
                                                            <p> متاح / غير متاح
                                                            </p>
                                                            <input type="checkbox" name="saturday_status"
                                                                   id="switcheryColor4"
                                                                   class="switchery" data-color="success"
                                                                   @if(isset($time ->  saturday_status) && $time ->  saturday_status == 1) checked @endif/>
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="row">
                                                    <div class="col-md-4 col-sm-12">
                                                        <div class="form-group mb-3">
                                                            <h2>الاحد </h2>
                                                            <p> من
                                                            </p>
                                                            <input type="text"
                                                                   @if(isset($time ->  sunday_start_work)) value="{{$time ->  sunday_start_work}}"
                                                                   @endif name="sunday_start_work"
                                                                   class="form-control input-lg"
                                                                   id="meridians3" placeholder="Date Dropper">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-12">
                                                        <div class="form-group mb-3">
                                                            <h2> الاحد</h2>
                                                            <p> ألي
                                                            </p>
                                                            <input type="text"
                                                                   @if(isset($time ->  sunday_end_work)) value="{{$time ->  sunday_end_work}}"
                                                                   @endif name="sunday_end_work"
                                                                   class="form-control input-lg"
                                                                   id="meridians4" placeholder="Date Dropper">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-12">
                                                        <div class="form-group mb-3">
                                                            <h2>الحالة </h2>
                                                            <p> متاح / غير متاح
                                                            </p>
                                                            <input type="checkbox" name="sunday_status"
                                                                   id="switcheryColor4"
                                                                   class="switchery" data-color="success"
                                                                   @if(isset($time ->  sunday_status) && $time ->  sunday_status == 1) checked @endif />
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-4 col-sm-12">
                                                        <div class="form-group mb-3">
                                                            <h2>الاثنين </h2>
                                                            <p> من
                                                            </p>
                                                            <input type="text" name="monday_start_work"
                                                                   class="form-control input-lg"
                                                                   id="meridians5"
                                                                   @if(isset($time ->  monday_start_work)) value="{{$time ->  monday_start_work}}"
                                                                   @endif placeholder="Date Dropper">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-12">
                                                        <div class="form-group mb-3">
                                                            <h2> الاثنين</h2>
                                                            <p> ألي
                                                            </p>
                                                            <input type="text"
                                                                   @if(isset($time ->  monday_end_work)) value="{{$time ->  monday_end_work}}"
                                                                   @endif name="monday_end_work"
                                                                   class="form-control input-lg"
                                                                   id="meridians6" placeholder="Date Dropper">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-12">
                                                        <div class="form-group mb-3">
                                                            <h2>الحالة </h2>
                                                            <p> متاح / غير متاح
                                                            </p>
                                                            <input type="checkbox" name="monday_status"
                                                                   id="switcheryColor4"
                                                                   class="switchery" data-color="success"
                                                                   @if(isset($time ->  monday_status) && $time ->  monday_status == 1) checked @endif/>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-4 col-sm-12">
                                                        <div class="form-group mb-3">
                                                            <h2>الثلاثاء </h2>
                                                            <p> من
                                                            </p>
                                                            <input type="text"
                                                                   @if(isset($time ->  tuesday_start_work)) value="{{$time ->  tuesday_start_work}}"
                                                                   @endif name="tuesday_start_work"
                                                                   class="form-control input-lg"
                                                                   id="meridians7" placeholder="Date Dropper">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-12">
                                                        <div class="form-group mb-3">
                                                            <h2> الثلاثاء</h2>
                                                            <p> ألي
                                                            </p>
                                                            <input type="text"
                                                                   @if(isset($time ->  tuesday_end_work)) value="{{$time ->  tuesday_end_work}}"
                                                                   @endif
                                                                   name="tuesday_end_work"
                                                                   class="form-control input-lg"
                                                                   id="meridians8" placeholder="Date Dropper">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-12">
                                                        <div class="form-group mb-3">
                                                            <h2>الحالة </h2>
                                                            <p> متاح / غير متاح
                                                            </p>
                                                            <input type="checkbox" name="tuesday_status"
                                                                   id="switcheryColor4"
                                                                   class="switchery" data-color="success"
                                                                   @if(isset($time ->  tuesday_status) && $time ->  tuesday_status == 1) checked @endif/>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-4 col-sm-12">
                                                        <div class="form-group mb-3">
                                                            <h2>الاربعاء </h2>
                                                            <p> من
                                                            </p>
                                                            <input type="text" class="form-control input-lg"
                                                                   id="meridians9" @if(isset($time ->  wednesday_start_work)) value="{{$time ->  wednesday_start_work}}"
                                                                   @endif
                                                                   name="wednesday_start_work"
                                                                   placeholder="Date Dropper">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-12">
                                                        <div class="form-group mb-3">
                                                            <h2> الاربعاء</h2>
                                                            <p> ألي
                                                            </p>
                                                            <input type="text"  @if(isset($time ->  wednesday_end_work)) value="{{$time ->  wednesday_end_work}}"
                                                                   @endif
                                                                   name="wednesday_end_work"
                                                                   class="form-control input-lg"
                                                                   id="meridians10" placeholder="Date Dropper">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-12">
                                                        <div class="form-group mb-3">
                                                            <h2>الحالة </h2>
                                                            <p> متاح / غير متاح
                                                            </p>
                                                            <input type="checkbox" name="wednesday_status"
                                                                   id="switcheryColor4"
                                                                   class="switchery" data-color="success"
                                                                   @if(isset($time ->  wednesday_status) && $time ->  wednesday_status == 1) checked @endif />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4 col-sm-12">
                                                        <div class="form-group mb-3">
                                                            <h2>الخميس </h2>
                                                            <p> من
                                                            </p>
                                                            <input type="text" class="form-control input-lg"
                                                                   id="meridians11" @if(isset($time ->  thursday_start_work)) value="{{$time ->  thursday_start_work}}"
                                                                   @endif name="thursday_start_work"
                                                                   placeholder="Date Dropper">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-12">
                                                        <div class="form-group mb-3">
                                                            <h2> الخميس</h2>
                                                            <p> ألي
                                                            </p>
                                                            <input type="text" class="form-control input-lg"
                                                                   id="meridians12"  @if(isset($time ->  thursday_end_work)) value="{{$time ->  thursday_end_work}}"
                                                                   @endif name="thursday_end_work"
                                                                   placeholder="Date Dropper">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-12">
                                                        <div class="form-group mb-3">
                                                            <h2>الحالة </h2>
                                                            <p> متاح / غير متاح
                                                            </p>
                                                            <input type="checkbox"  name="thursday_status"
                                                                   id="switcheryColor4"
                                                                   class="switchery" data-color="success"
                                                                   @if(isset($time ->  thursday_status) && $time ->  thursday_status == 1) checked @endif />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4 col-sm-12">
                                                        <div class="form-group mb-3">
                                                            <h2>الجمعة </h2>
                                                            <p> من
                                                            </p>
                                                            <input type="text" class="form-control input-lg"
                                                                   id="meridians13"  @if(isset($time ->  friday_start_work)) value="{{$time ->  friday_start_work}}"
                                                                   @endif  name="friday_start_work"
                                                                   placeholder="Date Dropper">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-12">
                                                        <div class="form-group mb-3">
                                                            <h2> الجمعة </h2>
                                                            <p> ألي
                                                            </p>
                                                            <input type="text" class="form-control input-lg"
                                                                   id="meridians14"  @if(isset($time ->  friday_end_work)) value="{{$time ->  friday_end_work}}"
                                                                   @endif  name="friday_end_work"
                                                                   placeholder="Date Dropper">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-12">
                                                        <div class="form-group mb-3">
                                                            <h2>الحالة </h2>
                                                            <p> متاح / غير متاح
                                                            </p>
                                                            <input type="checkbox" name="friday_status"
                                                                   id="switcheryColor4"
                                                                   class="switchery" data-color="success"
                                                                   @if(isset($time ->  friday_status) && $time ->  friday_status == 1) checked @endif />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-actions">
                                                    <button type="button" class="btn btn-warning mr-1"
                                                            onclick="history.back();">
                                                        <i class="ft-x"></i> تراجع
                                                    </button>
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="la la-check-square-o"></i> حفظ
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- // Time dropper section end -->
            </div>
        </div>
    </div>
@stop
