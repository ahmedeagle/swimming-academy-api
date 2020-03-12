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
                                        <form id="attendance" class="form" action="" method=""
                                              enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-body">

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput2"> أختر الاكاديمية </label>
                                                            <select disabled="" id="academy" name="academy_id"
                                                                    class="select2 form-control">
                                                                <optgroup label="من فضلك أختر أكاديمية ">
                                                                    @if($academies && $academies -> count() > 0)
                                                                        @foreach($academies as $academy)
                                                                            <option
                                                                                value="{{$academy -> id }}"
                                                                                @if(Request()->academy_id ==  $academy -> id) selected @endif>{{$academy -> name}}</option>
                                                                        @endforeach
                                                                    @endif
                                                                </optgroup>
                                                            </select>
                                                            <span id="academy_id_error" class="text-danger"></span>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput2"> أختر القسم </label>
                                                            <select disabled="" name="category_id" id="category"
                                                                    class="select2 form-control appendCategories">
                                                                @if($categories && $categories -> count() > 0)
                                                                    @foreach($categories as $category)
                                                                        <option
                                                                            value="{{$category  -> id }}"
                                                                            @if( Request()->category_id  ==  $category  -> id) selected @endif>{{$category -> name_ar}}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                            <span id="category_id_error" class="text-danger"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput2"> فرقه اللاعب
                                                            </label>
                                                            <select disabled="" class="select2 form-control appendTeams"
                                                                    name="team_id" id="teamId">
                                                                @if($teams && $teams -> count() > 0)
                                                                    @foreach($teams as $team)
                                                                        <option
                                                                            value="{{$team  -> id }}"
                                                                            @if( Request()->team_id  ==  $team  -> id) selected @endif>{{$team -> name_ar}}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                            <span id="team_id_error" class="text-danger"></span>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput2"> اليوم </label>
                                                            <input disabled type="text" name="date" id="dateAttend"
                                                                   class="form-control input-lg" placeholder="">
                                                            <span id="date_error" class="text-danger"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <br>
                                                <span class="text-info">اختر يوما من ايام الفريق </span>
                                                <br>

                                                <div class="col-md-12" style="direction: rtl">
                                                    <!-- define the calendar element -->

                                                    <div id="date-popover" class="popover top"
                                                         style="cursor: pointer; display: block; margin-left: 33%; margin-top: -50px; width: 175px;">
                                                        <div class="arrow"></div>
                                                        <h3 class="popover-title" style="display: none;"></h3>

                                                        <div id="date-popover-content" class="popover-content"></div>
                                                    </div>

                                                    <div id="my-calendar"></div>
                                                    <div class="space-12"></div>
                                                </div>
                                            </div>
                                        </form>


                                        <div class="row">
                                            <div class="col-md-12 text-center">
                                                <button type="submit"
                                                        value="0"
                                                        id="showUsers"
                                                        class="btn btn-success mr-1 ">
                                                    <i class="la la-eye"></i> عرض الطلاب
                                                </button>
                                            </div>
                                        </div>

                                        <br><br>
                                        <table
                                               class="table display nowrap table-striped table-bordered scroll-horizontal">
                                            <thead>
                                            <tr>
                                                <th> الاسم بالعربي</th>
                                                <th> صوره الاعب</th>
                                                <th>الأكاديمية</th>
                                                <th>القسم</th>
                                                <th>الفرقة</th>
                                                <th>مستوي الفرقه</th>
                                                <th>رقم الهاتف</th>
                                                <th>الحضور</th>
                                            </tr>
                                            </thead>
                                            <tbody id="appendAttendanceUser">
                                            </tbody>
                                        </table>
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

        $(document).on('click', '#showUsers', function (e) {
            e.preventDefault();
            $('select,input').prop('disabled', false);
            $('#appendAttendanceUser').show().empty();
            $('#academy_id_error').empty();
            $('#category_id_error').empty();
            $('#team_id_error').empty();
            $('#date_error').empty();
            var myform = document.getElementById("attendance");
            var fdata = new FormData(myform);
            $.ajax({
                url: "{{Route('admin.attendance.loadUser')}}",
                data: fdata,
                cache: false,
                processData: false,
                contentType: false,
                type: 'post',
                success: function (data) {
                    $('select,input').prop('disabled', true);
                    if (!$.trim(data.content)) {
                        toastr.info('لا يوجد لاعبين في هذا الفريق');
                        $('.allTeamControll').hide();
                        $('#appendAttendanceUser').hide();
                    } else {
                        toastr.success('تم جلب البيانات بنجاح ');
                        $('.allTeamControll').show();
                    }
                    $('#appendAttendanceUser').empty().append(data.content);

                }, error: function (reject) {
                    $('select,input').prop('disabled', true);
                    toastr.error('عذرا هناك خطا ');
                    $('.allTeamControll').hide();
                    $('#appendAttendanceUser').empty();
                    var errors = $.parseJSON(reject.responseText);
                    $.each(errors, function (key, val) {
                        $("#" + key + "_error").text(val[0]);
                    });
                }
            });
        });

        $(document).on('change', '.userAttendace', function (e) {
            e.preventDefault();
            let attend = 0;
            if ($(this).is(":checked")) {
                attend = 1;
            }

            $.ajax({
                type: 'post',
                url: "{{Route('admin.users.attend')}}",
                data: {
                    'userId': $(this).val(),
                    'attend': attend,
                    'date': $('#dateAttend').val(),
                },
                success: function (data) {
                    toastr.success('تمت العمليه بنجاح ');
                },  error: function (jqXHR, textStatus, errorThrown) {
                         alert('Internal error: ' + jqXHR.responseText.msg);

                }

            });
        });

        $(document).on('click', '.statusOfAll', function (e) {
            e.preventDefault();
            let status = $(this).val();
            let date = $('#dateAttend').val();
            let teamId = $('select[name="team_id"] option:selected').val();

            $.ajax({
                type: 'post',
                url: "{{Route('admin.users.attendAll')}}",
                data: {
                    'attend': status,
                    'date': date,
                    'team_id': teamId,
                },
                success: function (data) {
                    if (data.attend == 0)
                        $('.userAttendace').removeAttr('checked');
                    else
                        $('.userAttendace').attr('checked', 'checked');
                }, error: function (reject) {
                    toastr.error('هناك خطا جميع الحقول مطلوبة ');
                }
            });
        });


        $(document).ready(function () {
            $("#date-popover").popover({html: true, trigger: "manual"});
            $("#date-popover").hide();
            $("#date-popover").click(function (e) {
                $(this).hide();
            });

            $("#my-calendar").zabuto_calendar({
                show_previous: false,
                language: "ar",
                today: true,
                action: function () {
                    return myDateFunction(this.id, false);
                },
                action_nav: function () {
                    return myNavFunction(this.id);
                },

                ajax: {
                    url: "{{route('admin.teams.loadTimes',['team_id' => $team_id])}}",
                    modal: true,
                },
                legend: [
                    {type: "block", classname: 'dangerc', label: "ايام غير متاحه للفريق الذي تم اختياره "}
                ],
                weekstartson: 0,
                nav_icon: {
                    next: '<i class="la la-arrow-circle-left la-3x"></i>',
                    prev: '<i class="la la-arrow-circle-right la-3x"></i>'
                }

            });
        });

        function myDateFunction(id, fromModal) {
            $("#date-popover").hide();
            if (fromModal) {
                $("#" + id + "_modal").modal("hide");
            }
            var date = $("#" + id).data("date");
            var hasEvent = $("#" + id).data("hasEvent");
            if (hasEvent && !fromModal) {
                return false;
            }
            $('#messages').empty();

            $('#dateAttend').val(date);
            return true;
        }

        function myNavFunction(id) {
            $("#date-popover").hide();
            var nav = $("#" + id).data("navigation");
            var to = $("#" + id).data("to");
        }

        $(document).on('click', '.day', function () {
            //  $(this).css('background', '#28D094');
            //$(this).css('color', '#fff');
        })

    </script>
@stop




