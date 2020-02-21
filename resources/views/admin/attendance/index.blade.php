@extends('admin.layouts.basic')
@section('title')
    الحضور والغياب
@stop
@section('style')
    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/zabuto_calendar/1.6.4/zabuto_calendar.min.css">
    <style>
        .dangerc {
            background-color: #fa5c66;
        }

        .list_se_f li {
            display: inline-block;
        }

        .list_se_f li a {
            display: block;
            background-color: #fff;
            padding: 7px;
            border: 1px solid #eaeaea;
            margin-left: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            font-size: 14px;
        }
    </style>
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
                                <li class="breadcrumb-item active">الحضور والغياب
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
                                    <h4 class="card-title" id="basic-layout-form"> حضور الطلاب</h4>
                                    <br>
                                    <span class="text-info">اختر اليوم لتحديد غياب وحضور الاعب بهذا اليوم -يمكنك فقط تحديد حضور وغياب الاعب للشهر الحالي </span>
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
                                        <form  class="form" action="{{route('admin.attendance.times')}}"
                                              method=""
                                              enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-body">

                                                <div class="row">
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
                                                                                @if(old('academy_id')  ==  $academy -> id) selected @endif>{{$academy -> name}}</option>
                                                                        @endforeach
                                                                    @endif
                                                                </optgroup>
                                                            </select>
                                                            @error('academy_id')
                                                            <span class="text-danger">{{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput2"> أختر القسم </label>
                                                            <select name="category_id" id="category"
                                                                    class="select2 form-control appendCategories">
                                                            </select>
                                                            @error('category_id')
                                                            <span class="text-danger">{{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="projectinput2"> فرقه اللاعب
                                                            </label>
                                                            <select class="select2 form-control appendTeams"
                                                                    name="team_id" id="teamId">
                                                            </select>
                                                            @error('team_id')
                                                            <span class="text-danger">{{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <br>

                                                <div class="row">
                                                    <div class="col-md-12 text-center">
                                                        <button type="submit"
                                                                value="0"
                                                                class="btn btn-danger mr-1 ">
                                                            <i class="la la-arrow-circle-o-left"></i> التالي
                                                        </button>
                                                    </div>
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
    <script src="{{asset('assets/admin/vendors/js/zabuto_calendar.min.js')}}" type="text/javascript"></script>
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

        //get academy teams branches
        $(document).on('change', '#academy', function (e) {
            e.preventDefault();
            $('.allTeamControll').hide();
            $('#appendAttendanceUser').hide();

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

                            if (!$.trim(data.content)) {
                                $('.allTeamControll').hide();
                                $('#appendAttendanceUser').hide();
                            }
                        }, error: function (reject) {
                            $('.allTeamControll').hide();
                            $('#appendAttendanceUser').hide();
                                 $('.appendTeams').empty().append("<optgroup label='الفرق'>");
                         }
                    });
                }
            });
        });

        $(document).on('change', '#category', function (e) {
            e.preventDefault();
            $('.allTeamControll').hide();
            $('#appendAttendanceUser').hide();
            $.ajax({
                type: 'post',
                url: "{{Route('admin.categories.loadTeams')}}",
                data: {
                    'category_id': $('#category').val(),
                },
                success: function (data) {
                    $('.appendTeams').empty().append(data.content);
                    if (!$.trim(data.content)) {
                        $('.allTeamControll').hide();
                        $('#appendAttendanceUser').hide();
                    }
                }, error: function (reject) {
                    $('.allTeamControll').hide();
                    $('#appendAttendanceUser').hide();
                }
            });
        });

    </script>
@stop

