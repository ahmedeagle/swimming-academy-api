@extends('admin.layouts.basic')
@section('title')
    تقييمات الكاباتن
@stop
@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-12 mb-2">
                    <h3 class="content-header-title"> تقييمات الكاباتن </h3>
                    <div class="row breadcrumbs-top">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">الرئيسية</a>
                                </li>
                                <li class="breadcrumb-item active"> تقييمات الكاباتن
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- DOM - jQuery events table -->
                <section id="dom">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
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
                                    <div class="card-body card-dashboard">
                                        <table
                                            class="table display nowrap table-striped table-bordered scroll-horizontal">
                                            <thead>
                                            <tr>
                                                <th> اسم الكابتن</th>
                                                <th>اسم الاعب</th>
                                                <th> ألاكاديمية</th>
                                                <th> الفريق</th>
                                                <th> التقييم</th>
                                                <th> التعليق</th>
                                                <th>تاريخ التقييم</th>
                                                <th>يوم التقييم</th>

                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(isset($rates) && $rates -> count() > 0 )
                                                @foreach($rates as $rates)
                                                    <tr>

                                                        <td>{{$rates -> coach -> name_ar}}</td>
                                                        <td>{{$rates -> user -> name_ar}}</td>
                                                        <td>{{$rates -> team -> academy -> name_ar}}</td>
                                                         <td>{{$rates -> team  ->name_ar}}</td>
                                                        <td>
                                                            {{$rates -> rate}}</td>
                                                        <td>{{$rates -> comment}}</td>
                                                        <td>{{$rates -> date}}</td>
                                                        <td>{{__('messages.'.$rates -> day_name)}}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

@stop

