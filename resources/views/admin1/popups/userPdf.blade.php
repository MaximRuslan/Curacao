<div id="userCountryPdfModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            {!! Form::open(['id'=>'userCountryPdfForm']) !!}
            <div class="modal-header">
                <h4 class="modal-title mt-0">PAGARE</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <!-- Wizard with Validation -->
                <div class="card-box">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('loan_type','Loan Type *') !!}
                                {!!Form::select('loan_type',[], old('amount'), ['class'=>'form-control select2single'])!!}
                                <span class="error" for="loan_type"></span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('amount','Amount *')!!}
                                {!!Form::text('amount', old('amount'), ['class'=>'form-control','placeholder'=>'Amount'])!!}
                                <span class="error" for="amount"></span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('amount_in_words','Amount in words *')!!}
                                {!!Form::text('amount_in_words', old('amount_in_words'), ['class'=>'form-control','placeholder'=>'Amount in words'])!!}
                                <span class="error" for="amount_in_words"></span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('date','Date *')!!}
                                {!!Form::text('date', old('date'), ['class'=>'form-control all-date-picker','placeholder'=>'Date'])!!}
                                <span class="error" for="date"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End row -->
            </div>
            <div class="modal-footer">
                <input type="hidden" name="id">
                <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary waves-effect">Print</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
