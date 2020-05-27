var merchantsPayment = {
    data: {
        dTable: '',
        columns: [
            {data: 'country', name: 'countries.name'},
            {data: 'created_by', name: 'merchants.first_name'},
            {data: 'created_by', name: 'merchants.last_name', visible: false},
            {data: 'created_by', name: 'main.name', visible: false},
            {data: 'created_by', name: 'merchants.name', visible: false},
            {data: 'branch', name: 'merchant_branches.name'},
            {data: 'month', searchable: false, orderable: false},
            {data: 'collected_amount', searchable: false, orderable: false},
            {data: 'commission', searchable: false, orderable: false},
            {data: 'action', searchable: false, orderable: false},
        ],
        dtTable: '',
        transactions_columns: [
            {data: 'client', name: 'users.firstname'},
            {data: 'client', name: 'users.lastname', visible: false},
            {data: 'loan_id', name: 'loan_transactions.loan_id'},
            {data: 'amount', name: 'loan_transactions.amount'},
            {data: 'created_at', name: 'loan_transactions.created_at', searchable: false},
            {data: 'received_by', name: 'merchants.first_name'},
            {data: 'received_by', name: 'merchants.last_name', visible: false},
            {data: 'received_by', name: 'main.name', visible: false},
        ]
    },
    el: {
        datatale: "#datatable",
        start_below: '#start_date_below',
        end_below: '#end_date_below',
        transactions_button: '.js--transaction-show',
        transactionsDatatable: '#js--transactions-datatable'
    },
    init() {
        var _this = this;
        _this.bindUiActions();
        datePickerInit();
        _this.setStartDate($('#start_month').val());
    },
    bindUiActions() {
        var _this = this;
        _this.data.dTable = $(_this.el.datatale).DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": adminAjaxURL + 'datatable-merchants-payments',
                data: function (d) {
                    d.start_month = $('#start_month').val();
                    d.end_month = $('#end_month').val();
                    d.search_custom = $('#search').val();
                }
            },
            columns: _this.data.columns,
            "drawCallback": function (settings) {
                initTooltip();
            },
            order: [[0, 'desc']],
            pageLength: '50',
            "bFilter": false,
            "lengthMenu": [50, 100, 250]
        });

        $(document).on('change', '#start_month,#end_month', function (e) {
            _this.data.dTable.draw();
        });
        $(document).on('blur keyup', '#search', function (e) {
            _this.data.dTable.draw();
        });
        $(document).on('click', '#js--export-excel', function () {
            $.ajax({
                dataType: 'json',
                method: 'post',
                url: adminAjaxURL + "payments/export",
                data: {
                    search_custom: $('#search').val(),
                    start_month: $('#start_month').val(),
                    end_month: $('#end_month').val()
                },
                success: function (data) {
                    window.open(data['url']);
                }
            });
        });
        $(document).on('change', '#start_month', function () {
            _this.setStartDate($(this).val());
        });

        $(document).on('click', _this.el.transactions_button, function (e) {
            e.preventDefault();
            _this.showTransactions($(this).data('merchant'), $(this).data('branch'), $(this).data('start'), $(this).data('end'));
        });
    },
    setStartDate(date) {
        let parts = date.split('/');
        date = new Date(parts[1] + '/' + parts[0] + '/' + parts[2]);
        $("#end_month").datepicker('setStartDate', date);
    },
    showTransactions(merchant, branch, start, end) {
        let _this = this;
        if (_this.data.dtTable != '') {
            _this.data.dtTable.destroy();
        }
        _this.data.dtTable = $(_this.el.transactionsDatatable).DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": adminAjaxURL + 'datatable-merchant-transactions',
                data: function (d) {
                    d.merchant_id = merchant;
                    d.branch_id = branch;
                    d.start_date = start;
                    d.end_date = end;
                }
            },
            columns: _this.data.transactions_columns,
            "drawCallback": function (settings) {
                initTooltip();
            },
            order: [[0, 'desc']],
            pageLength: '10',
        });
        $('#js--transactions-modal').modal('show');
    }
};