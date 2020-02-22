<!-- Modal -->
<div class="modal animated rotateInUpRight  text-left" id="rotateInUpRightMore" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel70" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-success" id="myModalLabel70">التفاصيل</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-center"><span
                        class="text-info text-center"> أدخل نبذه محتصره عن دور الطالب ف البطوله او الجائزه التي حصل عليها   </span></p>

                <form class="form" action="{{route('admin.heroes.note')}}" method="Post"
                      enctype="multipart/form-data">
                    @csrf
                     <div class="form-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group" id="contentOfDetials" style="line-height: 2.3">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">أغلاق</button>
                     </div>
                </form>
            </div>
        </div>
    </div>
</div>
