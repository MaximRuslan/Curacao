<div id="NLBReasonsModel" class="modal fade" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <form action="{{route('nlb-reasons.store')}}" id="reason_form" onsubmit="return SaveNLBReason(this)">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title mt-0">NLB Reasons</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('type','Type')!!}
                                {!!Form::select('type',['1'=>"IN",'2'=>"OUT"],old('type'),['class'=>'form-control select2','placeholder'=>'Type','id'=>'type','required'])!!}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('title','Title')!!}
                                {!!Form::text('title', old('title'), ['class'=>'form-control','placeholder'=>'Title','required'])!!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id">
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-info waves-effect waves-light">Save changes</button>
                </div>
            </div>
        </form>
    </div>
</div>