@extends('admin.layouts.basic')
@section('title')
    الكاباتن
@stop
@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-12 mb-2">
                    <h3 class="content-header-title">الكاباتن </h3>
                    <div class="row breadcrumbs-top">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">الرئيسية</a>
                                </li>
                                <li class="breadcrumb-item active"> الكاباتن
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
                                    <h4 class="card-title">جميع الكاباتن </h4>
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
                                                <th> الاسم بالعربي</th>
                                                <th>الأكاديمية</th>
                                                <th>القسم</th>
                                                <th>الهاتف</th>
                                                <th> صورة الشخصية</th>
                                                <th> النوع</th>
                                                <th>الحالة</th>
                                                <th>الأجراءات</th>


                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(isset($coaches) && $coaches -> count() > 0 )
                                                @foreach($coaches as $coach)
                                                    <tr>
                                                        <td>{{$coach -> name_ar}}</td>
                                                        <td>{{$coach -> academy -> name_ar}}</td>
                                                        <td>{{$coach ->category -> name_ar}}</td>
                                                        <td>{{$coach -> mobile}}</td>
                                                        <td>
                                                            <div class="chat-avatar">
                                                                <a class="avatar" data-toggle="tooltip" href="#"
                                                                   data-placement="left" title=""
                                                                   data-original-title=""
                                                                     style="width: 60px">
                                                                    <img src="{{$coach -> photo}}" style="height:70px">
                                                                </a>
                                                            </div>
                                                        </td>
                                                        <td>{{$coach -> getGender()}}</td>
                                                        <td>{{$coach -> getStatus()}}</td>
                                                        <td>
                                                            <div class="btn-group" role="group"
                                                                 aria-label="Basic example">
                                                                <a href="{{route('admin.coaches.edit',$coach->id)}}"
                                                                   class="btn btn-outline-primary btn-min-width box-shadow-3 mr-1 mb-1">تعديل</a>

                                                                <a href="{{route('admin.coaches.view',$coach->id)}}"
                                                                   class="btn btn-outline-success btn-min-width box-shadow-3 mr-1 mb-1">ألتفاصيل </a>

                                                                <button type="button"
                                                                        value="{{$coach->id}}"
                                                                        onclick="deletefn(this.value)"
                                                                        class="btn btn-outline-danger btn-min-width box-shadow-3 mr-1 mb-1"
                                                                        data-toggle="modal"
                                                                        data-target="#rotateInUpRight">
                                                                    حذف
                                                                </button>

                                                                <a href="{{route('admin.coaches.teams',$coach->id)}}"
                                                                   class="btn btn-outline-success btn-min-width box-shadow-3 mr-1 mb-1">الفرق</a>

                                                                <a href="{{route('admin.coaches.users',$coach->id)}}"
                                                                   class="btn btn-outline-warning btn-min-width box-shadow-3 mr-1 mb-1">الطلاب</a>
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
    @include('admin.includes.modals.deleteModal',['text' =>' هل بالفعل تريد حذف الكابتن ؟']);

@stop

@section('script')
    <script>
        function deletefn(val) {
            var a = document.getElementById('yes');
            a.href = "{{ url('admin/coaches/delete/') }}" + "/" + val;
        }
    </script>
@stop

