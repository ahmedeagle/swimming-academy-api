<!-- Modal -->
<div class="modal animated rotateInUpRight  text-left" id="rotateInUpRightHero{{$hero -> id}}" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel70" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-success" id="myModalLabel70">"   جائزة او نبذه عن الاعب  -  {{$hero -> user -> name_ar}}"</h4>
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
                    <input type="hidden" name="heroId" value="{{$hero -> id}}">
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="projectinput2">  النبذه بالعربي</label>
                                    <input class="form-control" name="note_ar" type="text">
                                    @error('note_ar')
                                    <span class="text-danger"> {{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="projectinput2"> النبذه بالانجليزي</label>
                                    <input class="form-control"  name="note_en"  type="text">
                                    @error('note_en')
                                    <span class="text-danger"> {{$message}}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="projectinput2"> صوره الطالب في المسابقة  </label>
                                    <input class="form-control"  name="hero_photo"  type="file">
                                    @error('hero_photo')
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
            </div>
        </div>
    </div>
</div>
