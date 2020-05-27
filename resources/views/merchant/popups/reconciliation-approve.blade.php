<div id="js--reconciliation-approve-modal" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title mt-0">@lang('keywords.approve')</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <form id="js--reconciliation-approve-form">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('otp',__('keywords.otp')) !!}
                                {!! Form::text('otp',old('otp'),['class'=>'form-control','placeholder'=>__('keywords.otp')]) !!}
                                <span class="error" for="otp"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id" value="0">
                    <button class="btn btn-primary js--submit-button" type="submit">@lang('keywords.approve')</button>
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">@lang('keywords.close')</button>
                </div>
            </form>
        </div>
    </div>
</div>
