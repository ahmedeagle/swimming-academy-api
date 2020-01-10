@extends('admin.layouts.basic')
@section('title')
    مدربي الفرقة
@stop
@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-12 mb-2">
                    <h3 class="content-header-title"> مدربي الفرقة - {{$team  -> name_ar}} </h3>
                    <div class="row breadcrumbs-top">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">الرئيسية</a>
                                </li>
                                <li class="breadcrumb-item"><a href="{{route('admin.teams.all')}}">الفرق</a>
                                </li>
                                <li class="breadcrumb-item active"> المدربين
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
                                    <h4 class="card-title">جميع  مدربي الفرقة  </h4>
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
                                        <table class="table display nowrap table-striped table-bordered /*scroll-horizontal*/">
                                            <thead>
                                            <tr>
                                                <th> الاسم بالعربي</th>
                                                <th>الاسم بالانجليزي</th>
                                                <th> الهاتف</th>
                                                <th>النوع </th>
                                                <th> صورة  المدرب</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(isset($coaches) && $coaches -> count() > 0 )
                                                @foreach($coaches as $coach)
                                                    <tr>
                                                        <td>{{$coach -> name_ar}}</td>
                                                        <td>{{$coach ->name_en}}</td>
                                                        <td>{{$coach -> mobile}}</td>
                                                        <td>{{$coach -> gender}}</td>
                                                        <td><img src="{{$coach -> photo}}" height="40px;"></td>
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
