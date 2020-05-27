<div id="userWorkModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
     style="display: none;">
    <div class="modal-dialog modal-lg" style="max-width: 1360px !important;">
        <div class="modal-content">
            <form id="usersWorkInfoForm" action="#">
                <div class="modal-header">
                    <h4 class="modal-title mt-0">Client Work</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <!-- Wizard with Validation -->
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card-box">
                                <div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {!!Form::label('employer','Employer *')!!}
                                                {!!Form::text('employer', old('employer'), ['class'=>'form-control','placeholder'=>'Employer','required'])!!}
                                                <span class="error" for="employer"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {!!Form::label('address1','Address *')!!}
                                                {!!Form::textarea('address', old('address'), ['class'=>'form-control','placeholder'=>'Address','id'=>'address1','required','rows'=>'3'])!!}
                                                <span class="error" for="address"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {!!Form::label('telephone','Work phone *')!!}
                                                <div class="telephone-group">
                                                    {!!Form::number('telephone_code', old('telephone_code'), ['class'=>'form-control numeric-input telephone-code','placeholder'=>'Code.'])!!}
                                                    {!!Form::number('telephone', old('telephone'), ['class'=>'form-control numeric-input numeric telephone-number','placeholder'=>'Work Phone','required'])!!}
                                                </div>
                                                <span class="error" for="telephone"></span>
                                                <span class="error" for="telephone_code"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                {!!Form::label('extension','Extension')!!}
                                                <div class="telephone-group">
                                                    {!!Form::text('extension', old('extension'), ['class'=>'form-control','placeholder'=>'Extension'])!!}
                                                    <span class="error" for="extension"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                {!!Form::label('position','Position *')!!}
                                                {!!Form::text('position', old('position'), ['class'=>'form-control','placeholder'=>'Position','required'])!!}
                                                <span class="error" for="position"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                {!!Form::label('employed_since','Employed Since *')!!}
                                                {!!Form::text('employed_since', old('employed_since'), ['class'=>'form-control  old-date-picker','placeholder'=>'Employed Since','required'])!!}
                                                <span class="error" for="employed_since"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                {!!Form::label('employment_type','Employment Type *')!!}
                                                {!!Form::select('employment_type',config('site.employment_type'),'',['class'=>'form-control select2single','placeholder'=>'Employment Type','required'])!!}
                                                <span class="error" for="employment_type"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                {!!Form::label('department','Department')!!}
                                                {!!Form::text('department', old('department'), ['class'=>'form-control','placeholder'=>'Department'])!!}
                                                <span class="error" for="department"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                {!!Form::label('supervisor_name','Manager / Supervisor Name')!!}
                                                {!!Form::text('supervisor_name', old('supervisor_name'), ['class'=>'form-control','placeholder'=>'Manager / Supervisor Name'])!!}
                                                <span class="error" for="supervisor_name"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                {!!Form::label('supervisor_telephone','Tel Number - Ext.')!!}
                                                <div class="telephone-group">
                                                    {!!Form::number('supervisor_telephone_code', old('supervisor_telephone_code'), ['class'=>'form-control telephone-code numeric-input','placeholder'=>'Code.'])!!}
                                                    {!!Form::number('supervisor_telephone', old('supervisor_telephone'), ['class'=>'form-control numeric-input telephone-number numeric','placeholder'=>'Tel Number - Ext.'])!!}
                                                </div>
                                                <span class="error" for="supervisor_telephone_code"></span>
                                                <span class="error" for="supervisor_telephone"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                {!!Form::label('contract_expires','Contract Expires')!!}
                                                {!!Form::text('contract_expires', old('contract_expires'), ['class'=>'form-control all-date-picker','placeholder'=>'Contract Expires'])!!}
                                                <span class="error" for="contract_expires"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                {!!Form::label('salary','Gross Salary *')!!}
                                                {!!Form::number('salary', old('salary'), ['class'=>'form-control','placeholder'=>'Gross Salary','min'=>0,'step'=>'0.01','required'])!!}
                                                <span class="error" for="salary"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                {!!Form::label('payment_frequency','Payment Frequency *')!!}
                                                {!!Form::select('payment_frequency',config('site.payment_frequency'),'',['class'=>'form-control select2single','placeholder'=>'Payment Frequency','required'])!!}
                                                <span class="error" for="payment_frequency"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End row -->
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id">
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary waves-effect userWorkInfoSubmit">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
