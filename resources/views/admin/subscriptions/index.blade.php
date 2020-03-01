@extends('admin.layouts.basic')
@section('title')
    الاشتراكات
@stop
@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-12 mb-2">
                    <h3 class="content-header-title">   {{$text}} </h3>

                    <div class="row breadcrumbs-top">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">الرئيسية</a>
                                </li>
                                <li class="breadcrumb-item active"> الاشتراكات
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
                                    <h4 class="card-title">   {{$text}}

                                    </h4>
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
                                                <th> الاسم الاعب</th>
                                                <th> صورة الاعب</th>
                                                <th>الاكاديمية</th>
                                                <th> القسم</th>
                                                <th> الفرقة</th>
                                                <th> بدأ الاشتراك</th>
                                                <th> أنتهاء الاشتراك</th>
                                                <th> قيمه الاشتراك</th>


                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(isset($subscriptions) && $subscriptions -> count() > 0 )
                                                @foreach($subscriptions as $subscription)
                                                    <tr>
                                                        <td>{{$subscription -> user -> name_ar}}</td>
                                                        <td><img src="{{$subscription -> user -> photo}}"
                                                                 height="40px;"></td>
                                                        <td>{{$subscription -> user-> academy -> name_ar}}</td>
                                                        <td>{{$subscription ->user ->  category -> name_ar}}</td>
                                                        <td>{{$subscription ->user   -> team -> name_ar}}</td>
                                                        <td>   {{ __('messages.'.date('l',strtotime($subscription -> start_date)))}}
                                                            - {{ date('d-m-Y',strtotime($subscription -> start_date))}}  </td>
                                                        </td>
                                                        <td>   {{ __('messages.'.date('l',strtotime($subscription -> end_date)))}}
                                                            - {{ date('d-m-Y',strtotime($subscription -> end_date))}}  </td>
                                                        </td>
                                                        <td>{{$subscription ->price}}</td>
                                                       {{-- <td>
                                                            <div class="btn-group" role="group"
                                                                 aria-label="Basic example">

                                                                <div class="form-group mt-1">
                                                                    <input type="checkbox" name="status"
                                                                           data_id="{{$subscription -> id}}"
                                                                           class="subscribeStatus{{$subscription -> id}} changeSubscriptionStatus switchery"
                                                                           data-color="success"
                                                                           @if($subscription  -> status == 1)checked @endif
                                                                    />
                                                                    <label for="switcheryColor4"
                                                                           class="card-title ml-1">الحالة </label>
                                                                </div>
                                                            </div>
                                                        </td> --}}
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

        $(document).on('change', '.changeSubscriptionStatus', function (e) {
            e.preventDefault();
            let status = 0;
            let subscriptionId = $(this).attr('data_id')

            if ($('.subscribeStatus' + subscriptionId).prop('checked')) {
                status = 1;
            } else {
                status = 0;
            }

            $.ajax({
                type: 'post',
                url: "{{Route('admin.subscriptions.status')}}",
                data: {
                    'status': status,
                    'subscriptionId': $(this).attr('data_id')
                },
                success: function (data) {
                    if (data.status == 1) {
                        toastr.success(data.message)
                    }
                    if (data.status == 0) {
                        toastr.info(data.message)
                        setTimeout(function () {
                            location.reload();
                        }, 3000)
                    }
                }, error: function () {
                    toastr.error('هناك خطا برجاء المحاوله مجددا')
                    setTimeout(function () {
                        location.reload();
                    }, 3000)
                }
            });
        });
    </script>

@stop
