<div id="loanTypeModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
     style="display: none;">
    <div class="modal-dialog" style="min-width: 1120px;">
        <form action="{{route('loan-types.store')}}" id="loanType_form" onsubmit="return SaveLoanType(this)">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title mt-0">Loan types</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">Name *</label>
                                <input type="text" name="title" class="form-control" placeholder="e.g Grace 7">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">Name ESP</label>
                                <input type="text" name="title_es" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">Name PAP</label>
                                <input type="text" name="title_nl" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!!Form::label('number_of_days','Period (wks)')!!}
                                {!!Form::number('number_of_days', old('number_of_days'), ['class'=>'form-control','placeholder'=>'Period (wks)','min'=>0,'required'])!!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!!Form::label('interest','Interest (p/wk)')!!}
                                {!!Form::number('interest', old('interest'), ['class'=>'form-control','placeholder'=>'Interest (p/wk)','step'=>0.01,'min'=>0,'required'])!!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!!Form::label('cap_period','Cap period (wks)')!!}
                                {!!Form::number('cap_period', old('cap_period'), ['class'=>'form-control','placeholder'=>'Cap period (wks)','min'=>0,'required'])!!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="control-label">Minimum Loan</label>
                            <input type="number" name="minimum_loan" required class="form-control" id="minimum_loan">
                            <p class="help-block minimum_maximum_loan_error"></p>
                        </div>
                        <div class="col-md-4">
                            <label class="control-label">Maximum Loan</label>
                            <input type="number" name="maximum_loan" required class="form-control" id="maximum_loan">
                            <p class="help-block minimum_maximum_loan_error"></p>
                        </div>
                        <div class="col-md-4">
                            <label class="control-label">Unit</label>
                            <input type="number" name="unit" required class="form-control" id="unit">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('loan_component','Loan component (%)')!!}
                                {!!Form::number('loan_component', old('loan_component'), ['class'=>'form-control','placeholder'=>'Loan Component (%)','min'=>0,'required'])!!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('apr','APR(%)')!!}
                                {!!Form::number('apr', old('apr'), ['class'=>'form-control','placeholder'=>'APR(%)','min'=>0,'step'=>'0.01','required'])!!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('origination_type','Origination fee type')!!}
                                {!!Form::select('origination_type',['1'=>'percentage','2'=>'flat'],old('origination_type'),['class'=>'form-control','placeholder'=>'Origination Fee Type','required'])!!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('origination_amount','Origination fee amount')!!}
                                {!!Form::number('origination_amount', old('origination_amount'), ['class'=>'form-control','placeholder'=>'Origination Fee Amount','step'=>0.01,'required'])!!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('renewal_type','Renewal fee type')!!}
                                {!!Form::select('renewal_type',['1'=>'percentage','2'=>'flat'],old('renewal_type'),['class'=>'form-control','placeholder'=>'Renewal Fee Type','required'])!!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('renewal_amount','Renewal fee amount')!!}
                                {!!Form::number('renewal_amount', old('renewal_amount'), ['class'=>'form-control','placeholder'=>'Renewal Fee Amount','step'=>0.01,'required'])!!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('debt_collection_type','Debt Collection type')!!}
                                {!!Form::select('debt_collection_type',['1'=>'percentage','2'=>'flat'],old('debt_collection_type'),['class'=>'form-control','placeholder'=>'Debt Collection Type','required'])!!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('debt_collection_percentage','Debt Collection Fee')!!}
                                {!!Form::number('debt_collection_percentage', old('debt_collection_percentage'), ['class'=>'form-control','placeholder'=>'Debt Collection Fee','step'=>0.01,'required'])!!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('debt_collection_tax_type','Debt Collection tax type')!!}
                                {!!Form::select('debt_collection_tax_type',['1'=>'percentage','2'=>'flat'],old('debt_collection_tax_type'),['class'=>'form-control','placeholder'=>'Debt Collection Tax Type','required'])!!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('debt_collection_tax_value','Debt Collection tax Fee')!!}
                                {!!Form::number('debt_collection_tax_value', old('debt_collection_tax_value'), ['class'=>'form-control','placeholder'=>'Debt Collection tax Fee','step'=>0.01,'required'])!!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('debt_type','Admin fee type')!!}
                                {!!Form::select('debt_type',config('site.debt_collection_fee_type'),old('debt_type'),['class'=>'form-control','placeholder'=>'Admin fee type','required'])!!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('debt_amount','Admin fee amount')!!}
                                {!!Form::number('debt_amount', old('debt_amount'), ['class'=>'form-control','placeholder'=>'Admin fee amount','step'=>0.01,'required'])!!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('debt_tax_type','Admin fee tax type')!!}
                                {!!Form::select('debt_tax_type',['1'=>'percentage','2'=>'flat'],old('debt_tax_type'),['class'=>'form-control','placeholder'=>'Admin Fee Tax Type','required'])!!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('debt_tax_amount','Admin fee tax amount')!!}
                                {!!Form::number('debt_tax_amount', old('debt_tax_amount'), ['class'=>'form-control','placeholder'=>'Admin Fee Tax Amount','step'=>0.01,'required'])!!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('country_id','Country')!!}
                                {!!Form::select('country_id',$countries,old('country_id'),['class'=>'form-control','id'=>'country_id','required','placeholder'=>'Select Country'])!!}
                            </div>
                        </div>
                        {{--<div class="col-md-12">--}}
                        {{--<div class="form-group">--}}
                        {{--{!!Form::label('territory_id','Territory')!!}--}}
                        {{--{!!Form::select('territory_id[]',[],old('territory_id'),['class'=>'form-control select-multiple','multiple','required','id'=>'territory_id'])!!}--}}
                        {{--</div>--}}
                        {{--</div>--}}
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('loan_agreement_eng','Terms Eng')!!}
                                {!!Form::textarea('loan_agreement_eng', $default_terms, ['class'=>'form-control cms_textarea','id'=>'loan_agreement_eng','placeholder'=>'Loan agreement ENG','style'=>'height:500px;'])!!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('loan_agreement_esp','Terms ESP')!!}
                                {!!Form::textarea('loan_agreement_esp', $default_terms, ['class'=>'form-control cms_textarea','id'=>'loan_agreement_esp','placeholder'=>'Loan agreement ESP','style'=>'height:500px;'])!!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('loan_agreement_pap','Terms PAP')!!}
                                {!!Form::textarea('loan_agreement_pap', $default_terms, ['class'=>'form-control cms_textarea','id'=>'loan_agreement_pap','placeholder'=>'Loan agreement PAP','style'=>'height:500px;'])!!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Status</label><br>
                                <label class="radio-inline">
                                    <input type="radio" name="status" value="1">Active
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="status" value="0">Inactive
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id">
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-info waves-effect waves-light">Save changes</button>
                </div>
            </div>
        </form>
    </div>
</div>
