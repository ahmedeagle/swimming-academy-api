@extends('admin.layouts.basic')
@section('title')
     تعديل   طالب  جديد
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
                                <li class="breadcrumb-item"><a href="{{route('admin.users.all')}}"> الطلاب  </a>
                                </li>
                                <li class="breadcrumb-item active"> تعديل  طالب
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
                                    <h4 class="card-title" id="basic-layout-form"> تعديل  الطالب - {{$user -> name_ar}} </h4>
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
                                        <form class="form" action="{{route('admin.users.update',$user -> id)}}" method="POST"
                                              enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-body">

                                                <div class="form-group">
                                                    <div class="text-center">
                                                        <img
                                                            src="{{$user -> photo}}"
                                                            class="rounded-circle  height-150" alt="صوره  الطالب  ">
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label> صوره الطالب </label>
                                                    <label id="projectinput7" class="file center-block">
                                                        <input type="file" id="file" name="photo">
                                                        <span class="file-custom"></span>
                                                    </label>
                                                    @error('photo')
                                                    <span class="text-danger"> {{$message}}</span>
                                                    @enderror
                                                </div>


                                                <h4 class="form-section"><i class="ft-user"></i> بيانات  الطلب </h4>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput1"> الاسم بالعربي </label>
                                                            <input type="text" value="{{$user -> name_ar}}" id="name_ar"
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
                                                            <input type="text" value="{{$user -> name_en}}" id="name_en"
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
                                                            <label for="projectinput1">  العنوان بالعربي </label>
                                                            <input type="text" value="{{$user -> address_ar}}" id="address_ar"
                                                                   class="form-control"
                                                                   placeholder="ادخل  عنوانك  "
                                                                   name="address_ar">
                                                            @error('address_ar')
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput1">  العنوان  بالانجليزي </label>
                                                            <input type="text" value="{{$user -> address_en}}" id="address_en"
                                                                   class="form-control"
                                                                   placeholder="ادخل  عنوانك  باللغة  الانجليزية  "
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
                                                            <label for="projectinput1">  رقم الهاتف </label>
                                                            <input type="text" value="{{$user -> mobile}}" id="mobile"
                                                                   class="form-control"
                                                                   placeholder="ادخل   رقمك الهاتف  "
                                                                   name="mobile">
                                                            @error('mobile')
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput1">  البريد الالكتروني     </label>
                                                            <input type="text" value="{{$user -> email}}" id="email"
                                                                   class="form-control"
                                                                   placeholder="ادخل  البريد الالكتروني   "
                                                                   name="email">
                                                            @error('email')
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput1"> كلمة المرو </label>
                                                            <input type="password" id="password" class="form-control"
                                                                   placeholder=" ادحل كبمة المرور الجديده  "
                                                                   name="password">
                                                            @error('password')
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput3">تأكيد كلمة المرور </label>
                                                            <input type="password" id="password_cnformation"
                                                                   class="form-control"
                                                                   placeholder="تأكيد كلمة المرور  "
                                                                   name="password_confirmation">

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput2"> طول الطالب بالسنتيميتر  </label>
                                                            <input type="number" min="1" value="{{$user -> tall}}"
                                                                   id="tall"
                                                                   class="form-control"
                                                                   placeholder="  أدحل طول الطالب بالسنتيمتر  "
                                                                   name="tall">
                                                            @error('tall')
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput2">  وزن الطالب   بالكيلوجرام  </label>
                                                            <input type="number" min="1" value="{{$user -> weight}}"
                                                                   id="weight"
                                                                   class="form-control"
                                                                   placeholder="  أدحل وزن الطالب بالكيلوجرام "
                                                                   name="weight">
                                                            @error('weight')
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput2"> أختر الاكاديمية </label>
                                                            <select name="academy_id" class="select2 form-control">
                                                                <optgroup label="من فضلك أختر أكاديمية ">
                                                                    @if($academies && $academies -> count() > 0)
                                                                        @foreach($academies as $academy)
                                                                            <option
                                                                                value="{{$academy -> id }}" @if($academy -> id == $user -> academy_id) selected @endif>{{$academy -> name}}</option>
                                                                        @endforeach
                                                                    @endif
                                                                </optgroup>
                                                            </select>
                                                            @error('academy_id')
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput2">  فرقه الطالب
                                                            </label>
                                                            <select class="select2 form-control" name="team_id" >
                                                                @if($teams && $teams -> count() > 0)
                                                                    <optgroup label=" الفرق ">
                                                                    @foreach($teams as $team)

                                                                            <option value="{{$team->id}}" @if($team -> id == $user -> team_id) selected @endif >{{$team -> name_ar}}</option>

                                                                    @endforeach
                                                                    </optgroup>
                                                                @endif
                                                            </select>
                                                            @error('team_id')
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="projectinput2">  تاريخ الميلاد  </label>
                                                            <input type="text"  name="birth_date" value="{{$user -> birth_date }}" class="form-control input-lg" id="lang" placeholder="Date Dropper">
                                                            @error('birth_date')
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group mt-1">
                                                            <input type="checkbox" name="status"
                                                                   id="switcheryColor4"
                                                                   class="switchery" data-color="success"
                                                                   @if($user -> status == 1) checked @endif/>
                                                            <label for="switcheryColor4" class="card-title ml-1">الحالة </label>
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


@section('script')
@stop
