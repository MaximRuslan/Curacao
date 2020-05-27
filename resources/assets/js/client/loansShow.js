var loansShow = {
    el: {
        dTable: '',
        dataTable: '#datatable',
    },
    data: {},
    init: function () {
        var _this = this;
        _this.bindUiActions();
    },
    bindUiActions: function () {
        var _this = this;
        _this.el.dTable = $(_this.el.dataTable).DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": clientAjaxURL + "datatable-transactions/" + loan_id,
            },
            columns: [
                {data: 'id', name: 'loan_transactions.id', visible: false},
                {data: 'transaction_type', name: 'transaction_types.title'},
                {data: 'transaction_type', name: 'transaction_types.title_es', visible: false},
                {data: 'transaction_type', name: 'transaction_types.title_nl', visible: false},
                {data: 'payment_type', name: 'loan_transactions.payment_type', searchable: false},
                {data: 'notes', name: 'loan_transactions.notes'},
                {data: 'amount', name: 'loan_transactions.amount'},
                {data: 'cash_back_amount', name: 'loan_transactions.cash_back_amount'},
                {data: 'payment_date', name: 'loan_transactions.payment_date'},
                {data: 'created_at', name: 'loan_transactions.created_at'},
            ],
            "language": {
                "lengthMenu": keywords.Show + " _MENU_ " + keywords.Entries,
                "zeroRecords": keywords.NoMatchingRecordsFound,
                "info": keywords.Showing + ('_END_' == 0 ? 0 : " _START_ ") + keywords.To + " _END_ " + keywords.Of + " _TOTAL_ " + keywords.Entries,
                "infoEmpty": keywords.Showing + " 0 " + keywords.To + " 0 " + keywords.Of + " 0 " + keywords.Entries,
                "infoFiltered": "(" + keywords.FilteredFrom + " _MAX_ " + keywords.TotalRecords + ")",
                "search": keywords.Search,
                "paginate": {
                    "previous": keywords.Previous,
                    "next": keywords.Next,
                },
            },
            "pageLength": 50,
            "order": [[0, 'desc']],
            "drawCallback": function (settings) {
                initTooltip();
            },
        });
    },
};