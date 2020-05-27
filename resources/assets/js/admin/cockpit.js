const cockpit = {
    el: {},
    data: {
        datatable: ''
    },
    init() {
        let _this = this;
        _this.bindUiActions();
        datePickerInit();
        $('#js--user').select2();
    },
    bindUiActions() {
        let _this = this;
        $(document).on('click', '.js--search', function (e) {
            e.preventDefault();
            _this.getData();
        });
        $(document).on('click', '.js--export', function (e) {
            e.preventDefault();
            _this.getExcel();
        });
        $(document).on('change', '#start_date_below', function () {
            $('#end_date_below').datepicker('setStartDate', $(this).val());
        });
        $(document).on('change', '#end_date_below', function () {
            $('#start_date_below').datepicker('setEndDate', $(this).val());
        });
        $(document).on('click', '.js--show-data', function () {
            if ($(this).parent().find('.show-row').length > 0) {
                $(this).parent().find('.show-row').removeClass('show-row').fadeOut(500);
                $(this).find('.js--expand-button').html(`<i class="fa fa-plus" style="color: blue;"></i>`)
            } else {
                $(this).parent().find('.hide-row').addClass('show-row').fadeIn(1000);
                $(this).find('.js--expand-button').html(`<i class="fa fa-minus" style="color: blue;"></i>`)
            }
        });
    },
    getData() {
        let _this = this;
        let start = $('#start_date_below').val();
        let end = $('#end_date_below').val();
        $.ajax({
            dataType: 'json',
            method: 'post',
            url: adminAjaxURL + 'cockpit',
            data: {
                start: start,
                end: end,
                user: $('#js--user').val()
            },
            success(data) {
                let str = ``;
                for (const employee of data['employees']) {
                    str += `
                    <div class="col-lg-12">
                    <div class="card-box">
                        <div class="row js--show-data">
                            <div class="col-md-11">
                                <h4 class="m-t-0 header-title" style="font-weight: 600;">${employee['name']}</h4>
                            </div>
                            <div class="col-md-1 text-right">
                                <span class="menu-arrow js--expand-button"><i class="fa fa-plus" style="color: blue;"></i></span>
                            </div>
                        </div>
                       
                        <table class="table table-bordered mt-2">
                            <thead class="thead-default">
                            <tr>
                                <th style="font-weight: 600;">Loan Status</th>
                                <th style="font-weight: 600;">Accounts</th>
                                <th style="font-weight: 600;">No Follow Up Date</th>
                                <th style="font-weight: 600;">Expired Follow Up Date</th>
                                <th style="font-weight: 600;">Principal<br>(<span style="color: blue;">Collected</span>/Outstanding)</th>
                                <th style="font-weight: 600;">Fees<br>(<span style="color: blue;">Collected</span>/Outstanding)</th>
                                <th style="font-weight: 600;">Debt Collection Fees<br>(<span style="color: blue;">Collected</span>/Outstanding)</th>
                                <th style="font-weight: 600;">Score</th>
                            </tr>
                            </thead>
                            <tbody class="hide-row">
                            <tr>
                                <th>Current</th>
                                <td>${employee['debts']['current']}</td>
                                <td>${employee['no_follow_up']['current']}</td>
                                <td>${employee['expired_follow_up']['current']}</td>
                                <td>
                                    <span style="color: blue;">${employee['principal_collected_format']['current']}</span><br>
                                    ${employee['principal_format']['current']}
                                </td>
                                <td>
                                    <span style="color: blue;">${employee['fees_collected_format']['current']}</span><br>
                                    ${employee['fees_format']['current']}
                                </td>
                                <td>
                                    <span style="color: blue;">${employee['debt_collected_format']['current']}</span><br>
                                    ${employee['debt_format']['current']}
                                </td>
                                <td>${employee['score']['current']}</td>
                            </tr>
                            <tr>
                                <th>In Default</th>
                                <td>${employee['debts']['in_default']}</td>
                                <td>${employee['no_follow_up']['in_default']}</td>
                                <td>${employee['expired_follow_up']['in_default']}</td>
                                <td>
                                    <span style="color: blue;">${employee['principal_collected_format']['in_default']}</span><br>
                                    ${employee['principal_format']['in_default']}
                                </td>
                                <td>
                                    <span style="color: blue;">${employee['fees_collected_format']['in_default']}</span><br>
                                    ${employee['fees_format']['in_default']}
                                </td>
                                <td>
                                    <span style="color: blue;">${employee['debt_collected_format']['in_default']}</span><br>
                                    ${employee['debt_format']['in_default']}
                                </td>
                                <td>${employee['score']['in_default']}</td>
                            </tr>
                            <tr>
                                <th>Debt Collector</th>
                                <td>${employee['debts']['debt_collector']}</td>
                                <td>${employee['no_follow_up']['debt_collector']}</td>
                                <td>${employee['expired_follow_up']['debt_collector']}</td>
                                <td>
                                    <span style="color: blue;">${employee['principal_collected_format']['debt_collector']}</span><br>
                                    ${employee['principal_format']['debt_collector']}
                                </td>
                                <td>
                                    <span style="color: blue;">${employee['fees_collected_format']['debt_collector']}</span><br>
                                    ${employee['fees_format']['debt_collector']}
                                </td>
                                <td>
                                    <span style="color: blue;">${employee['debt_collected_format']['debt_collector']}</span><br>
                                    ${employee['debt_format']['debt_collector']}
                                </td>
                                <td>${employee['score']['debt_collector']}</td>
                            </tr>
                            
                            </tbody>
                            <tfoot>
                            <tr>
                                <th style="font-weight: 600;">Total</th>
                                <td style="font-weight: 600;">${employee['debts']['total']}</td>
                                <td style="font-weight: 600;">${employee['no_follow_up']['total']}</td>
                                <td style="font-weight: 600;">${employee['expired_follow_up']['total']}</td>
                                <td style="font-weight: 600;">
                                    <span style="color: blue;">${employee['principal_collected_format']['total']}</span><br>
                                    ${employee['principal_format']['total']}
                                </td>
                                <td style="font-weight: 600;">
                                    <span style="color: blue;">${employee['fees_collected_format']['total']}</span><br>
                                    ${employee['fees_format']['total']}
                                </td>
                                <td style="font-weight: 600;">
                                    <span style="color: blue;">${employee['debt_collected_format']['total']}</span><br>
                                    ${employee['debt_format']['total']}
                                </td>
                                <td style="font-weight: 600;">${employee['score']['total']}</td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>`;
                }
                $('#js--cockpit-data').html(str);
            }
        });
    },
    getExcel() {
        let _this = this;
        let start = $('#start_date_below').val();
        let end = $('#end_date_below').val();
        $.ajax({
            dataType: 'json',
            method: 'post',
            url: adminAjaxURL + 'cockpit/export',
            data: {
                start: start,
                end: end,
            },
            success(response) {
                var url = response['url'];
                $('#exportPDFLink').attr('href', url);
                $('#exportPDFLink').attr('download', '');
                $('#exportPDFLink')[0].click();
            }
        });
    }
};
cockpit.init();