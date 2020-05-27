<div id="js--reconciliation-history-modal" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title mt-0">@lang('keywords.reconciliation_history')</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-stripped">
                            <thead>
                            <tr>
                                <th>@lang('keywords.user')</th>
                                <th>@lang('keywords.Status')</th>
                                <th>@lang('keywords.Date')</th>
                            </tr>
                            </thead>
                            <tbody id="js--reconciliation-history-tbody">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
