@extends('admin.layouts.basic')

@section('title', 'جميع التنبيهات')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-12 mb-2">
                    <h3 class="content-header-title">    التنبيهات </h3>
                    <div class="row breadcrumbs-top">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">الرئيسية</a>
                                </li>
                                <li class="breadcrumb-item active">  التنبيهات
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <div class="media-list">
                    @if(isset($notifications) && $notifications->count()>0)
                        @foreach($notifications as $notification)
                        <div id="headingCollapse1" class="card-header p-0">
                            @if($notification  -> type == 1)
                                <a class="collapsed email-app-sender media border-0 bg-blue-grey bg-lighten-5" href="{{route('admin.users.view',$notification -> notificationable -> id)}}">
                                @elseif($notification  -> type == 2 )
                                    <a class="collapsed email-app-sender media border-0 bg-blue-grey bg-lighten-5" href="{{route('admin.coaches.view',$notification -> notificationable -> id)}}">
                                        @elseif($notification  -> type == 3 )
                                            <a class="collapsed email-app-sender media border-0 bg-blue-grey bg-lighten-5" href="{{route('admin.users.view',$notification -> notificationable -> id)}}">
                                                @else
                                                    <a class="collapsed email-app-sender media border-0 bg-blue-grey bg-lighten-5"  href="">
                                                        @endif
                                <div class="media-left pr-1">
                      <span class="avatar avatar-md">
                        <img class="media-object rounded-circle" style="max-height: 100px;" src="{{ $notification -> notificationable -> photo}}"
                             alt="Generic placeholder image">
                      </span>
                                </div>
                                <div class="media-body w-100">
                                    <h6 class="list-group-item-heading">{{ $notification -> notificationable -> name_ar}}</h6>
                                    <p class="list-group-item-text">{{$notification -> content_ar}}.
                                        <span class="float-right text muted">{{$notification -> created_at}}</span>
                                    </p>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    @else
                        <br><br><br><br><br><br><br>
                        <div class="text-info d-flex justify-content-center " >
                                <h2><b> لا يوجد أشعارات حتي اللحظة</b></h2>
                        </div>
                    @endif
                </div>

                {!! $notifications ->appends(request()->input())->links('admin.pagination.default') !!}
            </div>
        </div>
    </div>

@stop
