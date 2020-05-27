<div id="countryModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
     style="display: none;">
    <div class="modal-dialog modal-lg">
        <form action="{{route('countries.store')}}" id="country_form">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title mt-0">Countries</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('name','Name')!!}
                                {!!Form::text('name', old('name'), ['class'=>'form-control','placeholder'=>'Name','required'])!!}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('country_code','Phone code (no +)')!!}
                                {!!Form::text('country_code', old('country_code'), ['class'=>'form-control numeric-input','placeholder'=>'Country Code','required'])!!}
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Valuta Name</label>
                                <input type="text" name="valuta_name" class="form-control" placeholder="Valuta Name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('phone_length','Phone length')!!}
                                {!!Form::text('phone_length', old('phone_length'), ['class'=>'form-control','placeholder'=>'Phone Length','min'=>1,'required'])!!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('tax','Tax')!!}
                                {!!Form::text('tax','',['class'=>'form-control','placeholder'=>'Tax','required'])!!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('map_link','Map Link')!!}
                                {!!Form::text('map_link','',['class'=>'form-control','placeholder'=>'Map Link','required'])!!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Tax Percentage</label>
                                <input type="number" name="tax_percentage" min="0" step="0.01" required
                                       class="form-control numeric-input">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Upload Logo (Accept only png, gif, jpg, jpeg) *</label>
                                <input type="file" id="logo" name="logo"
                                       class="form-control" accept="image/png,image/gif,image/jpeg,image/jpg" required>
                                <input type="hidden" name="removeLogo">
                            </div>
                        </div>
                        <div class="logo-holder col-md-6" style="display: none">
                            <div class="form-group loan-image thumb-small">
                                <span class="helper"></span>
                                <img src="" class="img-responsive">
                                <button type="button" onclick="removeLogo(this)"
                                        class="btn btn-danger delete-btn">Delete Logo
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('timezone','Time Zone')!!}
                                {!!Form::text('timezone','',['class'=>'form-control','placeholder'=>'Time Zone','required'])!!}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('terms_eng','Terms Eng')!!}
                                {!!Form::textarea('terms_eng', $default_terms, ['class'=>'form-control cms_textarea','id'=>'terms_eng','placeholder'=>'Terms ENG','style'=>'height:500px;'])!!}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('terms_esp','Terms ESP')!!}
                                {!!Form::textarea('terms_esp', $default_terms, ['class'=>'form-control cms_textarea','id'=>'terms_esp','placeholder'=>'Terms ESP','style'=>'height:500px;'])!!}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('terms_pap','Terms PAP')!!}
                                {!!Form::textarea('terms_pap', $default_terms, ['class'=>'form-control cms_textarea','id'=>'terms_pap','placeholder'=>'Terms PAP','style'=>'height:500px;'])!!}
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
