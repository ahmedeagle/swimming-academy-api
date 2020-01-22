@extends('admin.layouts.basic')
@section('title')
    الأقسام
@stop
@section('style')
@stop
@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-12 mb-2">
                    <h3 class="content-header-title">الأقسام </h3>
                    <div class="row breadcrumbs-top">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">الرئيسية</a>
                                </li>
                                <li class="breadcrumb-item active">الأقسام
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
                                    <h4 class="card-title">جميع الأقسام </h4>
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
                                        <table class="table table-striped table-bordered dom-jQuery-events">
                                            <thead>
                                            <tr>
                                                <th> الاسم بالعربي</th>
                                                <th>الاسم بالانجليزي</th>
                                                <th>الأكاديمية </th>
                                                <th>الحالة</th>
                                                <th>الأجراءات</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(isset($categories) && $categories -> count() > 0 )
                                                @foreach($categories as $category)
                                                    <tr>
                                                        <td>{{$category -> name_ar}}</td>
                                                        <td>{{$category ->name_en}}</td>
                                                        <td>{{$category -> academy -> name_ar}}</td>
                                                        <td>{{$category -> getStatus()}}</td>
                                                        <td>
                                                            <div class="btn-group" role="group"
                                                                 aria-label="Basic example">
                                                                <a href="{{route('admin.categories.edit',$category->id)}}"
                                                                   class="btn btn-float btn-outline-cyan"><i
                                                                        class="la la-edit"></i>
                                                                    <span>تعديل</span>
                                                                </a>

                                                                <button type="button"
                                                                        value="{{$category->id}}"  onclick="deletefn(this.value)"
                                                                        class="btn btn-float btn-outline-danger"
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

    @include('admin.includes.modals.deleteModal',['text' =>'سيتم حذف القسم بجميع محتواه']);

@stop

@section('script')
    <script>
        function deletefn(val){
            var a = document.getElementById('yes');
            a.href = "{{ url('admin/categories/delete/') }}"+ "/" +val;
        }
    </script>
@stop
