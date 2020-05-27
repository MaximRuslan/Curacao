<div id="cashpayoutWallet" class="modal fade" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <form id="cashpayoutWalletForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title mt-0 jq__title">Cash Payout</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="js__status">
                    <div class="row">
                        <div class="col-md-12 row">
                            <div class="col-md-6">
                                <h5>Payment Received</h5>
                                @foreach($payment_types as $key=>$type)
                                    <div class="form-group">
                                        <label class="control-label payment_type_name"
                                               data-id="{!! $key !!}">{!! $type !!}</label>
                                        <input type="text"
                                               name="payment_amount[{!! $key !!}]"
                                               class="form-control numeric-input payment_type_value_{!! $key !!}">
                                    </div>
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
                                        <p class="total_payment_amount_error error"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id">
                    <input type="hidden" name="status" value="3">
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-info waves-effect waves-light">Complete</button>
                </div>
            </div>
        </form>
    </div>
</div>

