@extends('admin.layouts.basic')

@section('title', "{$ticket -> title }")

@section('content')
    <div class="app-content content">
        <div class="sidebar-left sidebar-fixed">
            <div class="sidebar">
                <div class="sidebar-content card d-none d-lg-block">
                    <div id="users-list" class="list-group position-relative">
                        <div class="users-list-padding media-list">
                            <a href="#" class="media bg-blue-grey bg-lighten-5 border-right-info border-right-2">
                                <div class="media-left pr-1">
                                  <span class="avatar avatar-md avatar-online">
                                    <img class="media-object rounded-circle"
                                         src="{{$ticket -> ticketable -> photo}}"
                                         alt="Generic placeholder image">
                                    <i></i>
                                  </span>
                                </div>
                                <div class="media-body w-100">
                                    <h6 class="list-group-item-heading">{{$ticket -> ticketable -> name_ar}}
                                        <span class="font-small-3 float-right info"
                                              style="direction: ltr"> {{date("Y M d", strtotime($lastMessage -> created_at))}} </span>
                                    </h6>
                                    <p class="list-group-item-text text-muted mb-0"><i
                                            class="ft-check primary font-small-2"></i> {{$lastMessage -> message}}
                                    </p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-right">
            <div class="content-wrapper">
                <div class="content-header row">
                </div>
                <div class="content-body">
                    <section class="chat-app-window" id="chat-app-window">
                        <div class="chats">
                            <div class="chats">
                                <input type="hidden" name="ticket_id" id="ticket_id" value="{{$ticket -> id}}">
                                @if(isset($replies) && $replies -> count() > 0)
                                    @foreach($replies as $date => $repliesByDate)
                                        <p style="direction: ltr;"> {{ $date  }} </p>
                                        @foreach($repliesByDate as $reply)
                                            @if($reply -> FromUser == 1)
                                            <!-- user -->
                                                <div class="chat chat-left">
                                                    @if(checkForShowImage($reply -> id , $reply -> ticket_id))
                                                        <div class="chat-avatar">
                                                            <a class="avatar" data-toggle="tooltip" href="#"
                                                               data-placement="left"
                                                               title=""
                                                               data-original-title="">
                                                                <img style="height: 60px;"
                                                                    src="{{$ticket -> ticketable -> photo}}"
                                                                    alt="avatar"
                                                                />
                                                            </a>
                                                        </div>
                                                    @endif
                                                    <div class="chat-body">
                                                        <div class="chat-content">
                                                            <p> {{$reply -> message}}</p>
                                                            <span
                                                                class="time">{{date("h:i A", strtotime($reply -> created_at))}}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        <!-- admin -->
                                            @if($reply -> FromUser == 0)
                                                <div class="chat">
                                                    <div class="chat-avatar">
                                                        <a class="avatar" data-toggle="tooltip" href="#"
                                                           data-placement="right"
                                                           title=""
                                                           data-original-title="">
                                                            <img style="height: 60px;"
                                                                src="{{auth('admin')->user() -> photo}}"
                                                                alt="avatar"
                                                            />
                                                        </a>
                                                    </div>
                                                    <div class="chat-body">
                                                        <div class="chat-content">
                                                            <p> {{$reply -> message}}</p>
                                                            <i class="ft-check primary font-small-2"></i>
                                                            <span
                                                                class="time">{{date("h:i A", strtotime($reply -> created_at))}}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    @endforeach
                                @endif

                                <div class="adminMsg">
                                </div>
                            </div>
                        </div>
                    </section>
                    <section class="chat-app-form">
                        <form class="chat-app-input d-flex">
                            <fieldset class="form-group position-relative has-icon-left col-10 m-0">

                                <input type="text" id="replay_message" name="replay_message" class="form-control"
                                       placeholder="من فصلك أدخل الرسالة هنا ">
                                <div class="form-control-position control-position-right">
                                    <i class="ft-image"></i>
                                </div>
                                <span class="text-danger" id="errorMsg"></span>
                            </fieldset>
                            <fieldset class="form-group position-relative has-icon-left col-2 m-0">
                                <button id="sendReplayMessage" type="button" class="btn btn-info"><i
                                        class="la la-paper-plane-o d-lg-none"></i>
                                    <span class="d-none d-lg-block">أرسال</span>
                                </button>
                            </fieldset>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        /*keep scroll of chat window to botton always*/
        scrollToBottom();

        function scrollToBottom() {
            var chatWindow = document.getElementById("chat-app-window");
            chatWindow.scrollTop = chatWindow.scrollHeight;
        }


        $(document).on('click', '#sendReplayMessage', function (e) {
            e.preventDefault();
            $('#errorMsg').empty();
            $.ajax({
                type: 'post',
                url: "{{Route('admin.users.tickets.reply')}}",
                data: {
                    'replay_message': $('#replay_message').val(),
                    'ticket_id': $('#ticket_id').val(),
                },
                success: function (data) {
                    if (data.error) {
                        $('#errorMsg').empty().append(data.error);
                    }
                    if (data.content) {
                        $('#replay_message').val('');
                        $('.adminMsg').last().append(data.content);
                    }
                    scrollToBottom();
                }
            });
        });


    </script>
@stop

