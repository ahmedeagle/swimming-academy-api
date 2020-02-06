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
                                                                   name="times[saturday][from_time]"
                                                                   value="{{(isset($times) && $times -> count ()> 0 &&  $times->contains('day_name', 'saturday')) ? $times-> where('day_name','saturday') ->first() -> from_time:''}}"
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
                                                                   name="times[saturday][to_time]"
                                                                   value="{{(isset($times) && $times -> count ()> 0 &&  $times->contains('day_name', 'saturday')) ? $times-> where('day_name','saturday') ->first() -> to_time:''}}"
                                                                   class="form-control input-lg"
                                                                   id="meridians2" placeholder="Date Dropper"
                                                            >
                                                        </div>
                                                    </div>
                                                    <input type="hidden" value="saturday" name="times[saturday][day]">
                                                    <div class="col-md-4 col-sm-12">
                                                        <div class="form-group mb-3">
                                                            <h2>الحالة </h2>
                                                            <p> متاح / غير متاح
                                                            </p>
                                                            <input type="checkbox"
                                                                   name="times[saturday][status]"
                                                                   id="switcheryColor4"
                                                                   class="switchery" data-color="success"
                                                                   @if($times->contains('day_name', 'saturday')))
                                                                   checked @endif/>
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
                                                                   name="times[sunday][from_time]"
                                                                   value="{{(isset($times) && $times -> count ()> 0 &&  $times->contains('day_name', 'sunday')) ? $times-> where('day_name','sunday') ->first() -> from_time:''}}"
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
                                                                   name="times[sunday][to_time]"
                                                                   value="{{(isset($times) && $times -> count ()> 0 &&  $times->contains('day_name', 'sunday')) ? $times-> where('day_name','sunday') ->first() -> to_time:''}}"
                                                                   class="form-control input-lg"
                                                                   id="meridians4" placeholder="Date Dropper">
                                                        </div>
                                                    </div>
                                                    <input type="hidden" value="saturday" name="times[saturday][day]">
                                                    <div class="col-md-4 col-sm-12">
                                                        <div class="form-group mb-3">
                                                            <h2>الحالة </h2>
                                                            <p> متاح / غير متاح
                                                            </p>

                                                            <input type="hidden" value="sunday"
                                                                   name="times[sunday][day]">

                                                            <input type="checkbox" name="times[sunday][status]"
                                                                   id="switcheryColor4"
                                                                   class="switchery" data-color="success"
                                                                   @if($times->contains('day_name', 'sunday')))
                                                                   checked @endif />
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-4 col-sm-12">
                                                        <div class="form-group mb-3">
                                                            <h2>الاثنين </h2>
                                                            <p> من
                                                            </p>
                                                            <input type="text"
                                                                   name="times[monday][from_time]"
                                                                   value="{{(isset($times) && $times -> count ()> 0 &&  $times->contains('day_name', 'monday')) ? $times-> where('day_name','monday') ->first() -> from_time:''}}"
                                                                   class="form-control input-lg"
                                                                   id="meridians5"
                                                                   placeholder="Date Dropper">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-12">
                                                        <div class="form-group mb-3">
                                                            <h2> الاثنين</h2>
                                                            <p> ألي
                                                            </p>
                                                            <input type="text"
                                                                   name="times[monday][to_time]"
                                                                   value="{{(isset($times) && $times -> count ()> 0 &&  $times->contains('day_name', 'monday')) ? $times-> where('day_name','monday') ->first() -> to_time:''}}"
                                                                   class="form-control input-lg"
                                                                   id="meridians6" placeholder="Date Dropper">
                                                        </div>
                                                    </div>
                                                    <input type="hidden" value="monday" name="times[monday][day]">
                                                    <div class="col-md-4 col-sm-12">
                                                        <div class="form-group mb-3">
                                                            <h2>الحالة </h2>
                                                            <p> متاح / غير متاح
                                                            </p>
                                                            <input type="checkbox" name="times[monday][status]"
                                                                   id="switcheryColor4"
                                                                   class="switchery" data-color="success"
                                                                   @if($times->contains('day_name', 'monday')))
                                                                   checked @endif />
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
                                                                   name="times[tuesday][from_time]"
                                                                   value="{{(isset($times) && $times -> count ()> 0 &&  $times->contains('day_name', 'tuesday')) ? $times-> where('day_name','tuesday') ->first() -> from_time:''}}"
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
                                                                   name="times[tuesday][to_time]"
                                                                   value="{{(isset($times) && $times -> count ()> 0 &&  $times->contains('day_name', 'tuesday')) ? $times-> where('day_name','tuesday') ->first() -> to_time:''}}"
                                                                   class="form-control input-lg"
                                                                   id="meridians8" placeholder="Date Dropper">
                                                        </div>
                                                    </div>
                                                    <input type="hidden" value="tuesday" name="times[tuesday][day]">

                                                    <div class="col-md-4 col-sm-12">
                                                        <div class="form-group mb-3">
                                                            <h2>الحالة </h2>
                                                            <p> متاح / غير متاح
                                                            </p>
                                                            <input type="checkbox" name="times[tuesday][status]"
                                                                   id="switcheryColor4"
                                                                   class="switchery" data-color="success"
                                                                   @if($times->contains('day_name', 'tuesday')))
                                                                   checked @endif />
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
                                                                   id="meridians9"
                                                                   name="times[wednesday][from_time]"
                                                                   value="{{(isset($times) && $times -> count ()> 0 &&  $times->contains('day_name', 'wednesday')) ? $times-> where('day_name','wednesday') ->first() -> from_time:''}}"
                                                                   placeholder="Date Dropper">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-12">
                                                        <div class="form-group mb-3">
                                                            <h2> الاربعاء</h2>
                                                            <p> ألي
                                                            </p>
                                                            <input type="text"
                                                                   name="times[wednesday][to_time]"
                                                                   value="{{(isset($times) && $times -> count ()> 0 &&  $times->contains('day_name', 'wednesday')) ? $times-> where('day_name','wednesday') ->first() -> to_time:''}}"
                                                                   class="form-control input-lg"
                                                                   id="meridians10" placeholder="Date Dropper">
                                                        </div>
                                                    </div>
                                                    <input type="hidden" value="wednesday" name="times[wednesday][day]">
                                                    <div class="col-md-4 col-sm-12">
                                                        <div class="form-group mb-3">
                                                            <h2>الحالة </h2>
                                                            <p> متاح / غير متاح
                                                            </p>
                                                            <input type="checkbox" name="times[wednesday][status]"
                                                                   id="switcheryColor4"
                                                                   class="switchery" data-color="success"
                                                                   @if($times->contains('day_name', 'wednesday')))
                                                                   checked @endif />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4 col-sm-12">
                                                        <div class="form-group mb-3">
                                                            <h2>الخميس </h2>
                                                            <p> من
                                                            </p>
                                                            <input type="text"
                                                                   class="form-control input-lg"
                                                                   id="meridians11"
                                                                   name="times[thursday][from_time]"
                                                                   value="{{(isset($times) && $times -> count ()> 0 &&  $times->contains('day_name', 'thursday')) ? $times-> where('day_name','thursday') ->first() -> from_time:''}}"
                                                                   placeholder="Date Dropper">

                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-12">
                                                        <div class="form-group mb-3">
                                                            <h2> الخميس</h2>
                                                            <p> ألي
                                                            </p>
                                                            <input type="text" class="form-control input-lg"
                                                                   id="meridians12"
                                                                   name="times[thursday][to_time]"
                                                                   value="{{(isset($times) && $times -> count ()> 0 &&  $times->contains('day_name', 'thursday')) ? $times-> where('day_name','thursday') ->first() -> to_time:''}}"
                                                                   placeholder="Date Dropper">
                                                        </div>
                                                    </div>
                                                    <input type="hidden" value="thursday" name="times[thursday][day]">
                                                    <div class="col-md-4 col-sm-12">
                                                        <div class="form-group mb-3">
                                                            <h2>الحالة </h2>
                                                            <p> متاح / غير متاح
                                                            </p>
                                                            <input type="checkbox" name="times[thursday][status]"
                                                                   id="switcheryColor4"
                                                                   class="switchery" data-color="success"
                                                                   @if($times->contains('day_name', 'thursday')))
                                                                   checked @endif />
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
                                                                   id="meridians13"
                                                                   name="times[friday][from_time]"
                                                                   value="{{(isset($times) && $times -> count ()> 0 &&  $times->contains('day_name', 'friday')) ? $times-> where('day_name','friday') ->first() -> from_time:''}}"
                                                                   placeholder="Date Dropper">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-12">
                                                        <div class="form-group mb-3">
                                                            <h2> الجمعة </h2>
                                                            <p> ألي
                                                            </p>
                                                            <input type="text" class="form-control input-lg"
                                                                   id="meridians14"
                                                                   name="times[friday][to_time]"
                                                                   value="{{(isset($times) && $times -> count ()> 0 &&  $times->contains('day_name', 'friday')) ? $times-> where('day_name','friday') ->first() -> to_time:''}}"
                                                                   placeholder="Date Dropper">
                                                        </div>
                                                    </div>

                                                    <input type="hidden" value="friday" name="times[friday][day]">
                                                    <div class="col-md-4 col-sm-12">
                                                        <div class="form-group mb-3">
                                                            <h2>الحالة </h2>
                                                            <p> متاح / غير متاح
                                                            </p>
                                                            <input type="checkbox" name="times[friday][status]"
                                                                   id="switcheryColor4"
                                                                   class="switchery" data-color="success"
                                                                   @if($times->contains('day_name', 'friday')))
                                                                   checked @endif />
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
