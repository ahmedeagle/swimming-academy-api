@extends('admin.layouts.basic')
@section('title')
    الأبطال
@stop
@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-12 mb-2">
                    <h3 class="content-header-title"> الأبطال </h3>
                    <div class="row breadcrumbs-top">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">الرئيسية</a>
                                </li>
                                <li class="breadcrumb-item active"> الأبطال
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
                                    <h4 class="card-title">   {{isset($header) ? $header : 'جميع الابطال'}}
                                        - {{isset($startWeek) ? date('d-m-Y',strtotime($startWeek)) .' ألي ': ' '}}  {{isset($endWeek) ?  date('d-m-Y',strtotime($endWeek)) : ' '}} </h4>
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
                                                <th>صورة الطالب</th>
                                                <th>صورة الطالب بالمسابقه</th>
                                                <th> الاكاديمية</th>
                                                <th> القسم</th>
                                                <th> الفرقة</th>
                                                <th> التفاصيل</th>
                                                <th> التاريخ</th>
                                                <th> الاجراءات</th>

                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(isset($heroes) && $heroes -> count() > 0 )
                                                @foreach($heroes as $hero)
                                                    <tr>
                                                        <td>{{$hero -> user -> name_ar}}</td>
                                                        <td><img src="{{$hero -> user -> photo}}" height="40px;"></td>
                                                        <td> @if(isset($hero -> hero_photo ) &&  $hero -> hero_photo !="")<img src="{{$hero  -> hero_photo}}" height="40px;"> @else 'لم يتم اضافهتا' @endif</td>
                                                        <td>{{$hero -> academy -> name_ar}}</td>
                                                        <td>{{$hero -> category -> name_ar}}</td>
                                                        <td>{{$hero  -> team -> name_ar}}</td>
                                                        <td>{{$hero  -> note_ar ? $hero -> note_ar : '---'}}</td>
                                                        <td>   {{ __('messages.'.date('l',strtotime($hero -> created_at)))}}
                                                            - {{ date('d-m-Y',strtotime($hero -> created_at))}}  </td>
                                                        </td>

                                                        <td>
                                                            <div class="btn-group" role="group"
                                                                 aria-label="Basic example">
                                                                <a href="{{route('admin.heroes.delete',$hero->id)}}"
                                                                   class="btn btn-outline-danger btn-min-width box-shadow-3 mr-1 mb-1">
                                                                    حذف</a>

                                                                <button type="button"
                                                                        value="{{$hero->id}}"
                                                                        class="btn btn-outline-info btn-min-width box-shadow-3 mr-1 mb-1"
                                                                        data-toggle="modal"
                                                                        data-target="#rotateInUpRightHero{{$hero->id}}">
                                                                    اضافة تفاصيل وصورة المسابقه
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
    @if(isset($heroes) && $heroes -> count() > 0 )
        @foreach($heroes as $hero)
            @include('admin.includes.modals.infoModal',['hero' => $hero]);
        @endforeach
    @endif
@stop


@section('script')
    <script>
        @if(Session::has('modalId'))
        $("#rotateInUpRightHero{{Session::get('modalId')}}").modal('toggle');
        @endif
    </script>
@stop
