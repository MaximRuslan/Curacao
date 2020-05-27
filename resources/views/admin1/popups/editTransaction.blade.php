<div id="editCalculationModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg" style="max-width: 1000px;">
        <div class="modal-content">
            <form method="POST" id="editCalculationForm">
                <div class="modal-header">
                    <h4 class="modal-title mt-0">Correction</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-danger" id="common_error" style="display: none;">
                                </div>
                            </div>
                        </div>
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
                            <div class="col-md-12">
                                <h5>Payment Received</h5>
                                <div class="row">
                                    @foreach($payment_types as $key=>$type)
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label payment_type_name" data-id="{!! $key !!}">{!! $type !!}</label>
                                                <input step="0.01" type="number" name="payment_amount[{!! $key !!}]"
                                                       class="form-control numeric-input payment_type_value_{!! $key !!}">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-md-12">
                                <h5>Cash Back</h5>
                                <div class="row">
                                    @foreach($cash_back_payment_types as $key=>$type)
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label">{!! $type !!}</label>
                                                <input step="0.01" type="number" name="cashback_amount[{!! $key !!}]" class="form-control numeric-input">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-md-12">
                                <h5>Transaction Total</h5>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Total Received</label>
                                            <input type="text" name="transaction_total[received]" class="form-control numeric-input">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Total Cash Back</label>
                                            <input type="text" name="transaction_total[cash_back]" class="form-control numeric-input">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label"><b>Payment Total</b></label>
                                            <input type="text" name="transaction_total[payment]" class="form-control numeric-input" readonly style="font-weight: 600;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                {!!Form::label('notes','Notes')!!}
                                {!!Form::textarea('notes', old('notes'), ['class'=>'form-control','placeholder'=>'Notes'])!!}
                            </div>

                            {{--<div class="col-md-12 mt-2">
                                <h5>Transaction</h5>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Principal</label>
                                            <input step="0.01" type="number" name="transaction[principal]" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Interest</label>
                                            <input step="0.01" type="number" name="transaction[interest]" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Interest Tax</label>
                                            <input step="0.01" type="number" name="transaction[tax_for_interest]" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Renewal</label>
                                            <input step="0.01" type="number" name="transaction[renewal]" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Renewal Tax</label>
                                            <input step="0.01" type="number" name="transaction[tax_for_renewal]" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Tax</label>
                                            <input step="0.01" type="number" name="transaction[tax]" class="form-control" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Admin Fee</label>
                                            <input step="0.01" type="number" name="transaction[debt]" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Admin Fee Tax</label>
                                            <input step="0.01" type="number" name="transaction[debt_tax]" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Debt Collection</label>
                                            <input step="0.01" type="number" name="transaction[debt_collection_value]" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Debt Collection Tax</label>
                                            <input step="0.01" type="number" name="transaction[debt_collection_tax]" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Total</label>
                                            <input step="0.01" type="number" name="transaction[total]" class="form-control" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>--}}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group pull-right">
                            <input type="hidden" name="history_id" value="0">
                            <input type="hidden" name="open_balance" value="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary js--submit-button">Save</button>
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
