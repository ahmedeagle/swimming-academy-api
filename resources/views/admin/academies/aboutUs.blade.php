@extends('admin.layouts.basic')
@section('title')
     من نحن
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
                                 <li class="breadcrumb-item active"> من نحن
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
                                    <h4 class="card-title" id="basic-layout-form"> من نحن  </h4>
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
                                        <form class="form" action="{{route('admin.postaboutus')}}" method="POST"
                                              enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-body">
                                                <h4 class="form-section"><i class="ft-user"></i> البيانات   </h4>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput1"> العنوان بالعربي </label>
                                                            <input type="text" value="{{ @$settings -> title_ar }}" id="title_ar"
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
                                                            <input type="text" value="{{  @$settings -> title_en  }}" id="title_en"
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
                                                            <label for="projectinput1"> المحتوي بالعربي </label>
                                                            <textarea  id="ckeditor-language" name="content_ar" rows="10"
                                                                       name="content_ar">{{  @$settings -> content_ar  }}</textarea>
                                                            @error('content_ar')
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="projectinput1"> المحتوي بالانجليزي </label>
                                                            <textarea  id="ckeditor-language2" name="content_en" rows="10"
                                                                       name="content_en">{{@$settings -> content_en }}</textarea>
                                                            @error('content_en')
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


@section('script')
    <script>

        CKEDITOR.replace( 'ckeditor-language', {
            extraPlugins: 'language',
            // Customizing list of languages available in the Language drop-down.
            language_list: [ 'ar:Arabic:rtl', 'fr:French',  'he:Hebrew:rtl', 'es:Spanish' ],
            height: 350
        } );

        CKEDITOR.replace( 'ckeditor-language2', {
            extraPlugins: 'language',
            // Customizing list of languages available in the Language drop-down.
            language_list: [ 'ar:Arabic:rtl', 'fr:French',  'he:Hebrew:rtl', 'es:Spanish' ],
            height: 350
        } );

    </script>
@stop
