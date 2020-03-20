@extends('admin.layouts.basic')
@section('title')
   سجل التقييمات
@stop
@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-12 mb-2">
                    <h3 class="content-header-title"> سجل التقييمات  </h3>
                    <div class="row breadcrumbs-top">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">الرئيسية</a>
                                </li>
                                <li class="breadcrumb-item"><a href="{{route('admin.coaches.all')}}">الكاباتن</a>
                                </li>
                                <li class="breadcrumb-item active">   التقييمات
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
                                        <table
                                            class="table display nowrap table-striped table-bordered scroll-horizontal">
                                            <thead>
                                            <tr>
                                                <th> ألاعب</th>
                                                <th> الصوره </th>
                                                <th> الفريق </th>
                                                <th> التقييم </th>
                                                <th> التعليق</th>
                                                <th>تاريخ التقييم</th>
                                                <th>يوم التقييم</th>

                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(isset($rates) && $rates -> count() > 0 )
                                                @foreach($rates as $rates)
                                                    <tr>

                                                        <td>{{$rates -> user -> name_ar}}</td>
                                                        <td>
                                                             <div class="chat-avatar">
                                                                <a class="avatar" data-toggle="tooltip" href="#"
                                                                   data-placement="left" title=""
                                                                   data-original-title=""
                                                                   style="width: 60px">
                                                                    <img src="{{$rates ->user-> photo}}"
                                                                         style="height:70px">
                                                                </a>
                                                            </div>
                                                        </td>
                                                         <td>{{$rates -> team  ->name_ar}}</td>
                                                        <td>
                                                            @for($i=0;$i < $rates -> rate;$i++)
                                                                <i class="la la-star yellow"></i>
                                                            @endfor
                                                        </td>


                                                        <td>{{$rates -> comment}}</td>
                                                        <td>{{$rates -> date}}</td>
                                                        <td>{{__('messages.'.$rates -> day_name)}}</td>
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

@stop

