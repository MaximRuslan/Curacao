<div id="creditModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
     style="display: none;">
    <div class="modal-dialog">
        <form id="credit_form">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title mt-0">Credits</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('user_id','User *')!!}
                                {!!Form::select('user_id',$users,old('user_id'),['class'=>'form-control','placeholder'=>'User','id'=>'users_select'])!!}
                                <span class="error" for="user_id"></span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group" id='wallet'>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group" id='available_wallet'>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group" id='walletDateTime'>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('payment_type','Payment Type *')!!}
                                {!!Form::select('payment_type',config('site.credit_payment_types'),old('payment_type'),['class'=>'form-control','placeholder'=>'Payment Type','id'=>'payment_type_select'])!!}
                                <span class="error" for="payment_type"></span>
                            </div>
                        </div>
                        <input type="hidden" name="wallet" id="walletVal">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('amount','Amount *')!!}
                                {!!Form::text('amount', old('amount'), ['class'=>'form-control numeric-input','placeholder'=>'Amount','id'=>'credit_amount','step'=>0.01])!!}
                                <span class="error" for="amount"></span>
                            </div>
                        </div>
                        <div class="col-md-6 bank_div" style="display: none;">
                            <div class="form-group">
                                {!!Form::label('bank_id','Bank *')!!}
                                {!!Form::select('bank_id',[],'',['class'=>'form-control','placeholder'=> 'Bank','id'=>'bank_id'])!!}
                                <span class="error" for="bank_id"></span>
                            </div>
                        </div>
                        <div class="col-md-6 bank_div" style="display: none;">
                            <div class="form-group">
                                {!!Form::label('transaction_charge',"Transaction Charge")!!}
                                {!!Form::text('transaction_charge', old('transaction_charge'), ['class'=>'form-control','placeholder'=>'Transaction Charge','id'=>'transaction_charge'])!!}
                                <span class="error" for="transaction_charge"></span>
                            </div>
                        </div>
                        <div class="col-md-12 branch_div" style="display: none;">
                            <div class="form-group">
                                {!!Form::label('branch_id','Branch *')!!}
                                @if(auth()->user()->role_id==9)
                                    {!!Form::select('branch_id',$branches,'',['class'=>'form-control','placeholder'=> 'Select branch','id'=>'branch_selection_id'])!!}
                                @else
                                    {!!Form::select('branch_id',[],'',['class'=>'form-control','placeholder'=> 'Select Branch','id'=>'branch_selection_id'])!!}
                                @endif
                                <span class="error" for="bank_id"></span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('notes','Notes')!!}
                                {!!Form::textarea('notes', old('notes'), ['class'=>'form-control','placeholder'=>'Notes','maxlength'=>1000])!!}
                            </div>
                            <div class="error">
                                <span class="help-block" for="notes"></span>
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
