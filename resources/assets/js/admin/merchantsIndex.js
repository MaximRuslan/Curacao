var merchantsIndex = {
    data: {
        dTable: '',
        columns: [
            {data: 'id', name: 'id', visible: false},
            {data: 'type', name: 'merchants.type', searchable: false},
            {data: 'last_name', name: 'merchants.last_name', visible: false},
            {data: 'username', name: 'merchants.first_name'},
            {data: 'email', name: 'merchants.email'},
            {data: 'telephone', name: 'merchants.telephone'},
            {data: 'country_name', name: 'countries.name'},
            {data: 'status_name', name: 'user_status.title'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
    },
    el: {
        datatale: "#datatable",
        deleteButton: '.deleteMerchant',
        deleteMerchantModal: '#deleteMerchantModal',
        confirmDeleteMerchantButton: '.confirmDeleteMerchantButton',

    },
    init() {
        var _this = this;
        _this.bindUiActions();
        datePickerInit();
    },
    bindUiActions() {
        var _this = this;
        _this.data.dTable = $(_this.el.datatale).DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": adminAjaxURL + 'datatable-merchants',
            },
            columns: _this.data.columns,
            "drawCallback": function (settings) {
                initTooltip();
            },
            order: [[0, 'desc']],
            pageLength: '50'
        });
        $(document).on('click', _this.el.deleteButton, function (e) {
            e.preventDefault();
            $(_this.el.deleteMerchantModal).find(_this.el.confirmDeleteMerchantButton).data('id', $(this).data('id'));
            $(_this.el.deleteMerchantModal).modal('show');
        });

        $(document).on('click', _this.el.deleteMerchantModal + ' ' + _this.el.confirmDeleteMerchantButton, function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'delete',
                url: adminAjaxURL + 'merchants/' + $(this).data('id'),
                success: function (data) {
                    _this.data.dTable.draw();
                    $(_this.el.deleteMerchantModal).modal('hide');
                }
            })
        });
    }
};