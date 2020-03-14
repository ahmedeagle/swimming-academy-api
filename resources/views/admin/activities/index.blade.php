@extends('admin.layouts.basic')
@section('title')
    أنشطة  الاكاديمية
@stop
@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-12 mb-2">
                    <h3 class="content-header-title"> الانشطة </h3>
                    <div class="row breadcrumbs-top">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">الرئيسية</a>
                                </li>
                                <li class="breadcrumb-item active"> الانشطة
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
                                    <h4 class="card-title">جميع الانشطة </h4>
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
                                                <th> العنوان بالعربي</th>
                                                <th> العنوان بالانجليزي</th>
                                                <th>الأكاديمية</th>
                                                <th>القسم</th>
                                                <th>الفرقه</th>
                                                <th> الفيديو</th>
                                                <th>الحالة</th>
                                                <th>الأجراءات</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(isset($activities) && $activities -> count() > 0 )
                                                @foreach($activities as $activity)
                                                    <tr>
                                                        <td>{{ Str::limit($activity -> title_ar,100)}}</td>
                                                        <td>{{Str::limit($activity ->title_en,100)}}</td>
                                                        <td>{{$activity -> academy -> name_ar}}</td>
                                                        <td>{{$activity -> category -> name_ar}}</td>
                                                        <td>{{$activity -> team -> name_ar}}</td>
                                                        <td>
                                                            <div class="embed-responsive embed-responsive-4by3">
                                                                <iframe class="border-0"
                                                                        src="{{$activity -> videoLink}}"></iframe>
                                                            </div>
                                                        </td>
                                                        <td>{{$activity -> getStatus()}}</td>
                                                        <td>
                                                            <div class="btn-group" role="group"
                                                                 aria-label="Basic example">
                                                                <a href="{{route('admin.activities.edit',$activity->id)}}"
                                                                   class="btn btn-outline-primary btn-min-width box-shadow-3 mr-1 mb-1">تعديل</a>

                                                                <a href="{{route('admin.activities.delete',$activity->id)}}"
                                                                   class="btn btn-outline-danger btn-min-width box-shadow-3 mr-1 mb-1">
                                                                    حذف</a>

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
