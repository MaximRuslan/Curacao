<form id="loanApplicationForm" method="POST" enctype='multipart/form-data'
      action="{!! route('client1.loans.store') !!}">
    <div class="row box">
        <div class="col-md-12">
            <fieldset>
                <legend class="fieldset-title">@lang('keywords.Income')</legend>
                <div class="amount-holder income-holder">
                    <div class="row income-item">
                        <div class="column col-md-2">
                            <div class="form-group">
                                <label class="control-label">@lang('keywords.Type')</label>
                                <label class="income-type-name form-control">@lang('keywords.Gross salary')</label>
                                <input type="hidden" class="income-type" name="income_type[0]" value="1">
                            </div>
                        </div>
                        <div class="column col-md-2">
                            <label class="control-label">@lang('keywords.Amount') *</label>
                            <input step="0.01" min="0" type="number" name="income_amount[0]" id="income_amount"
                                   class="income-amount form-control">
                        </div>
                        <div class="column col-md-2 date-holder">
                            <label class="control-label">@lang('keywords.Date of payment') *</label>
                            <input type="text" name="date_of_payment[0]" id="date_of_payment"
                                   class="income-date form-control date-picker">
                        </div>
                        <div class="column col-md-4">
                            <label class="control-label" data-toggle="tooltip"
                                   title="png,gif,jpg,jpeg,doc,docx,pdf">
                                @lang('keywords.UploadLastPaySlip')*
                            </label>
                            <br>
                            <input type="file" name="income_proof_image[0]" id="income_proof_image"
                                   onchange="filesizeValidation(this)"
                                   class="proof-photo form-control"
                                   accept="image/png,image/gif,image/jpeg,image/jpg,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf">

                            <div class="has-error">
                                <span class="help-block"></span>
                            </div>
                            <input type="hidden" name="image_hidden[0]" class="income-proof-image-hidden">
                        </div>
                        <div class="column actions text-right col-md-2">
                            <button class="clear-btn btn btn-info clearIncome" type="button">
                                <i class="material-icons">remove</i>
                            </button>
                            <input type="hidden" name="income_id[0]" class="income-id">

                            <button class="add-btn btn btn-success addNewIncome" type="button">
                                <i class="material-icons">add</i>
                            </button>
                            <button class="delete-btn btn btn-danger removeIncome" style="display: none" type="button">
                                <i class="material-icons">delete</i>
                            </button>
                            <a href="#" class="income-image"></a>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="row box">
        <div class="col-md-6">
            <div class="loan-errors alert alert-danger" style="display: none">
                <ul class="list-group"></ul>
            </div>
            <!-- <div class="row">
                <div class="col-md-10">
                    <div class="form-group">
                        <label class="control-label">@lang('keywords.Loan reason') *</label>
                        {!! Form::select('loan_reason',$reasons,'',['class'=>'form-control','id'=>'loan_reason','placeholder'=> __('keywords.Loan reason') ]) !!}
                    </div>
                </div>
            </div> -->
            <div class="row">
                <div class="col-md-10">
                    <div class="form-group">
                        <label class="control-label">@lang('keywords.Purchase Type') *</label>
                        {!! Form::select('loan_type',$types,'',['class'=>'form-control','id'=>'loan_type','placeholder'=>__('keywords.Purchase Type')]) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-10">
                    <div class="form-group">
                        <label class="control-label">@lang('keywords.Total Bundles') *</label>
                        <select name="amount" class="form-control" id="amount"
                                placeholder="{!! __('keywords.Total Bundles') !!}">
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-10">
                    <div class="form-group">
                        <label class="control-label">
                            @lang('keywords.Max Bundle Purchase Allowed')
                        </label>
                        <input name="max_loan_amount" id="max_loan_amount" id="max_loan_amount" type="text" readonly=""
                               class="form-control" placeholder="{!! __('keywords.Max Bundle Purchase Allowed') !!}">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="table-responsive">
                <table class="table table-bordered table-stripped" style="width:100%">
                    <tr>
                        <td width="65%">
                            @lang('keywords.Total Salary')
                        </td>
                        <td width="35%" id="salary_amount"></td>
                    </tr>
                    <tr>
                        <td width="65%">  @lang('keywords.Total other loan') </td>
                        <td width="35%" id="existing_loan_amount"></td>
                    </tr>
                    <tr>
                        <td width="65%">

                            @lang('keywords.Purchase fee')
                            <span id="origination_fee_percentage"></span>
                        </td>
                        <td width="35%" id="origination_fee_amount"></td>
                    </tr>
                    @if(isset($country))
                        <tr>
                            <td width="65%">
                                @lang('keywords.Tax Purchase fee') ({!! $country->tax_percentage !!} %)
                            </td>
                            <td width="35%" id="territory_tax"></td>
                        </tr>
                    @else
                        <tr>
                            <td width="65%">
                                @lang('keywords.Tax Purchase fee')
                                (<span id="territory_tax_percentage"></span> %)
                            </td>
                            <td width="35%" id="territory_tax"></td>
                        </tr>
                    @endif
                    <tr>
                        <td width="65%">
                            @lang('keywords.Interest') <span id="interest_percentage"></span>
                        </td>
                        <td width="35%" id="interest_amount_span"></td>
                    </tr>
                    <tr>
                        <td width="65%">
                            @lang('keywords.Tax On Interest')
                        </td>
                        <td width="35%" id="tax_interest"></td>
                    </tr>
                    <tr>
                        <td width="65%">@lang('keywords.Total miles purchased')</td>
                        <td width="35%" id="credit_amount"></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="row box">
        <div class="col-md-12">
            <div style="display: none;" class="expense_types_options">
                @foreach($otherLoanTypes as $type)
                    <option value="{{$type->id}}">{{$type->title}}</option>
                @endforeach
            </div>
            <fieldset>
                <div class="row">
                    <div class="col-md-11">
                        <legend class="fieldset-title">@lang('keywords.Other loans')</legend>
                    </div>
                    <div class="col-md-1">
                        <div class="text-center">
                            <button class="add-btn btn btn-success addNewOtherLoan" type="button">
                                <i class="material-icons">add</i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="amount-holder other-loan-holder">
                </div>
            </fieldset>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {!! Form::hidden('id','0') !!}

                @if(isset($country))
                    {!! Form::hidden('tax_percentage',$country->tax_percentage,['id'=>'tax_percentage']) !!}
                    {!! Form::hidden('tax_name',$country->tax,['id'=>'tax_name']) !!}
                @else
                    {!! Form::hidden('tax_percentage','',['id'=>'tax_percentage']) !!}
                    {!! Form::hidden('tax_name','',['id'=>'tax_name']) !!}
                @endif

                {!! Form::hidden('loan_component','',['id'=>'loan_component']) !!}
                {!! Form::hidden('apr','',['id'=>'apr']) !!}

                {!! Form::hidden('origination_type','',['id'=>'origination_type']) !!}
                {!! Form::hidden('origination_amount','',['id'=>'origination_amount']) !!}
                {!! Form::hidden('origination_fee','',['id'=>'origination_fee']) !!}

                {!! Form::hidden('renewal_type','',['id'=>'renewal_type']) !!}
                {!! Form::hidden('renewal_amount','',['id'=>'renewal_amount']) !!}
                {!! Form::hidden('debt_type','',['id'=>'debt_type']) !!}
                {!! Form::hidden('debt_amount','',['id'=>'debt_amount']) !!}
                {!! Form::hidden('debt_collection_type','',['id'=>'debt_collection_type']) !!}
                {!! Form::hidden('debt_collection_percentage','',['id'=>'debt_collection_percentage']) !!}
                {!! Form::hidden('debt_collection_tax_type','',['id'=>'debt_collection_tax_type']) !!}
                {!! Form::hidden('debt_collection_tax_value','',['id'=>'debt_collection_tax_value']) !!}
                {!! Form::hidden('debt_tax_type','',['id'=>'debt_tax_type']) !!}
                {!! Form::hidden('debt_tax_amount','',['id'=>'debt_tax_amount']) !!}
                {!! Form::hidden('period','',['id'=>'period']) !!}
                {!! Form::hidden('interest','',['id'=>'interest']) !!}
                {!! Form::hidden('cap_period','',['id'=>'cap_period']) !!}

                {!! Form::hidden('max_amount','',['id'=>'max_amount']) !!}
                {!! Form::hidden('tax','',['id'=>'tax']) !!}
                {!! Form::hidden('interest_amount','',['id'=>'interest_amount']) !!}
                {!! Form::hidden('salary','') !!}
                {!! Form::hidden('other_loan_deduction','') !!}
                {!! Form::hidden('user_status','') !!}
                {!! Form::hidden('signature','') !!}
                <br>
                <button class="btn btn-primary" type="submit" id="apply_load_modal_submit_button">
                    @lang('keywords.SEND APPLICATION')
                </button>
            </div>
        </div>
    </div>
</form>
