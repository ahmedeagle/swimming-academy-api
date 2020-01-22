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
                                <li class="breadcrumb-item"><a href="{{route('admin.academies.all')}}"> الأكاديميات </a>
                                </li>
                                <li class="breadcrumb-item active">أضافة أكاديمية
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
                                    <h4 class="card-title" id="basic-layout-form"> أضافه أكاديمية </h4>
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
                                        <form class="form" action="{{route('admin.academies.store')}}" method="POST"
                                              enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-body">

                                                <div class="form-group">
                                                    <label> شعار الاكاديمية</label>
                                                    <label id="projectinput7" class="file center-block">
                                                        <input type="file" id="file" name="logo">
                                                        <span class="file-custom"></span>
                                                    </label>
                                                    @error('logo')
                                                    <span class="text-danger"> {{$message}}</span>
                                                    @enderror
                                                </div>


                                                <h4 class="form-section"><i class="ft-user"></i> بيانات الأكاديمية </h4>
                                        {{--        <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="projectinput2"> نشاط الاكاديمية (يمكنك اختيار
                                                                أكثر من نشاط ) </label>
                                                            <select name="categories[]" multiple="multiple"
                                                                    class="select2 form-control">
                                                                <optgroup label="من فضلك أختر  نشاط الاكاديمية ">
                                                                    @if($categories && $categories -> count() > 0)
                                                                        @foreach($categories as $category)
                                                                            <option
                                                                                value="{{$category -> id }}"
                                                                                {{ (is_array(old('categories')) && in_array($category -> id, old('categories'))) ? ' selected' : '' }}
                                                                            >{{$category -> name_ar}}</option>
                                                                        @endforeach
                                                                    @endif
                                                                </optgroup>
                                                            </select>
                                                            @error('categories')
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div> --}}

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput1"> الاسم بالعربي </label>
                                                            <input type="text" value="{{old('name_ar')}}" id="name_ar"
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
                                                            <input type="text" value="{{old('name_en')}}" id="name_en"
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
                                                            <label for="projectinput2">العنوان بالعربي </label>
                                                            <input type="text" value="{{old('address_ar')}}"
                                                                   id="address_ar"
                                                                   class="form-control"
                                                                   placeholder="أدخل ألعنوان بالعربي   "
                                                                   name="address_ar">
                                                            @error('address_ar')
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput2">العنوان بالانجليزي </label>
                                                            <input type="text" value="{{old('address_en')}}"
                                                                   id="address_en"
                                                                   class="form-control"
                                                                   placeholder="أدخل ألعنوان بالانجليوي  "
                                                                   name="address_en">
                                                            @error('address_en')
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group mt-1">
                                                                <input type="checkbox" name="status"
                                                                       id="switcheryColor4"
                                                                       class="switchery" data-color="success"
                                                                       checked/>
                                                                <label for="switcheryColor4" class="card-title ml-1">الحالة </label>
                                                            </div>
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
