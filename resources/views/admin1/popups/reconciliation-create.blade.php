<div id="js--reconciliation-modal" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <form id="js--reconciliation-form">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title mt-0">Reconciliation</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('merchant_id','Merchant') !!}
                                {!! Form::select('merchant_id',$merchants,old('merchant_id'),['class'=>'form-control select2Single','placeholder'=>'Select Merchant']) !!}
                                <span class="error" for="merchant_id"></span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('branch_id','Branch') !!}
                                {!! Form::select('branch_id',[],old('branch_id'),['class'=>'form-control select2Single','placeholder'=>'Select Branch']) !!}
                                <span class="error" for="branch_id"></span>
                            </div>
                        </div>
                        <div class="col-md-12 js--account-payable-div" style="display: none;">
                            <div class="row">
                                <div class="col-md-8">
                                    Account Payable: <span id="js--account-payable"></span>
                                </div>
                                {{--<div class="col-md-4">--}}
                                    {{--<button class="btn btn-primary js--update-account-payable">Update</button>--}}
                                {{--</div>--}}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('amount','Amount') !!}
                                {!! Form::number('amount',old('amount'),['class'=>'form-control','placeholder'=>'Amount']) !!}
                                <span class="error" for="amount"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id">
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-info waves-effect waves-light">Create Request</button>
                </div>
            </div>
        </form>
    </div>
</div>
