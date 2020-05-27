<div id="currentLoanModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true"
     style="display: none;">
    <div class="modal-dialog">
        <input type="hidden" name="id">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title mt-0 loan_title">Current Loan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <p class="loan_desc">Are you sure, you want to change this loan status to "Current"?</p>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="id">
                <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">No</button>
                <button type="button" class="btn btn-danger waves-effect waves-light"
                        onclick="updateStatus('currentLoanModal','current')">Yes
                </button>
            </div>
        </div>
    </div>
</div>
