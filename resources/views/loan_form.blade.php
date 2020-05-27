<form class="form-horizontal" id="loanApplication" method="POST" enctype='multipart/form-data'
      action="{{ route('loan-applications.store') }}">
    {{ csrf_field() }}

    @if(isset($page))
        <input type="hidden" name="page" value="{!! $page ? $page : 2 !!}" id="jq__page">
    @endif
    <div class="row">
        <div class="col-md-7">
            <div class="loan-errors alert alert-danger" style="display: none">
                <ul class="list-group"></ul>
            </div>
            <?php
            $apr = '';
            if (isset($country)) {
                $apr = $country['apr'];
            }
            ?>
            <input type="hidden" id="jq__apr" value="{!! $apr !!}">
            <div class="loan-success alert alert-success" style="display: none">
                <b>Success</b>, Loan application submitted successfully
            </div>
            @hasanyrole('admin|super admin|processor|manager')
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Client *</label>
                        {!!Form::select('client_id',$clients,'',['class'=>'form-control','id'=>'client_id','placeholder'=>'Client','onchange'=>'changeUser()'])!!}
                    </div>
                </div>
            </div>
            @endhasanyrole
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label">@lang('keywords.Loan reason') *</label>
                        {!! Form::select('loan_reason',$reasons,'',['class'=>'form-control','id'=>'loan_reason','placeholder'=> __('keywords.Loan reason') ]) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label">@lang('keywords.Loan type') *</label>
                        @if(isset($types))
                            {!! Form::select('loan_type',$types,'',['class'=>'form-control loan_type','id'=>'loan_type','placeholder'=>__('keywords.Loan type')]) !!}
                        @else
                            {!! Form::select('loan_type',[],'',['class'=>'form-control loan_type','id'=>'loan_type','placeholder'=>__('keywords.Loan type')]) !!}
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label">@lang('keywords.Requested Amount') *</label>
                        <select name="amount" class="form-control" onchange="calculate()" id="amount"
                                placeholder="{!! __('keywords.Requested Amount') !!}"></select>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-group">
                        <label class="control-label">@lang('keywords.Suggested Loan Amount') <span
                                    id="loan_component_amount"></span></label>
                        <input name="max_loan_amount" id="max_loan_amount" id="max_loan_amount" type="text" readonly=""
                               class="form-control" placeholder="{!! __('keywords.Suggested Loan Amount') !!}">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-1"></div>
        <div class="col-md-4">
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
                    <!-- <tr>
                        <td width="65%">Available for loan</td>
                        <td width="35%" id="available_loan_amount"></td>
                    </tr> -->
                    <tr>
                        <td width="65%">

                            @lang('keywords.Origination Fee')
                            <span id="origination_fee_percentage"></span>
                        </td>
                        <td width="35%" id="origination_fee_amount"></td>
                    </tr>
                    @if(isset($country))
                        <tr>
                            <td width="65%">
                                @lang('keywords.Tax On Origination Fee') ({!! $country->tax_percentage !!} %)
                            </td>
                            <td width="35%" id="territory_tax"></td>
                        </tr>
                    @else
                        <tr>
                            <td width="65%">
                                @lang('keywords.Tax On Origination Fee')
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
                        <td width="65%">@lang('keywords.Credit Amount')</td>
                        <td width="35%" id="credit_amount"></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <fieldset>
                <legend class="fieldset-title">@lang('keywords.Income')</legend>
                <div class="amount-holder income-holder">
                    <div class="row income-item">
                        <div class="column col-md-2">
                            <div class="form-group">
                                <label class="control-label">@lang('keywords.Type')</label>
                                <label class="income-type-name form-control">@lang('keywords.Gross salary')</label>
                                <input type="hidden" class="income-type" name="income_type[0]"
                                       value="1">
                            </div>
                        </div>
                        <div class="column col-md-2">
                            <label class="control-label">@lang('keywords.Amount') *</label>
                            <input type="text" name="income_amount[0]" id="income_amount"
                                   class="income-amount form-control numeric-input">
                        </div>
                        <div class="column col-md-2 date-holder">
                            <label class="control-label">@lang('keywords.Date of payment') *</label>
                            <input type="text" name="date_of_payment[0]" id="date_of_payment"
                                   class="income-date form-control date-picker">
                        </div>
                        <div class="column col-md-3">
                            <label class="control-label" data-toggle="tooltip"
                                   title="png,gif,jpg,jpeg,doc,docx,pdf">
                                @lang('keywords.UploadLastPaySlip')
                                *</label>
                            <input type="file" name="income_proof_image[0]" id="income_proof_image"
                                   onchange="filesizeValidation(this)"
                                   class="proof-photo form-control"
                                   accept="image/png,image/gif,image/jpeg,image/jpg,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf">

                            <div class="has-error">
                                <span class="help-block"></span>
                            </div>
                            <input type="hidden" name="image_hidden[0]" class="income-proof-image-hidden">
                        </div>
                        <div class="column actions text-right col-md-3">
                            <button class="clear-btn btn btn-info" type="button"
                                    onclick="ClearIncome(this)"><i class="fa fa-minus"></i></button>
                            <input type="hidden" name="income_id[0]" class="income-id">

                            <button class="add-btn btn btn-success" type="button"
                                    onclick="addNewIncome(this)"><i class="fa fa-plus"></i></button>

                            <button class="delete-btn btn btn-danger" style="display: none"
                                    type="button" onclick="RemoveIncome(this)"><i
                                        class="fa fa-trash"></i></button>
                            <a href="#" class="income-image"></a>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div style="display: none;" class="expense_types_options">
                @foreach($otherLoanTypes as $type)
                    <option value="{{$type->id}}">{{$type->title}}</option>
                @endforeach
            </div>
            <fieldset>
                <legend class="fieldset-title">@lang('keywords.Other loans')</legend>
                <div class="amount-holder other-loan-holder">
                    <div class="row">
                        <div class="col-md-11">
                        </div>
                        <div class="col-md-1">
                            <div class="text-center">
                                <button class="add-btn btn btn-success" type="button"
                                        onclick="addNewOtherLoan(this)"><i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>

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
                {!! Form::hidden('origination_type','',['id'=>'origination_type']) !!}
                {!! Form::hidden('origination_amount','',['id'=>'origination_amount']) !!}
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
                {!! Form::hidden('origination_fee','',['id'=>'origination_fee']) !!}
                {!! Form::hidden('tax','',['id'=>'tax']) !!}
                {!! Form::hidden('interest_amount','',['id'=>'interest_amount']) !!}
                {!! Form::hidden('salary','') !!}
                {!! Form::hidden('other_loan_deduction','') !!}
                {!! Form::hidden('user_status','') !!}
                {!! Form::hidden('signature','') !!}
                <br>
                <button class="btn btn-primary" id="apply_load_modal_submit_button">
                    @lang('keywords.SEND APPLICATION')
                </button>
            </div>
        </div>
    </div>
</form>
