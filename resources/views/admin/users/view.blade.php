@extends('admin.layouts.basic')
@section('title')
    {{$user -> name_ar}}
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
                                <li class="breadcrumb-item"><a href="{{route('admin.users.all')}}"> الاعبين </a>
                                </li>
                                <li class="breadcrumb-item active">   {{$user -> name_ar}}
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
                                    <h4 class="card-title" id="basic-layout-form"> بيانات اللاعب
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

                                <div class="card-content collapse show">
                                    <div class="card-body">
                                        <form class="form" action=""
                                              method="POST"
                                              enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-body">

                                                <div class="form-group">
                                                    <div class="text-center">
                                                        <img
                                                            src="{{$user -> photo}}"
                                                            class="rounded-circle  height-150" alt="صوره  اللاعب  ">
                                                    </div>
                                                </div>


                                                <h4 class="form-section"><i class="ft-user"></i> بيانات الاعب </h4>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput1"> الاسم بالعربي </label>
                                                            <input readonly type="text" value="{{$user -> name_ar}}"
                                                                   id="name_ar"
                                                                   class="form-control"

                                                                   name="name_ar">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput1"> الاسم بالانجليزي </label>
                                                            <input readonly type="text" value="{{$user -> name_en}}"
                                                                   id="name_en"
                                                                   class="form-control"

                                                                   name="name_en">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput1"> العنوان بالعربي </label>
                                                            <input readonly type="text" value="{{$user -> address_ar}}"
                                                                   id="address_ar"
                                                                   class="form-control"

                                                                   name="address_ar">

                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput1"> العنوان بالانجليزي </label>
                                                            <input readonly type="text" value="{{$user -> address_en}}"
                                                                   id="address_en"
                                                                   class="form-control"

                                                                   name="address_en">
                                                            @error('address_en')
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput1"> رقم الهاتف </label>
                                                            <input readonly type="text" value="{{$user -> mobile}}"
                                                                   id="mobile"
                                                                   class="form-control"

                                                                   name="mobile">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput1"> البريد الالكتروني </label>
                                                            <input readonly type="text" value="{{$user -> email}}"
                                                                   id="email"
                                                                   class="form-control"

                                                                   name="email">

                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput2"> طول اللاعب بالسنتيميتر </label>
                                                            <input readonly type="number" min="1"
                                                                   value="{{$user -> tall}}"
                                                                   id="tall"
                                                                   class="form-control"

                                                                   name="tall">

                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput2"> وزن اللاعب بالكيلوجرام </label>
                                                            <input readonly type="number" min="1"
                                                                   value="{{$user -> weight}}"
                                                                   id="weight"
                                                                   class="form-control"

                                                                   name="weight">

                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput2"> الاكاديمية </label>
                                                            <input readonly type="text"
                                                                   value="{{$user -> academy -> name_ar}}"
                                                                   class="form-control"
                                                                   name="acadmy">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput2"> القسم </label>
                                                            <input readonly type="text"
                                                                   value="{{$user ->category -> name_ar}}"
                                                                   class="form-control"
                                                                   name="category">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="projectinput2"> الفرقه </label>
                                                            <input readonly type="text"
                                                                   value="{{$user -> team -> name_ar}}"
                                                                   class="form-control"
                                                                   name="team">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="projectinput2"> تاريخ الميلاد </label>
                                                            <input readonly type="text" name="birth_date"
                                                                   value="{{$user -> birth_date }}"
                                                                   class="form-control input-lg">
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group mt-1">
                                                            <input readonly type="checkbox" name="status"
                                                                   id="switcheryColor4"
                                                                   class="switchery" data-color="success"
                                                                   @if($user -> status == 1) checked @endif/>
                                                            <label for="switcheryColor4"
                                                                   class="card-title ml-1">الحالة </label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12 d-flex justify-content-center">
                                                    <div class="m-2">
                                                        <a href="{{route('admin.academy.subscriptions',['user_id' => $user->id,'type' => 'all'])}}"
                                                           type="button" class="btn btn-primary">
                                                            <i class="la la-check-square-o"></i> عرض و أضافه اشتراك
                                                            اكاديمية للطالب
                                                        </a>
                                                    </div>

                                                    <div class="m-2">
                                                        <a href="{{route('admin.users.rates',$user->id)}}" type="button"
                                                           class="btn btn-success">
                                                            <i class="la la-star"></i> سجل تقييمات الاعب
                                                        </a>
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

