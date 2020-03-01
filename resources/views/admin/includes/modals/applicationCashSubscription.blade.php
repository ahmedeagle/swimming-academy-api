<!-- Modal -->
<div class="modal animated rotateInUpRight  text-left" id="rotateInUpRightSubscription{{$user -> id}}" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel70" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-success" id="myModalLabel70">"  أضافة اشتراك تطبيق نقدي للاعب {{$user -> name_ar}}"</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                @if(!$user ->  subscribed )
                <p class="text-center"><span
                        class="text-info text-center"> يتم احتساب 30 يوما لمدة الاشتراك تبدأ بتاريخ اليوم  </span></p>

                <p>يبدا في :   <span class="text-info text-center">{{date('Y-m-d')}}</span></p>
                و
                <p> ينتهي في :   <span class="text-info text-center">{{date('Y-m-d',strtotime(today() -> addDays(29)))}}</span></p>


                <form class="form" action="{{route('admin.subscriptions.storeCash')}}" method="post"
                      enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="userId" value="{{$user -> id}}">
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="projectinput2"> قيمه الاشتراك </label>
                                    <input class="form-control" name="price" min="0" type="number">
                                    @error('price')
                                    <span class="text-danger"> {{$message}}</span>
                                    @enderror
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">أغلاق</button>
                        <button  type="submit" class="btn grey btn-outline-success" id="yes"> حفظ</button>
                    </div>

                </form>

                 @else
                    <br>
                    <span class="text-danger center">  عفوا  لا يمكن اضافه اشتراك يوجد اشتراك تطبيق حالي لهذا الاعب</span>
                    <br>
                @endif
            </div>

        </div>
    </div>
</div>
