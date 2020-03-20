@extends('admin.layouts.basic')
@section('title')
    {{$coach -> name_ar}}
@stop
@section('style')

@stop
@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">الرئيسية </a>
                                </li>
                                <li class="breadcrumb-item"><a href="{{route('admin.coaches.all')}}"> الكاباتن </a>
                                </li>
                                <li class="breadcrumb-item active"> {{$coach -> name_ar}}
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Basic form layout section start -->
                <section id="basic-form-layouts">
                    <div class="row match-height">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title" id="basic-layout-form"> بيانات الكابتن
                                        - {{$coach -> name_ar}} </h4>
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
                                    <div class="card-body">
                                        <form class="form" action=""
                                              method="POST"
                                              enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-body">

                                                <div class="form-group">
                                                    <div class="text-center">
                                                        <img
                                                            src="{{$coach -> photo}}"
                                                            class="rounded-circle  height-150" alt="صوره  ألكابتن  ">
                                                    </div>
                                                </div>

                                                <h4 class="form-section"><i class="ft-user"></i> بيانات الكابتن </h4>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput1"> الاسم بالعربي </label>
                                                            <input readonly type="text" value="{{$coach -> name_ar}}"
                                                                   id="name_ar"
                                                                   class="form-control"
                                                                   name="name_ar">

                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput1"> الاسم بالانجليزي </label>
                                                            <input readonly type="text" value="{{$coach -> name_en}}"
                                                                   id="name_en"
                                                                   class="form-control"
                                                                   name="name_en">

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput1"> كلمة المرو (فقط في حاله الحاجه
                                                                لتغير الحالية )</label>
                                                            <input readonly type="password" id="password"
                                                                   class="form-control"
                                                                   name="password">

                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput3">تأكيد كلمة المرور </label>
                                                            <input readonly type="password" id="password_cnformation"
                                                                   class="form-control"
                                                                   name="password_confirmation">

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput2"> رقم الهاتف </label>
                                                            <input readonly type="number" min="1"
                                                                   value="{{$coach -> mobile}}"
                                                                   id="mobile"
                                                                   class="form-control"
                                                                   name="mobile">

                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput2"> الاكاديمية </label>
                                                            <input readonly type="text"
                                                                   value="{{$coach -> academy -> name_ar}}"
                                                                   class="form-control"
                                                                   name="academy">

                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="projectinput2">القسم </label>
                                                            <input readonly type="text"
                                                                   value="{{$coach -> category -> name_ar}}"
                                                                   class="form-control"
                                                                   name="category">

                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group mt-1">
                                                            <input readonly type="checkbox" name="status"
                                                                   id="switcheryColor4"
                                                                   class="switchery" data-color="success"
                                                                   @if($coach -> status == 1) checked @endif/>
                                                            <label for="switcheryColor4"
                                                                   class="card-title ml-1">الحالة </label>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="text-center inline">
                                                            <div
                                                                class="d-inline-block custom-control custom-radio mr-1">
                                                                <input readonly type="radio" value="1"
                                                                       class="custom-control-input"
                                                                       name="gender" id="radio1"
                                                                       @if($coach -> gender == 1  )checked @endif>
                                                                <label class="custom-control-label" for="radio1"
                                                                       checked> ذكر </label>

                                                            </div>
                                                            <div
                                                                class="d-inline-block custom-control custom-radio mr-1">
                                                                <input readonly type="radio"
                                                                       class="custom-control-input"
                                                                       name="gender" value="2" id="radio2"
                                                                       @if($coach -> gender == 2 )checked @endif>
                                                                <label class="custom-control-label" for="radio2"
                                                                >أنثي</label>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-actions d-flex justify-content-center ">
                                                    <a href="{{route('admin.coaches.teams',$coach->id)}}" type="button" class="btn btn-primary">
                                                        <i class="la la-check-square-o"></i> عرض الفرق الخاصه بالكابتن
                                                    </a>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- // Basic form layout section end -->
            </div>
        </div>
    </div>
@stop

@section('script')
    <script>
        //get academy teams branches
        $(document).on('change', '#academy', function (e) {
            e.preventDefault();
            $.ajax({

                type: 'post',
                url: "{{Route('admin.categories.loadCategories')}}",
                data: {
                    'academy_id': $(this).val(),
                },
                success: function (data) {
                    $('.appendCategories').empty().append(data.content);
                    $.ajax({
                        type: 'post',
                        url: "{{Route('admin.categories.loadTeams')}}",
                        data: {
                            'category_id': $('#category').val(),
                        },
                        success: function (data) {
                            $('.appendTeams').empty().append(data.content);
                        }
                    });
                }
            });
        });

        $(document).on('change', '#category', function (e) {
            e.preventDefault();
            $.ajax({
                type: 'post',
                url: "{{Route('admin.categories.loadTeams')}}",
                data: {
                    'category_id': $('#category').val(),
                },
                success: function (data) {
                    $('.appendTeams').empty().append(data.content);
                }
            });
        });


    </script>
@stop
