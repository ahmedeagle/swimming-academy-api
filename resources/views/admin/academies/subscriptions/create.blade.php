@extends('admin.layouts.basic')
@section('title')
    أضافة أكاديمية جديدة
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
                                <li class="breadcrumb-item"><a href="{{route('admin.users.all')}}"> المستخدمين </a>
                                </li>
                                <li class="breadcrumb-item active">أضافة أشتراك
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
                                    <h4 class="card-title" id="basic-layout-form"> أضافه أشتراك للطالب
                                        - {{$user -> name_ar}} </h4>
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
                                        <form class="form" action="{{route('admin.academy.store.subscriptions')}}"
                                              method="POST"
                                              enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-body">

                                                <input name="user_id" type="hidden" value="{{$user -> id}}">

                                                <h4 class="form-section"><i class="ft-user"></i>تفاصيل الأشتراك </h4>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput2">تاريخ بدأ الاشتراك</label>
                                                            <input type="text" name="start_date"
                                                                   class="form-control input-lg dateVal"
                                                                   id="lang" placeholder="Date Dropper">
                                                            @error('start_date')
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput2">تاريخ انتهاء الاشتراك
                                                            </label>
                                                            <input type="text" name="end_date"
                                                                   class="form-control input-lg dateVal"
                                                                   id="lang3" placeholder="Date Dropper">
                                                            @error('end_date')
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput2">قيمه الاشتراك </label>
                                                            <input type="number" min="0" value="{{old('price')}}"
                                                                   id="price"
                                                                   class="form-control"
                                                                   placeholder="أدخل قيمة الاشتراك"
                                                                   name="price">
                                                            @error('price')
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
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
