<div id="bankModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <form id="bank_form">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title mt-0">Bank</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!!Form::label('name','Name *')!!}
                            {!!Form::text('name', old('name'), ['class'=>'form-control','placeholder'=>'Name'])!!}
                            <span class="error" for="name"></span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            {!!Form::label('contact_person','Contact Person *')!!}
                            {!!Form::text('contact_person', old('contact_person'), ['class'=>'form-control','placeholder'=>'Contact Person'])!!}
                            <span class="error" for="contact_person"></span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            {!!Form::label('email','Email *')!!}
                            {!!Form::text('email', old('email'), ['class'=>'form-control','placeholder'=>'Email'])!!}
                            <span class="error" for="email"></span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            {!!Form::label('phone','Phone *')!!}
                            {!!Form::number('phone', old('phone'), ['class'=>'form-control','placeholder'=>'Phone'])!!}
                            <span class="error" for="phone"></span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            {!!Form::label('country_id','Country *')!!}
                            {!!Form::select('country_id',$countries,old('country_id'),['class'=>'form-control','placeholder'=>'Country','id'=>'country_id'])!!}
                            <span class="error" for="country_id"></span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            {!!Form::label('transaction_fee','Transaction Fee Amount *')!!}
                            {!!Form::number('transaction_fee', old('transaction_fee'), ['class'=>'form-control','placeholder'=>'Transaction Fee Amount','step'=>0.01])!!}
                            <span class="error" for="transaction_fee"></span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            {!!Form::label('tax_transaction','Tax On Transaction Fee *')!!}
                            {!!Form::number('tax_transaction', old('tax_transaction'), ['class'=>'form-control','placeholder'=>'Tax On Transaction Fee','step'=>0.01,'readonly'])!!}
                            <span class="error" for="tax_transaction"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id">
                    <input type="hidden" name="country_tax_percentage">
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-info waves-effect waves-light">Save changes</button>
                </div>
            </div>
        </form>
    </div>
</div>