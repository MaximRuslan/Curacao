<div id="referralCategoryModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <form id="referral_category_form">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title mt-0">Referral Category</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        @if(auth()->user()->hasRole('super admin') && !session()->has('country'))
                            <div class="col-md-12">
                                <div class="form-group">
                                    {!!Form::label('country_id','Country *')!!}
                                    {!!Form::select('country_id',$countries,old('country_id'),['class'=>'form-control','id'=>'country_id','placeholder'=>'Select Country'])!!}
                                    <span class="error" for="country_id"></span>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('title','Title *')!!}
                                {!!Form::text('title', old('title'), ['class'=>'form-control','placeholder'=>'Title'])!!}
                                <span class="error" for="title"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('min_referrals','Min Referrals *')!!}
                                {!!Form::number('min_referrals', old('min_referrals'), ['class'=>'form-control','placeholder'=>'Min Referrals'])!!}
                                <span class="error" for="min_referrals"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('max_referrals','Max Referrals')!!}
                                {!!Form::number('max_referrals', old('max_referrals'), ['class'=>'form-control','placeholder'=>'Max Referrals'])!!}
                                <span class="error" for="max_referrals"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('loan_start','Pay Per Loan Start *')!!}
                                {!!Form::number('loan_start', old('loan_start'), ['class'=>'form-control','placeholder'=>'Pay Per Loan Start'])!!}
                                <span class="error" for="loan_start"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('loan_pif','Pay Per Loan PIF *')!!}
                                {!!Form::number('loan_pif', old('loan_pif'), ['class'=>'form-control','placeholder'=>'Pay Per Loan PIF'])!!}
                                <span class="error" for="loan_pif"></span>
                            </div>
                        </div>
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