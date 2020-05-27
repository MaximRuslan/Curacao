<div id="walletModel" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <form id="walletModelForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title mt-0">Add Payment</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('user_id','User *')!!}
                                {!!Form::select('user_id',$users,old('user_id'),['class'=>'form-control select2single','placeholder'=>"Select User",'id'=>'user_select'])!!}
                                <span class="error" for="user_id"></span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('amount','Amount *')!!}
                                {!!Form::number('amount', old('amount'), ['class'=>'form-control','min'=>'0.01','step'=>'0.01','placeholder'=>'Amount'])!!}
                                <span class="error" for="amount"></span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('note','Note')!!}
                                {!!Form::textarea('note', old('note'), ['class'=>'form-control','placeholder'=>'Note','maxlength'=>160])!!}
                                <span class="error" for="note"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-info waves-effect waves-light">Send</button>
                </div>
            </div>
        </form>
    </div>
</div>