@extends('admin.layouts.basic')
@section('title')
     تعديل  نشاط
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
                                <li class="breadcrumb-item"><a href="{{route('admin.activities.all')}}"> الانشطة </a>
                                </li>
                                <li class="breadcrumb-item active"> تعديل نشاط
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
                                    <h4 class="card-title" id="basic-layout-form">  تعديل  نشاط - {{$activity -> title_ar}} </h4>
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
                                        <form class="form" action="{{route('admin.activities.update',$activity->id)}}" method="POST"
                                              enctype="multipart/form-data">
                                            @csrf

                                            <div class="col-md-6  offset-md-3 col-sm-12">
                                                <iframe class="border-0" src="{{$activity -> videoLink}}"
                                                        width="400" height="200"></iframe>
                                            </div>
                                            <div class="form-body">
                                                <h4 class="form-section"><i class="ft-user"></i> بيانات فيديو النشاط
                                                </h4>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput1"> العنوان بالعربي </label>
                                                            <input type="text" value="{{$activity -> title_ar}}" id="title_ar"
                                                                   class="form-control"
                                                                   placeholder="ادخل  العنوان  باللغة العربية "
                                                                   name="title_ar">
                                                            @error('title_ar')
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput1"> العنوان بالانجليزي </label>
                                                            <input type="text" value="{{$activity -> title_en}}" id="title_en"
                                                                   class="form-control"
                                                                   placeholder="ادخل   العنوان  باللغة  الانجليزية  "
                                                                   name="title_en">
                                                            @error('title_en')
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="projectinput1"> رابط الفيديو </label>
                                                            <input type="text" value="{{$activity -> videoLink}}"
                                                                   id="videoLink"
                                                                   class="form-control"
                                                                   placeholder="ادخل رابط فيديو اليوتيوب "
                                                                   name="videoLink">
                                                            @error('videoLink')
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <span class="text-info">يجب ان يكون رابط يوتيوب مثل (https://www.youtube.com/embed/tr7AcjcE0BQ)</span>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group mt-1">
                                                            <input type="checkbox" name="status"
                                                                   id="switcheryColor4"
                                                                   class="switchery" data-color="success"
                                                                   @if($activity -> status == 1) checked @endif/>
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

