<div id="loanTypeModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
     style="display: none;">
    <div class="modal-dialog" style="min-width: 1120px;">
        <form id="loan_type_form">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title mt-0">Loan type</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('country_id','Country *')!!}
                                {!!Form::select('country_id',$countries,old('country_id'),['class'=>'form-control','id'=>'country_id','placeholder'=>'Select Country'])!!}
                                <span class="error" for="country_id"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!!Form::label('title','Name *')!!}
                                {!!Form::text('title', old('title'), ['class'=>'form-control','placeholder'=>'Name *'])!!}
                                <span class="error" for="title"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!!Form::label('title_es','Name ESP *')!!}
                                {!!Form::text('title_es', old('title_es'), ['class'=>'form-control','placeholder'=>'Name ESP'])!!}
                                <span class="error" for="title_es"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!!Form::label('title_nl','Name PAP *')!!}
                                {!!Form::text('title_nl', old('title_nl'), ['class'=>'form-control','placeholder'=>'Name PAP'])!!}
                                <span class="error" for="title_nl"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!!Form::label('number_of_days','Period (wks) *')!!}
                                {!!Form::number('number_of_days', old('number_of_days'), ['class'=>'form-control','placeholder'=>'Period (wks)','min'=>0])!!}
                                <span class="error" for="number_of_days"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!!Form::label('interest','Interest (p/wk) *')!!}
                                {!!Form::number('interest', old('interest'), ['class'=>'form-control','placeholder'=>'Interest (p/wk)','step'=>0.01,'min'=>0])!!}
                                <span class="error" for="interest"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!!Form::label('cap_period','Cap period (wks) *')!!}
                                {!!Form::number('cap_period', old('cap_period'), ['class'=>'form-control','placeholder'=>'Cap period (wks)','min'=>0])!!}
                                <span class="error" for="cap_period"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!!Form::label('minimum_loan','Minimum Loan *')!!}
                                {!!Form::number('minimum_loan', old('minimum_loan'), ['class'=>'form-control','placeholder'=>'Minimum Loan','id'=>'minimum_loan'])!!}
                                <span class="error minimum_maximum_loan_error" for="minimum_loan"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!!Form::label('maximum_loan','Maximum Loan *')!!}
                                {!!Form::number('maximum_loan', old('maximum_loan'), ['class'=>'form-control','placeholder'=>'Maximum Loan','id'=>'maximum_loan'])!!}
                                <span class="error minimum_maximum_loan_error" for="maximum_loan"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!!Form::label('unit','Unit *')!!}
                                {!!Form::number('unit', old('unit'), ['class'=>'form-control','placeholder'=>'Unit'])!!}
                                <span class="error" for="unit"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('loan_component','Loan component(%) *')!!}
                                {!!Form::number('loan_component', old('loan_component'), ['class'=>'form-control','placeholder'=>'Loan Component (%)','min'=>0])!!}
                                <span class="error" for="loan_component"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('apr','APR(%) *')!!}
                                {!!Form::number('apr', old('apr'), ['class'=>'form-control','placeholder'=>'APR(%)','min'=>0,'step'=>'0.01'])!!}
                                <span class="error" for="apr"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('origination_type','Origination fee type *')!!}
                                {!!Form::select('origination_type',['1'=>'percentage','2'=>'flat'],old('origination_type'),['class'=>'form-control','id'=>'origination_type','placeholder'=>'Origination Fee Type'])!!}
                                <span class="error" for="origination_type"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('origination_amount','Origination fee amount *')!!}
                                {!!Form::number('origination_amount', old('origination_amount'), ['class'=>'form-control','placeholder'=>'Origination Fee Amount','step'=>0.01])!!}
                                <span class="error" for="origination_amount"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('renewal_type','Renewal fee type *')!!}
                                {!!Form::select('renewal_type',['1'=>'percentage','2'=>'flat'],old('renewal_type'),['class'=>'form-control','id'=>'renewal_type','placeholder'=>'Renewal Fee Type'])!!}
                                <span class="error" for="renewal_type"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('renewal_amount','Renewal fee amount *')!!}
                                {!!Form::number('renewal_amount', old('renewal_amount'), ['class'=>'form-control','placeholder'=>'Renewal Fee Amount','step'=>0.01])!!}
                                <span class="error" for="renewal_amount"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('debt_collection_type','Debt Collection type *')!!}
                                {!!Form::select('debt_collection_type',['1'=>'percentage','2'=>'flat'],old('debt_collection_type'),['class'=>'form-control','id'=>'debt_collection_type','placeholder'=>'Debt Collection Type'])!!}
                                <span class="error" for="debt_collection_type"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('debt_collection_percentage','Debt Collection Fee *')!!}
                                {!!Form::number('debt_collection_percentage', old('debt_collection_percentage'), ['class'=>'form-control','placeholder'=>'Debt Collection Fee','step'=>0.01])!!}
                                <span class="error" for="debt_collection_percentage"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('debt_collection_tax_type','Debt Collection tax type *')!!}
                                {!!Form::select('debt_collection_tax_type',['1'=>'percentage','2'=>'flat'],old('debt_collection_tax_type'),['class'=>'form-control','id'=>'debt_collection_tax_type','placeholder'=>'Debt Collection Tax Type'])!!}
                                <span class="error" for="debt_collection_tax_type"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('debt_collection_tax_value','Debt Collection tax Fee *')!!}
                                {!!Form::number('debt_collection_tax_value', old('debt_collection_tax_value'), ['class'=>'form-control','placeholder'=>'Debt Collection tax Fee','step'=>0.01])!!}
                                <span class="error" for="debt_collection_tax_value"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('debt_type','Admin fee type *')!!}
                                {!!Form::select('debt_type',config('site.debt_collection_fee_type'),old('debt_type'),['class'=>'form-control','id'=>'debt_type','placeholder'=>'Admin fee type'])!!}
                                <span class="error" for="debt_type"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('debt_amount','Admin fee amount *')!!}
                                {!!Form::number('debt_amount', old('debt_amount'), ['class'=>'form-control','placeholder'=>'Admin fee amount','step'=>0.01])!!}
                                <span class="error" for="debt_amount"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('debt_tax_type','Admin fee tax type *')!!}
                                {!!Form::select('debt_tax_type',['1'=>'percentage','2'=>'flat'],old('debt_tax_type'),['class'=>'form-control','id'=>'debt_tax_type','placeholder'=>'Admin Fee Tax Type'])!!}
                                <span class="error" for="debt_tax_type"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('debt_tax_amount','Admin fee tax amount *')!!}
                                {!!Form::number('debt_tax_amount', old('debt_tax_amount'), ['class'=>'form-control','placeholder'=>'Admin Fee Tax Amount','step'=>0.01])!!}
                                <span class="error" for="debt_tax_amount"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h3>User Status</h3>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="checkbox checkbox-purple">
                                            <label>
                                                <input name="user_status[]" type="checkbox" value="0" class="all_user_status">
                                                All
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                @foreach($user_statuses as $key=>$status)
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-purple">
                                                <label>
                                                    <input name="user_status[]" type="checkbox" value="{!! $key !!}" class="user_status">
                                                    {!! $status !!}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <span class="error" for="user_status"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('loan_agreement_eng','Terms Eng')!!}
                                {!!Form::textarea('loan_agreement_eng', $default_terms, ['class'=>'form-control cms_textarea','id'=>'loan_agreement_eng','placeholder'=>'Loan agreement ENG','style'=>'height:500px;'])!!}
                                <span class="error" for="loan_agreement_eng"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('loan_agreement_esp','Terms ESP')!!}
                                {!!Form::textarea('loan_agreement_esp', $default_terms, ['class'=>'form-control cms_textarea','id'=>'loan_agreement_esp','placeholder'=>'Loan agreement ESP','style'=>'height:500px;'])!!}
                                <span class="error" for="loan_agreement_esp"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('loan_agreement_pap','Terms PAP')!!}
                                {!!Form::textarea('loan_agreement_pap', $default_terms, ['class'=>'form-control cms_textarea','id'=>'loan_agreement_pap','placeholder'=>'Loan agreement PAP','style'=>'height:500px;'])!!}
                                <span class="error" for="loan_agreement_pap"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('agreement','PAGARE')!!}
                                <a href="#nogo" class="showAgreementEditor">Show</a>
                            </div>
                        </div>
                        <div class="col-md-12 agreementDiv" style="display: none;">
                            <div class="form-group">
                                {!!Form::textarea('pagare', '', ['class'=>'form-control cms_textarea','id'=>'agreement','placeholder'=>'PAGARE','style'=>'height:500px;'])!!}
                                <span class="error" for=pagare"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Status *</label><br>
                                <label class="radio-inline">
                                    <input type="radio" name="status" value="1" checked>Active
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="status" value="0">Inactive
                                </label>
                            </div>
                        </div>
                        <span class="error" for="status"></span>
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
