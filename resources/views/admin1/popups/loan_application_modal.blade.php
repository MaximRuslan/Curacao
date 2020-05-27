<div id="loanApplicationModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg" style="max-width: 1200px;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title mt-0">Apply Loan Application</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="loanApplicationForm">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="hidden" id="apr">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Client *</label>
                                    {!!Form::select('client_id',$clients,'',['class'=>'form-control','id'=>'client_id','placeholder'=>'Select Client'])!!}
                                    <span class="error" for="client_id"></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Loan reason *</label>
                                    {!! Form::select('loan_reason',$reasons,'',['class'=>'form-control select2','id'=>'loan_reason','placeholder'=> 'Select Loan Reason' ]) !!}
                                    <span class="error" for="loan_reason"></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Loan type *</label>
                                    {!! Form::select('loan_type',[],'',['class'=>'form-control select2','id'=>'loan_type','placeholder'=>'Select Loan Type']) !!}
                                    <span class="error" for="loan_type"></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    {!!Form::label('amount','Requested Amount *')!!}
                                    {!!Form::select('amount',[],old('amount'),['class'=>'form-control select2','placeholder'=>'Select Amount','id'=>'amount'])!!}
                                    <span class="error" for="amount"></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">
                                        Suggested Loan Amount <span id="loan_component_amount"></span>
                                    </label>
                                    {!!Form::text('max_loan_amount', old('max_loan_amount'), ['class'=>'form-control','id'=>'max_loan_amount','placeholder'=>'Suggested Loan Amount','readonly'])!!}
                                    <span class="error" for="max_loan_amount"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="table-responsive">
                                <table class="table table-bordered table-stripped" style="width:100%">
                                    <tr>
                                        <td width="65%">
                                            Total Salary
                                        </td>
                                        <td width="35%" id="salary_amount"></td>
                                    </tr>
                                    <tr>
                                        <td width="65%">Total other loan</td>
                                        <td width="35%" id="existing_loan_amount"></td>
                                    </tr>
                                    <tr>
                                        <td width="65%">
                                            Origination Fee <span id="origination_fee_percentage"></span>
                                        </td>
                                        <td width="35%" id="origination_fee_amount"></td>
                                    </tr>
                                    <tr>
                                        <td width="65%">
                                            Tax On Origination Fee
                                            (<span id="territory_tax_percentage"></span> %)
                                        </td>
                                        <td width="35%" id="territory_tax"></td>
                                    </tr>
                                    <tr>
                                        <td width="65%">
                                            Interest <span id="interest_percentage"></span>
                                        </td>
                                        <td width="35%" id="interest_amount_span"></td>
                                    </tr>
                                    <tr>
                                        <td width="65%">
                                            Tax On Interest
                                        </td>
                                        <td width="35%" id="tax_interest"></td>
                                    </tr>
                                    <tr>
                                        <td width="65%">Credit Amount</td>
                                        <td width="35%" id="credit_amount"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <fieldset>
                                <legend class="fieldset-title">Income</legend>
                                <div class="amount-holder income-holder">

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
                                <legend class="fieldset-title">Other loans</legend>
                                <div class="row">
                                    <div class="col-md-11">
                                    </div>
                                    <div class="col-md-1">
                                        <div class="text-center">
                                            <button class="add-btn btn btn-success addNewOtherLoan" type="button">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="amount-holder other-loan-holder">
                                </div>
                            </fieldset>
                        </div>
                    </div>
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
                </form>

            </div>
        </div>
    </div>
</div>
