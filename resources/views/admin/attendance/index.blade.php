@extends('admin.layouts.basic')
@section('title')
    الحضور والغياب
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
                                        <form id="attendance" class="form" action="" method=""
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
                                                            <span id="academy_id_error" class="text-danger"></span>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput2"> أختر القسم </label>
                                                            <select name="category_id" id="category"
                                                                    class="select2 form-control appendCategories">
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
                                                            <select class="select2 form-control appendTeams"
                                                                    name="team_id">
                                                            </select>
                                                            <span id="team_id_error" class="text-danger"></span>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput2"> اختر يوم للشهر الحالي لتحديد
                                                                الحضور والغياب </label>
                                                            <input type="text" name="date"
                                                                   class="form-control input-lg dateVal"
                                                                   id="lang" placeholder="Date Dropper">
                                                            <span id="date_error" class="text-danger"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <br>

                                                <div class="row">
                                                    <div class="col-md-12 text-center">
                                                        <button type="button"
                                                                id="showUsers"
                                                                class="btn btn-success mr-1">
                                                            <i class="la la-eye"></i> عرض الاعبين
                                                        </button>
                                                    </div>
                                                </div>

                                                <br><br>
                                                <div class="row allTeamControll" style="display: none;">
                                                    <div class="col-md-6 text-center">
                                                        <button type="button"
                                                                value="1"
                                                                class="btn btn-success mr-1 statusOfAll">
                                                            <i class="la la-check"></i> حضور جميع الفرقه
                                                        </button>
                                                    </div>

                                                    <div class="col-md-6 text-center">
                                                        <button type="button"
                                                                value="0"
                                                                class="btn btn-danger mr-1 statusOfAll">
                                                            <i class="la la-remove"></i> غياب جميع الفرقه
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>

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
                                            <tbody>
                                            <tr id="appendAttendanceUser">
                                            </tr>
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

        $(document).on('click', '#showUsers', function (e) {
            e.preventDefault();
            $('#appendAttendanceUser').empty();
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

                    if (!$.trim(data.content)) {
                        toastr.info('لا يوجد لاعبين في هذا الفريق');
                        $('.allTeamControll').hide();
                    } else {
                        toastr.success('تم جلب البيانات بنجاح ');
                        $('.allTeamControll').show();
                    }
                    $('#appendAttendanceUser').empty().append(data.content);

                }, error: function (reject) {
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
                    'date': $('.dateVal').val(),
                },
                success: function (data) {
                }, error: function (reject) {
                    toastr.error('هناك خطا جميع الحقول مطلوبة ');
                }
            });
        });

        $(document).on('click', '.statusOfAll', function (e) {
            e.preventDefault();
            let status = $(this).val();
            let date = $('.dateVal').val();
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
                }, error: function (reject) {
                    toastr.error('هناك خطا جميع الحقول مطلوبة ');
                }
            });
        });

    </script>
@stop

