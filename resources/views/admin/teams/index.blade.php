@extends('admin.layouts.basic')
@section('title')
    الفرق
@stop
@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-12 mb-2">
                    <h3 class="content-header-title">الفرق </h3>
                    <div class="row breadcrumbs-top">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">الرئيسية</a>
                                </li>
                                <li class="breadcrumb-item active">الفرق
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
                                    <h4 class="card-title">جميع الفرق </h4>
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
                                <div class="card-content collapse show">
                                    <div class="card-body card-dashboard">
                                        <table class="table display nowrap table-striped table-bordered scroll-horizontal">
                                            <thead>
                                            <tr>
                                                <th> الاسم بالعربي</th>
                                                <th>الاسم بالانجليزي</th>
                                                <th>الأكاديمية</th>
                                                <th> عدد الحصص الشهرية</th>
                                                <th> صورة الفرقه</th>
                                                <th> أيام الفرقه  </th>
                                                <th>الحالة</th>
                                                <th>الأجراءات</th>


                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(isset($teams) && $teams -> count() > 0 )
                                                @foreach($teams as $team)
                                                    <tr>
                                                        <td>{{$team -> name_ar}}</td>
                                                        <td>{{$team ->name_en}}</td>
                                                        <td>{{$team -> academy -> name}}</td>
                                                        <td>{{$team -> quotas}}</td>
                                                        <td><img src="{{$team -> photo}}" height="40px;"></td>
                                                        <td><a href="{{route('admin.teams.days',$team->id)}}"
                                                               class="btn btn-outline-danger btn-min-width box-shadow-3 mr-1 mb-1">عرض الايام </a></td>
                                                        <td>{{$team -> getStatus()}}</td>
                                                        <td>
                                                            <div class="btn-group" role="group"
                                                                 aria-label="Basic example">
                                                                <a href="{{route('admin.teams.edit',$team->id)}}"
                                                                   class="btn btn-outline-primary btn-min-width box-shadow-3 mr-1 mb-1">تعديل</a>
                                                                <a href="{{route('admin.teams.coaches',$team->id)}}"  class="btn btn-outline-success btn-min-width box-shadow-3 mr-1 mb-1">المدربين</a>
                                                                <a href="{{route('admin.teams.users',$team->id)}}"
                                                                   class="btn btn-outline-danger btn-min-width box-shadow-3 mr-1 mb-1">الطلاب</a>
                                                            </div>
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
