@extends('admin.layouts.basic')
@section('title')
    ألاكاديميات
    @stop
@section('style')
@stop
@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-12 mb-2">
                    <h3 class="content-header-title">الأكاديميات </h3>
                    <div class="row breadcrumbs-top">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">الرئيسية</a>
                                </li>
                                <li class="breadcrumb-item active">الأكاديميات
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
                                    <h4 class="card-title">جميع الاكاديميات </h4>
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
                                        <table class="table table-striped table-bordered dom-jQuery-events  scroll-horizontal">
                                            <thead>
                                            <tr>
                                                <th> الشعار </th>
                                                <th> الاسم بالعربي</th>
                                                <th>الاسم بالانجليزي</th>
                                                <th> العنوان بالعربي</th>
                                                <th> العنوان بالانجليزي </th>
                                                 <th>كود الدخول</th>
                                                <th>الحالة</th>
                                                <th>الأجراءات</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(isset($academies) && $academies -> count() > 0 )
                                                @foreach($academies as $academy)
                                                    <tr>
                                                        <td><img src="{{$academy -> logo}}" height="40px;"></td>
                                                        <td>{{$academy -> name_ar}}</td>
                                                        <td>{{$academy ->name_en}}</td>
                                                        <td>{{$academy -> address_ar}}</td>
                                                        <td>{{$academy -> address_en}}</td>
                                                         <td>{{$academy -> code}}</td>
                                                        <td>{{$academy -> getStatus()}}</td>
                                                        <td>
                                                            <div class="btn-group" role="group"
                                                                 aria-label="Basic example">
                                                                <a  href="{{route('admin.academies.edit',$academy->id)}}" class="btn btn-outline-primary btn-min-width box-shadow-3 mr-1">
                                                                    <span>تعديل</span>
                                                                </a>
                                                                <a  href="{{route('admin.academies.aboutus',$academy->id)}}" class="btn btn-outline-info btn-min-width box-shadow-3 mr-1">
                                                                    <span>البيانات</span>
                                                                </a>
                                                                <button type="button"
                                                                        value="{{$academy->id}}"  onclick="deletefn(this.value)"
                                                                        class="btn btn-outline-danger btn-min-width box-shadow-3 mr-1"
                                                                        data-toggle="modal"
                                                                        data-target="#rotateInUpRight">
                                                                    <i class="la la-remove"></i>
                                                                    حذف
                                                                </button>

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
    @include('admin.includes.modals.deleteModal',['text' =>'سيتم حذف الاكاديمية بجميع محتواها']);
@stop

@section('script')
    <script>
        function deletefn(val){
            var a = document.getElementById('yes');
            a.href = "{{ url('admin/academies/delete/') }}"+ "/" +val;
        }
    </script>
@stop
