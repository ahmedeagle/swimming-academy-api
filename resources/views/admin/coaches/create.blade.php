@extends('admin.layouts.basic')
@section('title')
    أضافة  مدرب  جديد
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
                                <li class="breadcrumb-item"><a href="{{route('admin.coaches.all')}}"> المدربين </a>
                                </li>
                                <li class="breadcrumb-item active">أضافة مدرب
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
                                    <h4 class="card-title" id="basic-layout-form"> أضافه مدرب </h4>
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
                                    <div class="card-body">
                                        <form class="form" action="{{route('admin.coaches.store')}}" method="POST"
                                              enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-body">

                                                <div class="form-group">
                                                    <label> صوره ألمدرب </label>
                                                    <label id="projectinput7" class="file center-block">
                                                        <input type="file" id="file" name="photo">
                                                        <span class="file-custom"></span>
                                                    </label>
                                                    @error('photo')
                                                    <span class="text-danger"> {{$message}}</span>
                                                    @enderror
                                                </div>


                                                <h4 class="form-section"><i class="ft-user"></i> بيانات المدرب </h4>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput1"> الاسم بالعربي </label>
                                                            <input type="text" value="{{old('name_ar')}}" id="name_ar"
                                                                   class="form-control"
                                                                   placeholder="ادخل الأسم باللغة العربية "
                                                                   name="name_ar">
                                                            @error('name_ar')
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput1"> الاسم بالانجليزي </label>
                                                            <input type="text" value="{{old('name_en')}}" id="name_en"
                                                                   class="form-control"
                                                                   placeholder="ادخل الأسم باللغة  الانجليزية  "
                                                                   name="name_en">
                                                            @error('name_en')
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput1"> كلمة المرو </label>
                                                            <input type="password" id="password" class="form-control"
                                                                   placeholder=" ادحل كبمة المرور الجديده  "
                                                                   name="password">
                                                            @error('password')
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput3">تأكيد كلمة المرور </label>
                                                            <input type="password" id="password_cnformation"
                                                                   class="form-control"
                                                                   placeholder="تأكيد كلمة المرور  "
                                                                   name="password_confirmation">

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput2"> رقم الهاتف </label>
                                                            <input type="number" min="1" value="{{old('mobile')}}"
                                                                   id="mobile"
                                                                   class="form-control"
                                                                   placeholder="  أدخل رقم الهاتف  "
                                                                   name="mobile">
                                                            @error('mobile')
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput2"> أختر الاكاديمية </label>
                                                            <select id="academy" name="academy_id"
                                                                    class="select2 form-control">
                                                                <optgroup label="من فضلك أختر أكاديمية ">
                                                                    @if($academies && $academies -> count() > 0)
                                                                        @foreach($academies as $academy)
                                                                            <option
                                                                                value="{{$academy -> id }}"
                                                                                @if(old('academy_id') == $academy ->  id ) selected @endif>{{$academy -> name}}</option>
                                                                        @endforeach
                                                                    @endif
                                                                </optgroup>
                                                            </select>
                                                            @error('academy_id')
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="projectinput2"> أختر القسم </label>
                                                            <select name="category_id" id="category"
                                                                    class="select2 form-control appendCategories">
                                                            </select>
                                                            @error('category_id')
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="form-group">
                                                            <label for="projectinput2"> أختر الفرق <span
                                                                    class="text-info"> (يمكنك أختيار اكثر من فرقه )</span>
                                                            </label>
                                                            <select class="select2 form-control appendTeams"
                                                                    name="teams[]"
                                                                    multiple="multiple">
                                                            </select>
                                                            @error('teams')
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group mt-1">
                                                                <input type="checkbox" name="status"
                                                                       id="switcheryColor4"
                                                                       class="switchery" data-color="success"
                                                                       checked/>
                                                                <label for="switcheryColor4" class="card-title ml-1">الحالة </label>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="text-center inline">
                                                                <div
                                                                    class="d-inline-block custom-control custom-radio mr-1">
                                                                    <input type="radio" value="1"
                                                                           class="custom-control-input"
                                                                           name="gender" id="radio1" checked>
                                                                    <label class="custom-control-label" for="radio1"
                                                                           checked> ذكر </label>

                                                                </div>
                                                                <div
                                                                    class="d-inline-block custom-control custom-radio mr-1">
                                                                    <input type="radio" class="custom-control-input"
                                                                           name="gender" value="2" id="radio2">
                                                                    <label class="custom-control-label" for="radio2"
                                                                    >أنثي</label>
                                                                </div>

                                                                @error('gender')
                                                                <span class="text-danger"> {{$message}}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-actions">
                                                    <button type="button" class="btn btn-warning mr-1"
                                                            onclick="history.back();">
                                                        <i class="ft-x"></i> تراجع
                                                    </button>
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="la la-check-square-o"></i> حفظ
                                                    </button>
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

        $(document).ready(function () {
            $.ajax({
                type: 'post',
                url: "{{Route('admin.categories.loadCategories')}}",
                data: {
                    'academy_id': $('#academy').val(),
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
