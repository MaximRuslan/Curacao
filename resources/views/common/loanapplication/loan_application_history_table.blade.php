<div id="loanHistoryModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-full">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title mt-0">Transaction History</h4>
                <div class="text-right">
                    <button type="button" class="btn btn-primary" id="addNewHistory">Cron Job</button>
                </div>
                <button type="button" class="close" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Week Iteration</th>
                        <th>Date</th>
                        <th>Transaction</th>
                        <th>Amount For Trans.</th>
                        <th>Principal</th>
                        <th>Origination</th>
                        <th>Interest</th>
                        <th>Renewal</th>
                        <th>Tax</th>
                        <th>Debt</th>
                        <th>Debt tax</th>
                        <th>Total Balance (Excl. tax)</th>
                        <th>Total Balance</th>
                    </tr>
                    </thead>
                    <tbody class="loan_hostory_table"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
