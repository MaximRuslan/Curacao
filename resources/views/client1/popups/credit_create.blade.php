<div id="creditModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
     style="display: none;" data-dismiss="modal">
    <div class="modal-dialog">
        <form id="credit_form">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title mt-0">@lang('keywords.Credits')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        {{--<div class="col-md-12">
                            <div class="form-group" id='wallet'>
                            </div>
                            <div class="form-group" id='available_wallet'>
                            </div>
                        </div>--}}
                        <div class="col-md-12">
                            <div class="form-group" id='walletDateTime'>
                            </div>
                        </div>
                        <input type="hidden" name="payment_type" value="">
                        <?php
                        $walletValue = isset($wallet) ? $wallet : 0;
                        ?>
                        <input type="hidden" name="wallet" id="walletVal" value="{!! $walletValue !!}">
                        @isset($wallet)
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <div class="col-md-6 my-wallet">
                                        <label for="">{!!Form::hidden('wallet',$wallet)!!}
                                            {!!Form::label('wallet',__('keywords.Ledger').' '.__('keywords.Balance'))!!}
                                        </label>: <span class="total_amount">{!! $wallet !!}</span>
                                    </div>
                                    <div class="col-md-6 my-wallet">
                                        <label for="">{!!Form::hidden('available_wallet',$available_balance)!!}
                                            {!!Form::label('available_wallet',__('keywords.Available') .' '. __('keywords.Balance'))!!}
                                        </label>: <span class="available_amount">{!! $available_balance !!}</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('amount',__('keywords.Amount').' *')!!}
                                {!!Form::text('amount', old('amount'), ['class'=>'form-control numeric-input','placeholder'=>__('keywords.Amount'),'id'=>'credit_amount','step'=>0.01])!!}
                                <div class="error">
                                    <span class="help-block" for="amount"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 bank_div" style="display: none;">
                            <div class="form-group">
                                {!!Form::label('bank_id',__('keywords.Bank').' *')!!}
                                <select name="bank_id" id="bank_id" class="form-control">
                                    <option value="">@lang('keywords.Bank')</option>
                                    @foreach($banks as $key=>$value)
                                        <option value="{!! $value->id !!}"
                                                data-transaction-type="{!! $value->transaction_fee_type !!}"
                                                data-transaction-amount="{!! $value->transaction_fee+$value->tax_transaction !!}">
                                            {!! $value->name." - ".$value->account_number !!}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="error">
                                    <span class="help-block" for="bank_id"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 bank_div" style="display: none;">
                            <div class="form-group">
                                {!!Form::label('transaction_charge',__('keywords.TransactionCharge'))!!}
                                {!!Form::text('transaction_charge', old('transaction_charge'), ['class'=>'form-control','placeholder'=>__('keywords.TransactionCharge'),'id'=>'transaction_charge','readonly'])!!}
                            </div>
                            <div class="error">
                                <span class="help-block" for="transaction_charge"></span>
                            </div>
                        </div>
                        <div class="col-md-10 branch_div" style="display: none;">
                            <div class="form-group">
                                {!!Form::label('branch_id',__('keywords.Branch').' *')!!}
                                {!!Form::select('branch_id',$branches,old('branch_id'),['class'=>'form-control','placeholder'=>__('keywords.Branch'),'id'=>'branch_id'])!!}
                                <div class="error">
                                    <span class="help-block" for="bank_id"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 branch_div" style="display:none;">
                            <div class="form-group text-center">
                                @if(isset($country) && $country!=null && $country->map_link!='')
                                    <a href="{!! $country->map_link !!}" class="btn action-button mt-4"
                                       target="_blank">
                                        <i class="material-icons">my_location</i>
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('notes',__('keywords.Notes'))!!}
                                {!!Form::textarea('notes', old('notes'), ['class'=>'form-control','placeholder'=>__('keywords.Notes'),'maxlength'=>1000])!!}
                            </div>
                            <div class="error">
                                <span class="help-block" for="notes"></span>
                            </div>
                        </div>
                        <div class="col-md-12 terms_condition">
                            <label class="cs-checkbox">
                                <input type="checkbox" name="terms" value="1" id="loan_model_terms_checkbox"
                                       required> @lang('keywords.IAgreewith')
                                <a href="{!! url('terms-conditions') !!}"
                                   target="_blank"> @lang('keywords.TermsAndConditions')</a>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id">

                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">
                        @lang('keywords.Close')
                    </button>
                    <button type="submit" class="btn btn-info waves-effect waves-light creditCreateButton" disabled>
                        @lang('keywords.Apply')
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
