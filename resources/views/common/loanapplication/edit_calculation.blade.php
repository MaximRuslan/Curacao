<div id="editCalculationModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" id="editCalculationForm">
                <div class="modal-header">
                    <h4 class="modal-title mt-0">Payment Correction</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    {!!Form::label('date','Date')!!}
                                    {!!Form::text('date', old('date'), ['class'=>'form-control date-picker','placeholder'=>'Date'])!!}
                                    @if($errors->has('date'))
                                        <p class="help-block">{!!$errors->first('date')!!}</p>
                                    @endif
                                </div>
                            </div>
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
                                            <span class="error" for="payment_amount[{!! $key !!}]"></span>
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
                                            <span class="error" for="cashback_amount[{!! $key !!}]"></span>
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
                            <div class="col-md-12">
                                {!!Form::label('notes','Notes')!!}
                                {!!Form::textarea('notes', old('notes'), ['class'=>'form-control','placeholder'=>'Notes'])!!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group pull-right">
                            <input type="hidden" name="history_id" value="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
