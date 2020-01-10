<!-- Modal -->
<div class="modal animated rotateInUpRight  text-left" id="rotateInUpRight" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel70" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-primary" id="myModalLabel70">{{$user -> name_ar}}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                 <p><span class="text-primary">الأسم</span> : {{$user -> name_ar}} </p>
                <p><span class="text-primary">العنوان</span> : {{$user -> address_ar}} </p>
                <hr>
                <p><span class="text-primary">الموبيل</span> : {{$user -> mobile}} </p>
                <p><span class="text-primary">البريد الالكتروني</span> : {{$user -> email}} </p>
                <hr>
                <p><span class="text-primary">الطول</span> : {{$user -> tall}} </p>
                <p><span class="text-primary">الوزن</span> : {{$user -> weight}} </p>
                <hr>
                <p><span class="text-primary"> تاريخ الميلاد </span> : {{$user -> birth_date}} </p>
                <p><span class="text-primary">الحاله</span> : {{$user -> status}} </p>
                <p><span class="text-primary">الفريق الحالي</span> : {{$user -> team -> name_ar}} </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">أغلاق</button>
            </div>
        </div>
    </div>
</div>
