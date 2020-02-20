@extends('admin.layouts.basic')
@section('title')
    الاعبين
@stop
@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-12 mb-2">
                    <h3 class="content-header-title"> الاعبين </h3>
                    <div class="row breadcrumbs-top">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">الرئيسية</a>
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
                                    <h4 class="card-title">جميع الاعبين </h4>
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
                                                <th>الاسم بالانجليزي</th>
                                                <th>الأكاديمية</th>
                                                <th>القسم</th>
                                                <th>الفرق</th>
                                                <th>الهاتف</th>
                                                <th>البريد الالكتروني</th>
                                                <th> صورة الشخصية</th>
                                                <th>الحالة</th>
                                                <th>أشتراك الاكاديمية</th>
                                                <th>أشتراك التطبيق</th>
                                                <th>الأجراءات</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(isset($users) && $users -> count() > 0 )
                                                @foreach($users as $user)
                                                    <tr>
                                                        <td>{{$user -> name_ar}}</td>
                                                        <td>{{$user ->name_en}}</td>
                                                        <td>{{$user ->academy -> name_ar}}</td>
                                                        <td>{{$user ->category -> name_ar}}</td>
                                                        <td>{{$user ->team -> name_ar}}</td>
                                                        <td>{{$user -> mobile}}</td>
                                                        <td>{{$user -> email}}</td>
                                                        <td><img src="{{$user -> photo}}" height="40px;"></td>
                                                        <td>{{$user -> getStatus()}}</td>
                                                        <td>{{$user -> getAcademySubscribed()}}</td>
                                                        <td>{{$user -> getApplicationSubscribed()}}</td>
                                                        <td>
                                                            <div class="btn-group" role="group"
                                                                 aria-label="Basic example">
                                                                <a href="{{route('admin.users.edit',$user->id)}}"
                                                                   class="btn btn-outline-primary btn-min-width box-shadow-3 mr-1 mb-1">تعديل</a>
                                                                <button type="button"
                                                                        value="{{$user->id}}"
                                                                        onclick="deletefn(this.value)"
                                                                        class="btn btn-outline-danger btn-min-width box-shadow-3 mr-1 mb-1"
                                                                        data-toggle="modal"
                                                                        data-target="#rotateInUpRight">
                                                                    حذف
                                                                </button>

                                                                <button type="button"
                                                                        class="btn btn-outline-warning btn-min-width box-shadow-3 mr-1 mb-1 userModalDetails"
                                                                        data-toggle="modal"
                                                                        data-target="#rotateInUpRight{{$user -> id}}">
                                                                    التفاصيل
                                                                </button>

                                                                <a href="{{route('admin.academy.subscriptions',['user_id' => $user->id,'type' => 'all'])}}"
                                                                   class="btn btn-outline-primary btn-min-width box-shadow-3 mr-1 mb-1">أشتراكات الاكاديمية </a>

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
    <div class="userModalContent">
    </div>
    @if(isset($users) && $users -> count() > 0 )
        @foreach($users as $user)
            @include('admin.includes.modals.userDetails',$user)
        @endforeach
    @endif
    @include('admin.includes.modals.deleteModal',['text' =>' هل بالفعل تريد حذف الاعب ؟']);
@stop

@section('script')
    <script>
        function deletefn(val) {
            var a = document.getElementById('yes');
            a.href = "{{ url('admin/users/delete/') }}" + "/" + val;
        }
    </script>
@stop

