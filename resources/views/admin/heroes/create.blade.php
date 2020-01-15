@extends('admin.layouts.basic')
@section('title')
    أضافة   أبطال الاسبوع
@stop
@section('style')
@stop
@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">الرئيسية </a>
                                </li>
                                <li class="breadcrumb-item"><a href="{{route('admin.heroes.all')}}">  الابطال  </a>
                                </li>
                                <li class="breadcrumb-item active">أضافة  أبطال  الاسبوع
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Basic form layout section start -->
                <section id="basic-form-layouts">
                    <div class="row match-height">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title" id="basic-layout-form">   أبطال الاسبوع  <span class="text-success">  السبت : {{$startWeek }} الي  الجمعة  {{$endWeek}}  </span>  </h4>
                                    <a class="heading-elements-toggle"><i
                                            class="la la-ellipsis-v font-medium-3"></i></a>
                                    <div class="heading-elements">
                                        <ul class="list-inline mb-0">
                                            <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                                            <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                                            <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                            <li><a data-action="close"><i class="ft-x"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                                @include('admin.includes.alerts.success')
                                @include('admin.includes.alerts.errors')
                                <div class="card-content collapse show">
                                    <div class="card-body">
                                        <form class="form" action="{{route('admin.heroes.store')}}" method="POST"
                                              enctype="multipart/form-data">
                                            @csrf

                                            <input type="hidden" name="startWeek" value="{{$startWeek}}" >
                                            <input type="hidden" name="endWeek" value="{{$endWeek}}" >
                                            <div class="form-body">
                                                @if(isset($teams) && $teams -> count() > 0 )
                                                    @foreach($teams as $_team )
                                                        <h4 class="form-section"><i
                                                                class="ft-user"></i> {{$_team -> name}}
                                                        </h4>
                                                        <fieldset class="form-group">
                                                            @foreach($_team -> users as $user)
                                                                <label class="btn">
                                                                    <input type="checkbox" name="studentIds[]" id="{{$user -> id}}"
                                                                           value="{{$user -> id}}"
                                                                           class="hidden">
                                                                    <img data-toggle="tooltip" data-placement="top"  data-original-title="{{$user -> name}}" style="max-width: 100px; max-height: 100px;"
                                                                        src="{{$user -> photo}}"
                                                                        alt="..." class="check img-thumbnail">
                                                                </label>
                                                            @endforeach
                                                        </fieldset>
                                                    @endforeach
                                                @else
                                                <!-- no data here -->
                                                @endif
                                                <div class="form-actions">
                                                    <button type="button" class="btn btn-warning mr-1"
                                                            onclick="history.back();">
                                                        <i class="ft-x"></i> تراجع
                                                    </button>
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="la la-check-square-o"></i> حفظ
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- // Basic form layout section end -->
            </div>
        </div>
    </div>
@stop


@section('script')
@stop
