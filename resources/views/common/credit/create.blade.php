<div id="creditModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
     data-dismiss="modal"
     style="display: none;">
    <div class="modal-dialog">
        <form
                @if(auth()->user()->role_id==3)
                action="{{route('client.credits.store')}}"
                @else
                action="{{route('credits.store')}}"
                @endif
                id="credit_form" onsubmit="return SaveCredit(this)">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title mt-0">@lang('keywords.Credits')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        @hasanyrole('admin|super admin|processor|credit and processing')
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('user_id','User *')!!}
                                {!!Form::select('user_id',$users,old('user_id'),['class'=>'form-control','placeholder'=>'User','id'=>'users_select'])!!}
                            </div>
                            <div class="error">
                                <span class="help-block" for="user_id"></span>
                            </div>
                        </div>
                        @endhasanyrole

                        @hasanyrole('admin|super admin|client|processor|credit and processing')
                        <div class="col-md-12">
                            <div class="form-group" id='wallet'>
                            </div>
                            <div class="form-group" id='available_wallet'>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group" id='walletDateTime'>
                            </div>
                        </div>
                        @if(auth()->user()->hasRole('client'))
                            <input type="hidden" name="payment_type" value="">
                        @else
                            <div class="col-md-12">
                                <div class="form-group">
                                    {!!Form::label('payment_type','Payment Type *')!!}
                                    {!!Form::select('payment_type',config('site.credit_payment_types'),old('payment_type'),['class'=>'form-control','placeholder'=>'Payment Type','id'=>'payment_type_select'])!!}
                                </div>
                                <div class="error">
                                    <span class="help-block" for="payment_type"></span>
                                </div>
                            </div>
                        @endif
                        <?php
                        $walletValue = isset($wallet) ? $wallet : 0;
                        ?>
                        <input type="hidden" name="wallet" id="walletVal" value="{!! $walletValue !!}">
                        @isset($wallet)
                            <div class="row col-md-12">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        {!!Form::hidden('wallet',$wallet)!!}
                                        {!!Form::label('wallet',__('keywords.Ledger').' '.__('keywords.Balance'))!!}
                                        : {!! $wallet !!}
                                    </div>
                                    <div class="col-md-12">
                                        {!!Form::hidden('available_wallet',$available_balance)!!}
                                        {!!Form::label('available_wallet',__('keywords.Available') .' '. __('keywords.Balance'))!!}
                                        : {!! $available_balance !!}
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
                                @if(auth()->user()->role_id!=3)
                                    {!!Form::select('bank_id',[],'',['class'=>'form-control','placeholder'=> __('keywords.Bank'),'id'=>'bank_id'])!!}
                                @else
                                    <select name="bank_id" id="bank_id" class="form-control">
                                        <option value="">@lang('keywords.Bank')</option>
                                        @foreach($banks as $key=>$value)
                                            <option value="{!! $value->id !!}"
                                                    data-transaction-type="{!! $value->transaction_fee_type !!}"
                                                    data-transaction-amount="{!! $value->transaction_fee+$value->tax_transaction !!}">{!! $value->name." - ".$value->account_number !!}</option>
                                        @endforeach
                                    </select>
                                @endif
                                <div class="error">
                                    <span class="help-block" for="bank_id"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 bank_div" style="display: none;">
                            <div class="form-group">
                                {!!Form::label('transaction_charge',__('keywords.TransactionCharge'))!!}
                                {!!Form::text('transaction_charge', old('transaction_charge'), ['class'=>'form-control','placeholder'=>__('keywords.TransactionCharge'),'id'=>'transaction_charge'])!!}
                            </div>
                            <div class="error">
                                <span class="help-block" for="transaction_charge"></span>
                            </div>
                        </div>
                        @if(auth()->user()->hasRole('client|credit and processing'))
                            <div class="col-md-6 branch_div" style="display: none;">
                                <div class="form-group">
                                    {!!Form::label('branch_id',__('keywords.Branch').' *')!!}
                                    @if(auth()->user()->role_id!=3)
                                        @if(auth()->user()->role_id==9)
                                            {!!Form::select('branch_id',$branches,'',['class'=>'form-control','placeholder'=> 'branches'])!!}
                                        @else
                                            {!!Form::select('branch_id',[],'',['class'=>'form-control','placeholder'=> __('keywords.Branch'),'id'=>'branch_id'])!!}
                                        @endif
                                    @else
                                        <select name="branch_id" id="branch_id" class="form-control">
                                            <option value="">@lang('keywords.Branch')</option>
                                            @foreach($branches as $key=>$value)
                                                <option value="{!! $key !!}">
                                                    {!! $value !!}
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif
                                    <div class="error">
                                        <span class="help-block" for="bank_id"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 branch_div" style="display:none;">
                                <div class="form-group text-center">
                                    @if(isset($country) && $country!=null && $country->map_link!='')
                                        <a href="{!! $country->map_link !!}" class="btn btn-primary mt-4"
                                           target="_blank">
                                            <i class="fa fa-map-marker"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('notes',__('keywords.Notes'))!!}
                                {!!Form::textarea('notes', old('notes'), ['class'=>'form-control','placeholder'=>__('keywords.Notes'),'maxlength'=>1000])!!}
                            </div>
                            <div class="error">
                                <span class="help-block" for="notes"></span>
                            </div>
                        </div>
                        @endhasanyrole
                        @hasanyrole('client')
                        <div class="col-md-12 terms_condition">
                            <label class="cs-checkbox">
                                <input type="checkbox" name="terms" value="1" id="loan_model_terms_checkbox"
                                       required> @lang('keywords.IAgreewith')
                                <a href="{!! url('/terms') !!}"
                                   target="_blank"> @lang('keywords.TermsAndConditions')</a>
                            </label>
                        </div>
                        @endhasanyrole
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id">
                    <button type="button" class="btn btn-secondary waves-effect"
                            data-dismiss="modal">@lang('keywords.Close')</button>
                    <button type="submit"
                            class="btn btn-info waves-effect waves-light apply_credit">@lang('keywords.Apply')</button>
                </div>
            </div>
        </form>
    </div>
</div>
