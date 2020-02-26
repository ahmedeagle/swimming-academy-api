<!-- Modal -->
<div class="modal animated rotateInUpLeft  text-left" id="rotateInUpLeft{{$ticket -> id}}" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel70" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-primary" id="myModalLabel70">{{$ticket -> title}}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                 <p><span class="text-primary"> رقم التذكرة </span> : {{$ticket ->message_no }} </p>
                 <p><span class="text-primary"> غنوان التذكرة   </span> : {{$ticket ->title }} </p>
                <hr>
                <p><span class="text-primary"> الراسل</span> : {{$ticket -> ticketable -> name_ar}} </p>
                <p><span class="text-primary">النوع </span> : {{$ticket -> type}} </p>
                <p><span class="text-primary">الاهمية  </span> : {{$ticket -> importance}} </p>
                <hr>
                <p><span class="text-primary">تاريخ الانشاء </span> : {{$ticket -> created_at}} </p>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">أغلاق</button>
            </div>
        </div>
    </div>
</div>
