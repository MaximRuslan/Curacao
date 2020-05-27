<div id="typeModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-dismiss="modal"
style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title mt-0">Type</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!!Form::label('type','Type')!!}
                          {!!Form::select('type',['1'=>'Cash payout','2'=>'Deposit on bank account','3'=>'Payment merchant'],'',['class'=>'form-control','placeholder'=>'Type','id'=>'type','required'])!!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-info waves-effect waves-light submit_type">Save changes</button>
            </div>
        </div>
    </div>
</div>
