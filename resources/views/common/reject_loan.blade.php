<div id="rejectLoanModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true"
     style="display: none;">
    <div class="modal-dialog">
        <input type="hidden" name="id">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title mt-0 loan_title">Reject Loan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <p class="loan_desc">Are you sure, you want to reject this loan?</p>
                    </div>
                    <div class="col-md-12 mt-3">
                        {!!Form::label('note','Note')!!}
                        {!!Form::textarea('note', old('note'), ['class'=>'form-control','placeholder'=>'Note','id'=>'reject_note'])!!}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="id">
                <input type="hidden" name="reason_required">
                <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">No</button>
                <button type="button" class="btn btn-danger waves-effect waves-light"
                        onclick="updateStatus('rejectLoanModal','Declined')">Yes, Reject
                </button>
            </div>
        </div>
    </div>
</div>
