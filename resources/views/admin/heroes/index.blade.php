@extends('admin.layouts.basic')
@section('title')
    الأبطال
@stop
@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-12 mb-2">
                    <h3 class="content-header-title"> الأبطال </h3>
                    <div class="row breadcrumbs-top">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">الرئيسية</a>
                                </li>
                                <li class="breadcrumb-item active"> الأبطال
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
                                    <h4 class="card-title">   {{isset($header) ? $header : 'جميع الابطال'}}   -   {{isset($startWeek) ? date('d-m-Y',strtotime($startWeek)) .' ألي ': ' '}}  {{isset($endWeek) ?  date('d-m-Y',strtotime($endWeek)) : ' '}} </h4>
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
                                            class="table display nowrap table-striped table-bordered ">
                                            <thead>
                                            <tr>
                                                <th> الاسم بالعربي</th>
                                                <th> الاسم بالانجليزي</th>
                                                <th> الفرقة </th>
                                                <th> صورة</th>
                                                <th> التاريخ</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(isset($heroes) && $heroes -> count() > 0 )
                                                @foreach($heroes as $hero)
                                                    <tr>
                                                        <td>{{$hero -> user -> name_ar}}</td>
                                                        <td>{{$hero -> user -> name_en}}</td>
                                                        <td>{{$hero -> user -> team -> name_ar}}</td>
                                                        <td><img src="{{$hero -> user -> photo}}" height="40px;"></td>
                                                        <td>   {{ __('messages.'.date('l',strtotime($hero -> created_at)))}}
                                                            - {{ date('d-m-Y',strtotime($hero -> created_at))}}  </td>
                                                        </td>
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
