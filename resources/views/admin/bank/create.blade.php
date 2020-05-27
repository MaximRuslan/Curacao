<div id="bankModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
     style="display: none;">
    <div class="modal-dialog">
        <form action="{{route('banks.store')}}" id="bank_form" onsubmit="return SaveBank(this)">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title mt-0">Banks</h4>
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
                                {!!Form::label('contact_person','Contact Person')!!}
                                {!!Form::text('contact_person', old('contact_person'), ['class'=>'form-control','placeholder'=>'Contact Person','required'])!!}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('email','Email')!!}
                                {!!Form::text('email', old('email'), ['class'=>'form-control','placeholder'=>'Email','required'])!!}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('phone','Phone')!!}
                                {!!Form::number('phone', old('phone'), ['class'=>'form-control','placeholder'=>'Phone','required'])!!}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('country_id','Country')!!}
                                {!!Form::select('country_id',$countries,old('country_id'),['class'=>'form-control','placeholder'=>'Country','id'=>'country_id','required'])!!}
                            </div>
                        </div>
                        {{--<div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('territory_id','District')!!}
                                {!!Form::select('territory_id[]',[],old('teritory_id'),['class'=>'form-control select2multiple','multiple','placeholder'=>'Territory','required','id'=>'territory_id'])!!}
                            </div>
                        </div>--}}
                        {{--<div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('transaction_fee_type','Transaction Fee Type')!!}
                                {!!Form::select('transaction_fee_type',['1'=>'percentage','2'=>'flat'],old('transaction_fee_type'),['class'=>'form-control ','placeholder'=>'Transaction Fee Type','required'])!!}
                            </div>
                        </div>--}}
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::hidden('transaction_fee_type','2')!!}
                                {!!Form::label('transaction_fee','Transaction Fee Amount')!!}
                                {!!Form::number('transaction_fee', old('transaction_fee'), ['class'=>'form-control','placeholder'=>'Transaction Fee Amount','step'=>0.01,'required'])!!}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('tax_transaction','Tax On Transaction Fee')!!}
                                {!!Form::number('tax_transaction', old('tax_transaction'), ['class'=>'form-control','placeholder'=>'Tax On Transaction Fee','step'=>0.01,'required','readonly'])!!}
                            </div>
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