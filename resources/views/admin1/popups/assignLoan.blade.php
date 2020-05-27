<div id="js--assign-loan-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <form id="js--assign-loan-form">
                <div class="modal-header">
                    <h4 class="modal-title">Assign</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('employee_id','User') !!}
                                {!!Form::select('employee_id',$employees,old('employee_id'),['id'=>"js--employee-current",'class'=>'form-control select2','placeholder'=>'Select Employee'])!!}
                                <span class="error" for="employee_id"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="ids">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button class="btn btn-primary" type="submit">Submit</button>
                </div>
            </form>
        </div>

    </div>
</div>