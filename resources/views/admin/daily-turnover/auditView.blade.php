<div id="auditReportView" class="modal fade" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title mt-0">
                    Audit
                    @if(request('type')==1)
                        Day Open
                    @elseif(request('type')==2)
                        Bank
                    @elseif(request('type')==3)
                        Vault
                    @endif
                    <span id="branch_name"></span>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <table class="table  table-striped table-bordered">
                        <thead>
                        <tr>
                            <th id="date_title"></th>
                            <th>Begin Balance</th>
                            <th>In</th>
                            <th>Out</th>
                            <th>End Balance</th>
                            <th>Difference</th>
                        </tr>
                        </thead>
                        <tbody id="auditReportTbody">
                        </tbody>
                        <tfooter>
                            <tr>
                                <th>Total</th>
                                <th id="total_dayopen_sum"></th>
                                <th id="total_day_in"></th>
                                <th id="total_day_out"></th>
                                <th id="total_next_dayopen_sum"></th>
                                <th id="total_diff"></th>
                            </tr>
                        </tfooter>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-info waves-effect waves-light approveTodayReport">Approve</button>
            </div>
        </div>
    </div>
</div>