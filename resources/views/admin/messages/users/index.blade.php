@extends('admin.layouts.basic')

@section('title', 'رسائل المستخدمين')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-12 mb-2">
                    <h3 class="content-header-title">التذاكر </h3>
                    <div class="row breadcrumbs-top">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">الرئيسية</a>
                                </li>
                                <li class="breadcrumb-item active"> تذاكر الطلاب
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
                                    <h4 class="card-title">جميع  تذاكر الطلاب </h4>
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
                                                <th>رقم الرسالة</th>
                                                <th> عنوان المراسلة </th>
                                                <th>اسم المستخدم</th>
                                                <th>النوع</th>
                                                <th>الأهمية</th>
                                                <th>رسائل</th>
                                                <th>التاريخ</th>
                                                <th>العمليات</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(isset($tickets) && $tickets -> count() > 0 )
                                                @foreach($tickets as $ticket)
                                                    <tr>
                                                        <td>{{$ticket -> message_no}}</td>
                                                        <td>{{$ticket ->title}}</td>
                                                        <td>{{$ticket -> ticketable -> name_ar}}</td>
                                                         <td>{{$ticket -> type}}</td>
                                                        <td>{{$ticket -> importance}}</td>
                                                        <td>@if($ticket  -> replies()  ->where('FromUser',1) ->  where('seen','0') -> count() > 0)
                                                                <span class="notification-tag badge badge-default badge-danger float-right m-0">{{$ticket  -> replies()  ->where('FromUser',1) -> where('seen','0') -> count()}} جديدة </span>
                                                            @endif</td>
                                                        <td>{{$ticket -> created_at}}</td>
                                                        <td>
                                                            <div class="btn-group" role="group"
                                                                 aria-label="Basic example">
                                                                <a href="{{route('admin.users.tickets.getreply',$ticket->id)}}"
                                                                   class="btn btn-outline-primary btn-min-width box-shadow-3 mr-1 mb-1"> الرد </a>

                                                                <button type="button"
                                                                        class="btn btn-outline-warning btn-min-width box-shadow-3 mr-1 mb-1"
                                                                        data-toggle="modal"
                                                                        data-target="#rotateInUpLeft">التفاصيل
                                                                </button>

                                                                <a href="{{route('admin.users.tickets.delete',$ticket->id)}}"
                                                                   class="btn btn-outline-danger btn-min-width box-shadow-3 mr-1 mb-1">  حذف التذكرة  </a>

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
    @if(isset($ticket))
        @include('admin.includes.modals.userMessage',$ticket)
    @endif
@stop
