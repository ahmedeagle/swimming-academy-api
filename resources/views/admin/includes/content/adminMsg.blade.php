@section('main')
    <div class="chat adminMsg">
        <div class="chat-avatar">
            <a class="avatar" data-toggle="tooltip" href="#"
               data-placement="right"
               title=""
               data-original-title="">
                <img
                    src="{{auth('admin')->user() -> photo}}"
                    alt="avatar"
                />
            </a>
        </div>
        <div class="chat-body">
            <div class="chat-content">
                <p> {{@$newMessage -> message}}</p>
                <i class="ft-check primary font-small-2"></i>
                <span
                    class="time">{{@date("h:i A", strtotime($newMessage -> created_at))}}</span>
            </div>
        </div>
    </div>
@stop
