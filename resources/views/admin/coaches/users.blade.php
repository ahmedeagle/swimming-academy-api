@extends('admin.layouts.basic')
@section('title')
     لاعبي  الكابتن
@stop
@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-12 mb-2">
                    <h3 class="content-header-title"> لاعبي  الكابتن  - {{$coach  -> name_ar}} </h3>
                    <div class="row breadcrumbs-top">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">الرئيسية</a>
                                </li>
                                <li class="breadcrumb-item"><a href="{{route('admin.coaches.all')}}">الكاباتن </a>
                                </li>
                                <li class="breadcrumb-item active"> الاعبين
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
                                    <h4 class="card-title">جميع  لاعبي  الكابتن
                                        -
                                        <a href="{{route('admin.users.create')}}"
                                           class="btn btn-outline-success btn-min-width ">أضافة لاعب </a>
                                    </h4>
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
                                         <table
                                            class="table display nowrap table-striped table-bordered scroll-horizontal">
                                            <thead>
                                            <tr>
                                                <th> الاسم بالعربي</th>
                                                <th>الاسم بالانجليزي</th>
                                                <th>الهاتف</th>
                                                <th>البريد الالكتروني</th>
                                                <th> صورة الشخصية</th>
                                                <th>الحالة</th>
                                                <th>الأجراءات</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(isset($users) && $users -> count() > 0 )
                                                @foreach($users as $user)
                                                    <tr>
                                                        <td>{{$user -> name_ar}}</td>
                                                        <td>{{$user ->name_en}}</td>
                                                        <td>{{$user -> mobile}}</td>
                                                        <td>{{$user -> email}}</td>
                                                        <td><img src="{{$user -> photo}}" height="40px;"></td>
                                                        <td>{{$user -> getStatus()}}</td>
                                                        <td>
                                                            <div class="btn-group" role="group"
                                                                 aria-label="Basic example">

                                                                <button type="button"
                                                                        class="btn btn-outline-warning btn-min-width box-shadow-3 mr-1 mb-1"  data-toggle="modal"
                                                                        data-target="#rotateInUpRight{{$user -> id}}">التفاصيل </button>
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
    @if(isset($users) && $users -> count() > 0 )
        @foreach($users as $user)
            @include('admin.includes.modals.userDetails',$user)
        @endforeach
    @endif
@stop
