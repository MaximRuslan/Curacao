<div id="creditStatusModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
     data-dismiss="modal"
     style="display: none;">
    <div class="modal-dialog">
        <form id="credit_status_form">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title mt-0">@lang('keywords.Credits')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('notes','Note')!!}
                                {!!Form::textarea('notes',old('notes'),['class'=>'form-control','placeholder'=>'Note'])!!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id">
                    <input type="hidden" name="status">
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-info waves-effect waves-light statusChange"></button>
                </div>
            </div>
        </form>
    </div>
</div>
