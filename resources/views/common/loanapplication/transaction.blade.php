<div id="loanTransactionModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-full">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title mt-0">Loan transactions</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
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
                                        <div class="col-md-12">
                                            <form method="POST" id="transactionForm"
                                                  action="{{ route('save-transaction') }}"
                                                  onsubmit="return SaveTransaction(this)">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Transaction type *</label>
                                                            <select name="transaction_type" id="loan_transaction_type"
                                                                    class="form-control">
                                                                @foreach($transactiontypes as $type)
                                                                    <option value="{{$type->id}}">{{$type->title}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Transaction Payment
                                                                Date *</label>
                                                            <input value="{!! date('d/m/Y') !!}" type="text"
                                                                   class="old-date-picker form-control"
                                                                   name="payment_date">
                                                            <span class="error" for="payment_date"></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Next Payment Date *</label>
                                                            <input type="text" class="date-picker form-control"
                                                                   name="next_payment_date">
                                                            <span class="error" for="next_payment_date"></span>
                                                        </div>
                                                    </div>
                                                    @if(auth()->user()->hasRole('super admin'))
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                {!!Form::label('branch_id','Branch')!!}
                                                                {!!Form::select('branch_id',[],old('branch_id'),['class'=>'form-control','placeholder'=>'Branch','id'=>'branch_selection'])!!}
                                                                <span class="error" for="branch_id"></span>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <h5>Payment Received</h5>
                                                                @foreach($payment_types as $key=>$type)
                                                                    @if($key!=2)
                                                                        <div class="form-group">
                                                                            <label class="control-label payment_type_name"
                                                                                   data-id="{!! $key !!}">{!! $type !!}</label>
                                                                            <input type="text"
                                                                                   name="payment_amount[{!! $key !!}]"
                                                                                   class="form-control numeric-input payment_type_value_{!! $key !!}">
                                                                        </div>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div>
                                                                    <h5>Cash Back</h5>
                                                                    @foreach($cash_back_payment_types as $key=>$type)
                                                                        <div class="form-group">
                                                                            <label class="control-label">{!! $type !!}</label>
                                                                            <input type="text"
                                                                                   name="cashback_amount[{!! $key !!}]"
                                                                                   class="form-control numeric-input">
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                                <div class="mt-5">
                                                                    <h5>Transaction Total</h5>
                                                                    <div class="form-group">
                                                                        <label class="control-label">Total
                                                                            Received</label>
                                                                        <input type="text"
                                                                               name="transaction_total[received]"
                                                                               class="form-control numeric-input">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="control-label">Total
                                                                            Cash Back</label>
                                                                        <input type="text"
                                                                               name="transaction_total[cash_back]"
                                                                               class="form-control numeric-input">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="control-label">Payment
                                                                            Total</label>
                                                                        <input type="text"
                                                                               name="transaction_total[payment]"
                                                                               class="form-control numeric-input">
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
                                                            <button class="btn btn-primary">Save</button>
                                                            <button class="btn btn-danger write_off_loan_application"
                                                                    data-id="">Write Off
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
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
                                            <label class="control-label">Principal Balance</label>
                                            <input type="text"
                                                   name="principal_balance"
                                                   class="form-control numeric-input" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Renewal Fee Balance</label>
                                            <input type="text"
                                                   name="renewal_balance"
                                                   class="form-control numeric-input" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Interest Fee Balance</label>
                                            <input type="text"
                                                   name="interest_balance"
                                                   class="form-control numeric-input" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Renewal & Interest Fee Tax</label>
                                            <input type="text"
                                                   name="renewal_interest_tax"
                                                   class="form-control numeric-input" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Admin Fee Balance</label>
                                            <input type="text"
                                                   name="debt_balance"
                                                   class="form-control numeric-input" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Admin Fee Tax</label>
                                            <input type="text"
                                                   name="debt_tax"
                                                   class="form-control numeric-input" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Total Balance</label>
                                            <input type="text"
                                                   name="total_balance"
                                                   class="form-control numeric-input" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="portlet-body">
                            <div class="row">
                                <div id="bg-primary" class="panel-collapse collapse show">
                                    <div class="mt-4">
                                        <table id="loan-transaction-table"
                                               class="table table-striped table-bordered"
                                               style="width:100%">
                                            <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>User</th>
                                                <th>User</th>
                                                <th>Type</th>
                                                <th>Payment Type</th>
                                                <th>Notes</th>
                                                <th>Amount</th>
                                                <th>Cashback Amount</th>
                                                <th>Payment Date</th>
                                                {{--<th>Action</th>--}}
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
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
