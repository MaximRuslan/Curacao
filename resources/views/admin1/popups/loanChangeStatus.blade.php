<div id="loanStatusChangeModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <form id="loanStatusChangeForm">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <span id="status_desc"></span>
                        </div>
                    </div>
                    <div class="row currentEmployeeDiv mt-2">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::select('employee_id',$employees,old('employee_id'),['id'=>"js--employee-current",'class'=>'form-control select2','placeholder'=>'Select Employee'])!!}
                                <span class="error" for="employee_id"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row declineReasonDiv">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('reason','Reason')!!}
                                {!!Form::select('decline_reason',$declineReasons,old('reasons'),['id'=>"change_status_decline_reason",'class'=>'form-control select2','placeholder'=>'Reason'])!!}
                                <span class="error" for="decline_reason"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row holdReasonDiv">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('reason','Reason')!!}
                                {!!Form::select('hold_reason',$onHoldReasons,old('reasons'),['id'=>"change_status_hold_reason",'class'=>'form-control select2','placeholder'=>'Reason'])!!}
                                <span class="error" for="hold_reason"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row descriptionDiv">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Description <span id="description_required"></span></label>
                                {!!Form::textarea('description', old('description'), ['class'=>'form-control','placeholder'=>'Description'])!!}
                                <span class="error" for="description"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="description_required">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                    <button class="btn btn-primary" type="submit">Yes</button>
                </div>
            </form>
        </div>

    </div>
</div>