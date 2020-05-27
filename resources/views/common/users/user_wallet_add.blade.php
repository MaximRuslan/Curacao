<div id="userWallerModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg" style="min-width: 1360px !important;">
        <div class="modal-content">
            <form id="userWalletForm" action="#">
                <div class="modal-header">
                    <h4 class="modal-title mt-0">User Wallet</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <!-- Wizard with Validation -->
                    <div class="row">
                        <div class="col-md-12 row">
                            <div class="col-md-4 portlet">
                                <div class="portlet-heading bg-custom">
                                    <h4 class="portlet-title">Client Name</h4>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="portlet-body">
                                    <span id="client_name"></span>
                                </div>
                            </div>
                            <div class="col-md-4 portlet">
                                <div class="portlet-heading bg-custom">
                                    <h4 class="portlet-title">Client Id</h4>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="portlet-body">
                                    <span id="client_id"></span>
                                </div>
                            </div>
                            <div class="col-md-4 portlet">
                                <div class="portlet-heading bg-custom">
                                    <h4 class="portlet-title">Balance</h4>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="portlet-body">
                                    <span id="balance"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-5">
                        <div class="col-sm-12">
                            <div class="card-box">
                                <div>
                                    <div class="row">
                                        <div class="col-md-12 row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Transaction Payment
                                                        Date *</label>
                                                    <input value="{!! date('d/m/Y') !!}" type="text"
                                                           class="old-date-picker form-control"
                                                           name="transaction_payment_date">
                                                    <span class="error" for="transaction_payment_date"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 row">
                                            <h4>Payment</h4>
                                            @foreach(config('site.payment_types') as $key => $value)
                                                <div class="col-md-12 mt-3">
                                                    {!!Form::label('amount['.$key.']',$value)!!}
                                                    {!!Form::number('amount['.$key.']', old('amount['.$key.']'), ['class'=>'form-control','placeholder'=>$value])!!}
                                                    <div class="error">
                                                        <span class="help-block" for="cheque_amount"></span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="col-md-6">
                                            <h4>Cashback</h4>
                                            @foreach($cash_back_payment_types as $key=>$type)
                                                <div class="col-md-12 mt-3">
                                                    <div class="form-group">
                                                        <label class="control-label">{!! $type !!}</label>
                                                        <input type="text"
                                                               name="cashback_amount[{!! $key !!}]"
                                                               class="form-control numeric-input">
                                                    </div>
                                                </div>
                                            @endforeach
                                            <div class="mt-5">
                                                <h4>Transaction Total</h4>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="control-label">Total Received</label>
                                                        <input type="text" name="transaction_total[received]"
                                                               class="form-control numeric-input">
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label">Total Cash Back</label>
                                                        <input type="text" name="transaction_total[cash_back]"
                                                               class="form-control numeric-input">
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label">Payment Total</label>
                                                        <input type="text" name="transaction_total[payment]"
                                                               class="form-control numeric-input">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mt-3">
                                            {!!Form::label('notes','Notes')!!}
                                            {!!Form::textarea('notes', old('notes'), ['class'=>'form-control','row'=>'3','placeholder'=>'Notes'])!!}
                                        </div>
                                        <div class="col-md-12 mt-3 text-right">
                                            <button type="submit" class="btn btn-primary waves-effect">Submit
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <table id="wallet-table" class="table  table-striped table-bordered" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Note</th>
                                    <th>Transaction Payment Date</th>
                                    <th>Date</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <!-- End row -->
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id">
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
