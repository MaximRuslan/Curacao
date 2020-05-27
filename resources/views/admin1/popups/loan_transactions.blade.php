<div id="loanTransactionModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-full">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title mt-0">Loan transactions</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 js--add-form">
                        <div class="portlet">
                            <div class="portlet-heading bg-custom">
                                <h3 class="portlet-title">
                                    Add new
                                </h3>
                                <div class="clearfix"></div>
                            </div>
                            <div id="bg-primary" class="panel-collapse collapse show">
                                <div class="portlet-body">
                                    <div class="row">
                                        <form id="transactionForm">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        {!!Form::label('transaction_type','Transaction Type *')!!}
                                                        {!!Form::select('transaction_type',$transaction_types,old('transaction_type'),['class'=>'form-control select2','id'=>'loan_transaction_type'])!!}
                                                        <span class="error" for="transaction_type"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        {!!Form::label('payment_date','Transaction Payment Date *')!!}
                                                        {!!Form::text('payment_date', \App\Library\Helper::date_to_sheet_timezone(date('Y-m-d H:i:s')), ['class'=>'form-control old-date-picker','placeholder'=>'Transaction Payment Date'])!!}
                                                        <span class="error" for="payment_date"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        {!!Form::label('next_payment_date','Next Payment Date *')!!}
                                                        {!!Form::text('next_payment_date', old('next_payment_date'), ['class'=>'form-control date-picker','placeholder'=>'Next Payment Date'])!!}
                                                        <span class="error" for="next_payment_date"></span>
                                                    </div>
                                                </div>
                                                @if(auth()->user()->hasRole('super admin|admin'))
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            {!!Form::label('branch_id','Branch *')!!}
                                                            {!!Form::select('branch_id',[],old('branch_id'),['class'=>'form-control select2','placeholder'=>'Branch','id'=>'branch_selection'])!!}
                                                            <span class="error" for="branch_id"></span>
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h5>Payment Received</h5>
                                                            @foreach($payment_types as $key=>$type)
                                                                <div class="form-group">
                                                                    {!!Form::label('payment_amount['.$key.']',$type)!!}
                                                                    {!!Form::number('payment_amount['.$key.']', old('payment_amount['.$key.']'), ['step'=>'0.01','class'=>'form-control','placeholder'=>$type])!!}
                                                                    <span class="error" for="payment_amount[{!! $key !!}]"></span>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div>
                                                                <h5>Cash Back</h5>
                                                                @foreach($cash_back_payment_types as $key=>$type)
                                                                    <div class="form-group">
                                                                        {!!Form::label('cashback_amount['.$key.']',$type)!!}
                                                                        {!!Form::number('cashback_amount['.$key.']', old('cashback_amount['.$key.']'), ['step'=>'0.01','class'=>'form-control','placeholder'=>$type])!!}
                                                                        <span class="error" for="cashback_amount[{!! $key !!}]"></span>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <div class="mt-5">
                                                                <h5>Transaction Total</h5>
                                                                <div class="form-group">
                                                                    {!!Form::label('transaction_total[received]','Total Received')!!}
                                                                    {!!Form::number('transaction_total[received]', old('transaction_total[received]'), ['class'=>'form-control','placeholder'=>'Total Received'])!!}
                                                                </div>
                                                                <div class="form-group">
                                                                    {!!Form::label('transaction_total[cash_back]','Total Cash Back')!!}
                                                                    {!!Form::number('transaction_total[cash_back]', old('transaction_total[cash_back]'), ['class'=>'form-control','placeholder'=>'Total Cash Back'])!!}
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="transaction_total[payment]"><b>Payment Total</b></label>
                                                                    {!!Form::number('transaction_total[payment]', old('transaction_total[payment]'), ['class'=>'form-control','placeholder'=>'Payment Total','readonly','style'=>'font-weight:600;'])!!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @if(auth()->user()->hasRole('super admin'))
                                                    <div class="col-md-12">
                                                        {!!Form::label('notes','Notes')!!}
                                                        {!!Form::textarea('notes', old('notes'), ['class'=>'form-control','placeholder'=>'Notes'])!!}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <div class="form-group pull-right">
                                                        <input type="hidden" name="loan_id" value="0">

                                                        <input type="hidden" name="write_off" value="0">

                                                        @if(env('RECEIPT_ON')==true)
                                                            <label><input type="checkbox" name="receipt" value="1" checked> Download Receipt</label>
                                                        @endif
                                                        <button class="btn btn-primary js--submit-button" type="submit">Save</button>
                                                        @if(auth()->user()->hasRole('super admin|admin'))
                                                            <button class="btn btn-danger write_off_loan_application"
                                                                    data-id="">
                                                                Write Off
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 js--add-form">
                        <div class="portlet">
                            <div class="portlet-heading bg-custom">
                                <h3 class="portlet-title">
                                    Loan Balance
                                </h3>
                                <div class="clearfix"></div>
                            </div>
                            <div class="portlet-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            {!!Form::label('principal_balance','Principal balance')!!}
                                            {!!Form::text('principal_balance', old('principal_balance'), ['class'=>'form-control','placeholder'=>'Principal balance','readonly'])!!}
                                        </div>
                                        <div class="form-group">
                                            {!!Form::label('renewal_balance','Renewal Fee Balance')!!}
                                            {!!Form::text('renewal_balance', old('renewal_balance'), ['class'=>'form-control','placeholder'=>'Renewal Fee Balance','readonly'])!!}
                                        </div>
                                        <div class="form-group">
                                            {!!Form::label('interest_balance','Interest Fee Balance')!!}
                                            {!!Form::text('interest_balance', old('interest_balance'), ['class'=>'form-control','placeholder'=>'Interest Fee Balance','readonly'])!!}
                                        </div>
                                        <div class="form-group">
                                            {!!Form::label('renewal_interest_tax','Renewal & Interest Fee Tax')!!}
                                            {!!Form::text('renewal_interest_tax', old('renewal_interest_tax'), ['class'=>'form-control','placeholder'=>'Renewal & Interest Fee Tax','readonly'])!!}
                                        </div>
                                        <div class="form-group">
                                            {!!Form::label('debt_balance','Admin Fee Balance')!!}
                                            {!!Form::text('debt_balance', old('debt_balance'), ['class'=>'form-control','placeholder'=>'Admin Fee Balance','readonly'])!!}
                                        </div>
                                        <div class="form-group">
                                            {!!Form::label('debt_tax','Admin Fee Tax')!!}
                                            {!!Form::text('debt_tax', old('debt_tax'), ['class'=>'form-control','placeholder'=>'Admin Fee Tax','readonly'])!!}
                                        </div>
                                        <div class="form-group">
                                            {!!Form::label('total_balance','Total Balance')!!}
                                            {!!Form::text('total_balance', old('total_balance'), ['class'=>'form-control','placeholder'=>'Total Balance','readonly'])!!}
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="portlet-body">
                            <div class="row">
                                <table id="loan-transaction-table"
                                       class="table table-striped table-bordered" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>User</th>
                                        <th>Type</th>
                                        <th>Payment Type</th>
                                        <th>Notes</th>
                                        <th>Amount</th>
                                        <th>Cash back Amount</th>
                                        <th>Payment Date</th>
                                        <th>Log Date</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
