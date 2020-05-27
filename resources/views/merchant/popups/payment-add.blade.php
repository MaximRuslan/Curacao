<div id="js--payment-modal" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title mt-0">@lang('keywords.payment_add')</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <form id="transactionForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                {!! Form::label('user_id',__('keywords.user_id').' / '.__('keywords.loan_id')) !!}
                                {!! Form::text('user_id',old('user_id'),['id'=>'js--user-id','class'=>'form-control','placeholder'=>__('keywords.user_id').' / '.__('keywords.loan_id')]) !!}
                                <span class="error" for="user_id"></span>
                            </div>
                        </div>
                        <div class="col-md-4" style="margin-top:28px;">
                            <button class="btn btn-primary" id="js--user-find">@lang('keywords.find')</button>
                        </div>
                        <div class="col-md-6 mt-2 js--type-user js--user-name" style="display: none;">

                        </div>
                        <div class="col-md-6 mt-2 js--type-user" style="display: none;">
                            @lang('keywords.open_balance'): <span id="js--open-balance"></span>
                        </div>
                        <div class="col-md-12 mt-2 js--type-user js--user-message error" style="display: none; text-align: center;"></div>
                        @if(!session()->has('branch_id') && \App\Library\Helper::authMerchantUser()->type == 1)
                            <div class="col-md-12 mt-2">
                                <div class="form-group">
                                    {!! Form::label('branch_id',__('keywords.branch')) !!}
                                    {!! Form::select('branch_id',$branches,old('branch_id'),['class'=>'form-control select2Single','placeholder'=>__('keywords.select_branch')]) !!}
                                    <span class="error" for="branch_id"></span>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-12 mt-2">
                            <div class="form-group">
                                {!! Form::label('amount',__('keywords.petty_cash')) !!}
                                {!! Form::number('amount',old('amount'),['min'=>0,'class'=>'form-control','placeholder'=>__('keywords.petty_cash')]) !!}
                                <span class="error" for="amount"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="loan_id" value="0">

                    @if(env('RECEIPT_ON')==true)
                        <label style="margin-top: 10px;"><input type="checkbox" name="receipt" value="1" checked> @lang('keywords.download_receipt')</label>
                    @endif
                    <button class="btn btn-primary js--submit-button" disabled type="submit">@lang('keywords.save')</button>
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">@lang('keywords.close')</button>
                </div>
            </form>
        </div>
    </div>
</div>
