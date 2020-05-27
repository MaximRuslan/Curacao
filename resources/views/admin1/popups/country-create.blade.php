<div id="countryModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
     style="display: none;">
    <div class="modal-dialog modal-lg">
        <form id="country_form">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title mt-0">Country</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('name','Name *')!!}
                                {!!Form::text('name', old('name'), ['class'=>'form-control','placeholder'=>'Name'])!!}
                                <span class="error" for="name"></span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('country_code','Phone code (no +) *')!!}
                                {!!Form::number('country_code', old('country_code'), ['class'=>'form-control','placeholder'=>'Country Code'])!!}
                                <span class="error" for="country_code"></span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('valuta_name','Valuta Name *')!!}
                                {!!Form::text('valuta_name', old('valuta_name'), ['class'=>'form-control','placeholder'=>'Valuta Name'])!!}
                                <span class="error" for="name"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('rate_hylamiles','Rate Hylamiles *')!!}
                                {!!Form::text('rate_hylamiles', old('rate_hylamiles'), ['class'=>'form-control','placeholder'=>'Rate Hylamiles'])!!}
                                <span class="error" for="name"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('phone_length','Phone length *')!!}
                                {!!Form::number('phone_length', old('phone_length'), ['class'=>'form-control','placeholder'=>'Phone Length','min'=>1])!!}
                                <span class="error" for="phone_length"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('tax','Tax *')!!}
                                {!!Form::text('tax','',['class'=>'form-control','placeholder'=>'Tax'])!!}
                                <span class="error" for="tax"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('map_link','Map Link *')!!}
                                {!!Form::text('map_link','',['class'=>'form-control','placeholder'=>'Map Link'])!!}
                                <span class="error" for="map_link"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('tax_percentage','Tax Percentage *')!!}
                                {!!Form::number('tax_percentage', old('tax_percentage'), ['min'=>"0",'step'=>"0.01",'class'=>'form-control','placeholder'=>'Tax Percentage'])!!}
                                <span class="error" for="tax_percentage"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Upload Logo (Accept only png, gif, jpg, jpeg) *</label>
                                <input type="file" id="logo" name="logo" class="form-control"
                                       accept="image/png,image/gif,image/jpeg,image/jpg">
                            </div>
                            <span class="error" for="logo"></span>
                        </div>
                        <div class="logo-holder logo_image col-md-6" style="display: none">
                            <div class="form-group loan-image thumb-small">
                                <span class="helper"></span>
                                <img src="" class="img-responsive">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('timezone','Time Zone *')!!}
                                {!!Form::text('timezone','',['class'=>'form-control','placeholder'=>'Time Zone'])!!}
                                <span class="error" for="timezone"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('company_name','Company Name *')!!}
                                {!!Form::text('company_name','',['class'=>'form-control','placeholder'=>'Company Name'])!!}
                                <span class="error" for="company_name"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('email','Email *')!!}
                                {!!Form::email('email','',['class'=>'form-control','placeholder'=>'Email'])!!}
                                <span class="error" for="email"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('telephone','Telephone *')!!}
                                {!!Form::number('telephone','',['class'=>'form-control','placeholder'=>'Telephone'])!!}
                                <span class="error" for="telephone"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('sender_number','Sender Number *')!!}
                                {!!Form::text('sender_number','',['class'=>'form-control','placeholder'=>'Sender Number'])!!}
                                <span class="error" for="sender_number"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('web','Web *')!!}
                                {!!Form::text('web','',['class'=>'form-control','placeholder'=>'Web'])!!}
                                <span class="error" for="web"></span>
                            </div>
                        </div>
                        <div class="col-md-12"></div>
                        <div class="col-md-4 mt-4">
                            <div class="form-group">
                                <label><input type="checkbox" name="referral" value="1">Referral</label>
                                <span class="error" for="referral"></span>
                            </div>
                        </div>
                        <div class="col-md-4 mt-4">
                            <div class="form-group">
                                <label><input type="checkbox" name="raffle" value="1">Raffle</label>
                                <span class="error" for="raffle"></span>
                            </div>
                        </div>
                        <div class="col-md-4 mt-4">
                            <div class="form-group">
                                <label><input type="checkbox" name="decimal" value="1">Decimal</label>
                                <span class="error" for="decimal"></span>
                            </div>
                        </div>
                        <div class="col-md-12 mt-4">
                            <div class="form-group">
                                <label><input type="checkbox" name="pagare" value="1"> PAGARE</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('terms_eng','Terms Eng')!!}
                                {!!Form::textarea('terms_eng', $default_terms, ['class'=>'form-control cms_textarea','id'=>'terms_eng','placeholder'=>'Terms ENG','style'=>'height:500px;'])!!}
                                <span class="error" for="terms_eng"></span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('terms_esp','Terms ESP')!!}
                                {!!Form::textarea('terms_esp', $default_terms, ['class'=>'form-control cms_textarea','id'=>'terms_esp','placeholder'=>'Terms ESP','style'=>'height:500px;'])!!}
                                <span class="error" for="esp"></span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('terms_pap','Terms PAP')!!}
                                {!!Form::textarea('terms_pap', $default_terms, ['class'=>'form-control cms_textarea','id'=>'terms_pap','placeholder'=>'Terms PAP','style'=>'height:500px;'])!!}
                                <span class="error" for="terms_pap"></span>
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
