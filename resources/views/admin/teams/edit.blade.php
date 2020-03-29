@extends('admin.layouts.basic')
@section('title')
    تعديل فريق
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
                                <li class="breadcrumb-item"><a href="{{route('admin.teams.all')}}"> الفرق </a>
                                </li>
                                <li class="breadcrumb-item active"> تعديل فرقة
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
                                    <h4 class="card-title" id="basic-layout-form"> تعديل فرقة </h4>
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
                                        <form class="form" action="{{route('admin.teams.update',$team -> id)}}"
                                              method="POST"
                                              enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-body">

                                                <div class="form-group">
                                                    <div class="text-center">

                                                        <img
                                                            src="{{$team -> photo}}"
                                                            class="rounded-circle  height-150" alt="صوره الفريق ">

                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label> صوره للفريق </label>
                                                    <label id="projectinput7" class="file center-block">
                                                        <input type="file" id="file" name="photo">
                                                        <span class="file-custom"></span>
                                                    </label>
                                                    @error('photo')
                                                    <span class="text-danger"> {{$message}}</span>
                                                    @enderror
                                                </div>


                                                <h4 class="form-section"><i class="ft-user"></i> بيانات الفرقة </h4>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput1"> الاسم بالعربي </label>
                                                            <input type="text" value="{{ $team -> name_ar }}"
                                                                   id="name_ar"
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
                                                            <input type="text" value="{{$team -> name_en}}" id="name_en"
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
                                                            <label for="projectinput2">عدد الحصص الشهرية </label>
                                                            <input type="number" min="1" value="{{$team -> quotas}}"
                                                                   id="quotas"
                                                                   class="form-control"
                                                                   placeholder="أدحل عدد حص الفرقه الشهرية   "
                                                                   name="quotas">
                                                            @error('quotas')
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput2"> أختر الاكاديمية </label>
                                                            <select name="academy_id"
                                                                    id="academy"
                                                                    class="select2 form-control">
                                                                <optgroup label="من فضلك أختر أكاديمية ">
                                                                    @if($academies && $academies -> count() > 0)
                                                                        @foreach($academies as $academy)
                                                                            <option value="{{$academy -> id }}"
                                                                                    @if($academy -> id == $team -> academy_id) selected @endif>{{$academy -> name}}</option>
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

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput2"> أختر القسم </label>
                                                            <select name="category_id" id="category"
                                                                    class="select2 form-control appendCategories">
                                                                <optgroup label="من فضلك أختر القسم ">
                                                                    @if(isset($categories) && $categories -> count() > 0)
                                                                        @foreach($categories as $category)
                                                                            <option
                                                                                value="{{$category -> id }}"
                                                                                @if($category -> id  == $team -> category -> id ) selected @endif>{{$category -> name_ar}}</option>
                                                                        @endforeach
                                                                    @endif
                                                                </optgroup>
                                                            </select>
                                                            @error('category_id')
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput2"> أختر الكابتن </label>
                                                            <select name="coach_id" id=""
                                                                    class="select2 form-control appendCoaches">
                                                                <optgroup label="من فضلك أختر الكابتن ">
                                                                    @if($coaches && $coaches -> count() > 0)
                                                                        @foreach($coaches as $coach)
                                                                            <option
                                                                                value="{{$coach -> id }}"
                                                                                {{$team -> coach_id  == $coach -> id ? 'selected' : ''}}>{{$coach -> name}}</option>
                                                                        @endforeach
                                                                    @endif
                                                                </optgroup>
                                                            </select>
                                                            @error('coach_id')
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput1">مستوي الفريق بالعربي</label>
                                                            <input type="text" value="{{$team -> level_ar}}" id="level_ar"
                                                                   class="form-control"
                                                                   placeholder="مستوي الفريق بالعربي "
                                                                   name="level_ar">
                                                            @error('level_ar')
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput1">مستوي الفريق بالانجليزي</label>
                                                            <input type="text" value="{{$team -> level_en}}" id="level_en"
                                                                   class="form-control"
                                                                   placeholder="مستوي الفريق بالانجليزي "
                                                                   name="level_en">
                                                            @error('level_en')
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group mt-1">
                                                            <input type="checkbox" name="status"
                                                                   id="switcheryColor4"
                                                                   class="switchery" data-color="success"
                                                                   @if($team -> status == 1) checked @endif/>
                                                            <label for="switcheryColor4"
                                                                   class="card-title ml-1">الحالة </label>
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
                        url: "{{Route('admin.categories.loadCoaches')}}",
                        data: {
                            'category_id': $('#category').val(),
                        },
                        success: function (data) {
                            console.log(data.content);
                            $('.appendCoaches').empty().append(data.content);
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
                        url: "{{Route('admin.categories.loadCoaches')}}",
                        data: {
                            'category_id': $('#category').val(),
                        },
                        success: function (data) {
                            $('.appendCoaches').empty().append(data.content);
                        }, error: function (reject) {
                            $('.appendCoaches').empty().append("<optgroup label='من فضلك أختر كابتن'>");
                        }
                    });
                }, error: function (reject) {
                    $('.appendCategories').empty();
                    $('.appendCoaches').empty();
                }
            });
        });


        $(document).on('change', '#category', function (e){

            $.ajax({
                type: 'post',
                url: "{{Route('admin.categories.loadCoaches')}}",
                data: {
                    'category_id': $('#category').val(),
                },
                success: function (data) {
                    $('.appendCoaches').empty().append(data.content);
                }, error: function (reject) {
                    $('.appendCoaches').empty();
                }
            });
        });

    </script>
@stop
