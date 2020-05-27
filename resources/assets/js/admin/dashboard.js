var dashboard = {
    el: {
        country: "#country_id",
        branch: "#branch_id",
        user: "#user_id",
        credit: "#credit_id",
        transaction: "#transaction_id",
        start_date: "#start_date",
        end_date: "#end_date",
        table_tbody: '#table_tbody',
        table_tfoot: '#table_tfoot',
        total_client: '#total_client',
        total_credit: '#total_credit',
        searchButton: '.js--search',
        exportButton: '.js--excel',
        cashTrackingExcel: '.js--cash-excel',
        start_below: '#start_date_below',
        end_below: '#end_date_below',
        start_cash: '#start_date_cash',
        end_cash: '#end_date_cash',
        start_loan: '#start_date_loan',
        end_loan: '#end_date_loan',
        client_id_loan: '#client_id_loan',
        branch_id_loan: '#branch_id_loan',
        user_id_loan: '#user_id_loan',
        historyExcel: '.js--history-excel',
        total: {
            loans: '#js--total-loans-count',
            loans_amount: '#js--total-loans-amount',
            loans_amount_collected: '#js--total-loans-amount-collected',

            renewal_fees: '#js--renewal-fees',
            debt_collection_fees: '#js--debt-collection-fees',
            admin_fees: '#js--admin-fees',
            interest: '#js--interest-posted',
            origination_fees: '#js--origination-fees',

            renewal_collected: '#js--renewal-fees-collected',
            debt_collection_collected: '#js--debt-collection-fees-collected',
            admin_collected: '#js--admin-fees-collected',
            interest_collected: '#js--interest-collected',
            total_collected: '#js--total-collected',

            principal_outstanding: '#js--principal-outstanding',
            renewal_outstanding: '#js--renewal-fees-outstanding',
            debt_collection_fees_outstanding: '#js--debt-collection-fees-outstanding',
            interest_outstanding: '#js--interest-outstanding',
            admin_fees_outstanding: '#js--admin-fees-outstanding'
        }
    },
    data: {},
    init() {
        var _this = this;
        $(_this.el.country).select2();
        $(_this.el.branch).select2();
        $(_this.el.transaction).select2();
        $(_this.el.credit).select2();
        $(_this.el.user).select2();
        $(_this.el.client_id_loan).select2();
        $(_this.el.branch_id_loan).select2();
        $(_this.el.user_id_loan).select2();
        _this.binsUiActions();
        datePickerInit();
        _this.getData();
        _this.getBelowData();
        $(_this.el.end_below).datepicker('setStartDate', new Date(moment($(_this.el.start_below).val(), 'DD/MM/YYYY')));
    },
    binsUiActions() {
        var _this = this;
        $(document).on('change', _this.el.country + ',' + _this.el.start_date + ',' + _this.el.end_date, (e) => {
            e.preventDefault();
            _this.getData();
        });

        $(document).on('click', _this.el.searchButton, function (e) {
            e.preventDefault();
            _this.getBelowData();
        });

        $(document).on('click', _this.el.exportButton, function (e) {
            e.preventDefault();
            _this.getExcelSheet();
        });

        $(document).on('click', _this.el.historyExcel, function (e) {
            e.preventDefault();
            _this.historyExcel();
        });

        $(document).on('change', _this.el.start_below, function (e) {
            $(_this.el.end_below).datepicker('setStartDate', new Date(moment($(_this.el.start_below).val(), 'DD/MM/YYYY')));
        });

        $(document).on('change', _this.el.start_loan, function (e) {
            $(_this.el.end_loan).datepicker('setStartDate', new Date(moment($(_this.el.start_loan).val(), 'DD/MM/YYYY')));
        });
        $(document).on('change', _this.el.end_loan, function (e) {
            $(_this.el.start_loan).datepicker('setEndDate', new Date(moment($(_this.el.end_loan).val(), 'DD/MM/YYYY')));
        });

        $(document).on('click', _this.el.cashTrackingExcel, function (e) {
            e.preventDefault();
            _this.getCashExcelSheet();
        });
    },

    getData() {
        var _this = this;
        var country = $(_this.el.country).val();
        var start_date = $(_this.el.start_date).val();
        var end_date = $(_this.el.end_date).val();
        $.ajax({
            dataType: 'json',
            method: 'post',
            url: adminAjaxURL + 'dashboard/data',
            data: {
                country: country,
                start_date: start_date,
                end_date: end_date
            },
            success: _this.success
        })
    },
    success(response) {
        var str = '';
        var tfoot = ``;
        for (var i in response.rows) {
            let outstanding_html = ``;
            if (response.rows[i]['Outstanding'] != undefined) {
                outstanding_html = `<td>${response.rows[i]['Outstanding']}</td>`;
            }
            let received_html = ``;
            if (response.rows[i]['Payment Received'] != undefined) {
                received_html = `<td>${response.rows[i]['Payment Received']}</td>`;
            }
            if (i != 'Total') {
                str += `<tr>
                        <td  style="font-weight: 600;">${i}</td>
                        <td>${response.rows[i]['Loans']}</td>
                        ${outstanding_html}
                        ${received_html}
                    </tr>`
            } else {
                tfoot += `<tr style="font-weight: 600; border-top: 1px solid;">
                        <td>${i}</td>
                        <td>${response.rows[i]['Loans']}</td>
                        ${outstanding_html}
                        ${received_html}
                    </tr>`;
            }
        }
        tfoot += ``;
        $(dashboard.el.table_tbody).html(str);
        $(dashboard.el.table_tfoot).html(tfoot);
        $(dashboard.el.total_client).text(response.total_clients);
        $(dashboard.el.total_credit).text(response.total_credits);
    },
    getBelowData() {
        var _this = this;
        var country = $(_this.el.country).val();
        var start_date = $(_this.el.start_below).val();
        var end_date = $(_this.el.end_below).val();
        $.ajax({
            dataType: 'json',
            method: 'post',
            url: adminAjaxURL + 'dashboard/data/total',
            data: {
                country: country,
                start_date: start_date,
                end_date: end_date
            },
            success: _this.successTotal
        });
    },
    successTotal(response) {
        let _this = dashboard;
        $(_this.el.total.loans).text(response['Total Loans Count']);
        $(_this.el.total.loans_amount).text(response['Total Loans Amount']);
        $(_this.el.total.loans_amount_collected).text(response['Total Loans Collected']);

        $(_this.el.total.renewal_fees).text(response['Total Renewal Fees Posted']);
        $(_this.el.total.debt_collection_fees).text(response['Total Debt Collection Fees Posted']);
        $(_this.el.total.origination_fees).text(response['Total Origination fee']);
        $(_this.el.total.interest).text(response['Total Interest Posted']);
        $(_this.el.total.admin_fees).text(response['Total Admin Fees Posted']);

        $(_this.el.total.renewal_collected).text(response['Total Renewal Fees Collected']);
        $(_this.el.total.debt_collection_collected).text(response['Total Debt Collection Fees Collected']);
        $(_this.el.total.admin_collected).text(response['Total Admin Fees Collected']);
        $(_this.el.total.interest_collected).text(response['Total Interest Collected']);
        $(_this.el.total.total_collected).text(response['Total Collected']);

        $(_this.el.total.principal_outstanding).text(response['Total Principal Outstanding']);
        $(_this.el.total.renewal_outstanding).text(response['Total Renewal Fees Outstanding']);
        $(_this.el.total.debt_collection_fees_outstanding).text(response['Total Debt Collector Fees Outstanding']);
        $(_this.el.total.interest_outstanding).text(response['Total Interest Outstanding']);
        $(_this.el.total.admin_fees_outstanding).text(response['Total Admin Fees Outstanding']);
    },
    getExcelSheet() {
        var _this = this;
        var country = $(_this.el.country).val();
        var start_date = $(_this.el.start_below).val();
        var end_date = $(_this.el.end_below).val();
        $.ajax({
            dataType: 'json',
            method: 'post',
            url: adminAjaxURL + 'dashboard/data/excel',
            data: {
                country: country,
                start_date: start_date,
                end_date: end_date
            },
            success: _this.excelTotal
        });
    },
    excelTotal(response) {
        let _this = dashboard;
        var url = response['url'];
        $('#exportPDFLink').attr('href', url);
        $('#exportPDFLink').attr('download', '');
        $('#exportPDFLink')[0].click();
        console.log(response);
    },
    historyExcel() {
        var _this = this;
        let branch = $(_this.el.branch_id_loan).val();
        let client = $(_this.el.client_id_loan).val();
        let user = $(_this.el.user_id_loan).val();
        let start_loan = $(_this.el.start_loan).val();
        let end_loan = $(_this.el.end_loan).val();
        $.ajax({
            dataType: 'json',
            method: 'post',
            url: adminAjaxURL + 'dashboard/history/excel',
            data: {
                start_date: start_loan,
                end_date: end_loan,
                branch: branch,
                client: client,
                user: user,
            },
            success: _this.historyExcelSuccess
        });
    },
    historyExcelSuccess(response) {
        let _this = dashboard;
        var url = response['url'];
        $('#exportPDFLink').attr('href', url);
        $('#exportPDFLink').attr('download', '');
        $('#exportPDFLink')[0].click();
        console.log(response);
    },
    getCashExcelSheet() {
        var _this = this;
        var country = $(_this.el.country).val();
        var start_date = $(_this.el.start_cash).val();
        var end_date = $(_this.el.end_cash).val();
        var branch = $(_this.el.branch).val();
        var transaction = $(_this.el.transaction).val();
        var credit = $(_this.el.credit).val();
        var user = $(_this.el.user).val();
        if (branch != '') {
            $.ajax({
                dataType: 'json',
                method: 'post',
                url: adminAjaxURL + 'dashboard/cash-data/pdf',
                data: {
                    country: country,
                    start_date: start_date,
                    end_date: end_date,
                    branch: branch,
                    transaction: transaction,
                    credit: credit,
                    user: user
                },
                success: _this.excelTotal
            });
        } else {

        }
    }

};