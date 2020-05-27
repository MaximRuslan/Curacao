var usersIndex = {
    data: {
        dTable: '',
        columns: [
            {data: 'id', name: 'id', visible: false},
            {data: 'firstname', name: 'users.firstname', visible: false},
            {data: 'username', name: 'users.lastname'},
            {data: 'collector_first_name', searchable: false, orderable: false},
            {data: 'id_number', name: 'users.id_number'},
            {data: 'country_name', name: 'countries.name'},
            {data: 'status_name', name: 'user_status.title'},
            {data: 'role', name: 'roles.name'},
            {data: 'is_verified', name: 'users.is_verified', searchable: false},
            {data: 'date_time', name: 'created_at', searchable: false, visible: (window.type !== undefined && window.type == 'web')},
            {data: 'wallet', name: 'wallet', 'orderable': false, searchable: false, visible: (window.type == undefined || window.type != 'web')},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],

        walletTable: '',
        wColumns: [
            {data: 'type', name: 'type'},
            {data: 'amount', name: 'amount'},
            {data: 'notes', name: 'notes'},
            {data: 'transaction_payment_date_format', name: 'transaction_payment_date', searchable: false},
            {data: 'created_at_format', name: 'created_at', 'searchable': false},
        ],
        type: '',
    },

    el: {
        datatale: "#datatable",
        walletdatatable: "#wallet-table",
        userCountryPdfModal: '#userCountryPdfModal',
        userCountryPdfForm: '#userCountryPdfForm',
        deleteUser: '.deleteUser',
        deleteUserModal: '#deleteUserModal',
        confirmDeleteUserButton: '.confirmDeleteUserButton',
    },

    init() {
        var _this = this;
        if (window.type !== undefined) {
            _this.data.type = window.type;
        }

        _this.bindUiActions();
        datePickerInit();
    },

    bindUiActions() {
        var _this = this;
        _this.data.dTable = $(_this.el.datatale).DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": adminAjaxURL + 'datatable-users',
                data: function (d) {
                    d.type = _this.data.type;
                    d.status = $('[name="status"]').val();
                }
            },
            columns: _this.data.columns,
            "drawCallback": function (settings) {
                initTooltip();
                if (_this.data.type != 'web') {
                    let options = `<option value="">All</option>`;
                    let statuses = settings.jqXHR.responseJSON.statuses;
                    for (let i in statuses) {
                        options += `<option value="${statuses[i]['id']}">${statuses[i]['title']}</option>`;
                    }
                    if ($('[name="status"]').length == 0) {
                        let str = `<div class="statusSelect2"><select name="status" class="form-control" id="status-select2" style="margin-left: 10px; width: 30% !important;">
                        ${options}  
                    </select></div>`;
                        $('.dataTables_filter').prepend(str);
                        $('[name="status"]').val('').select2();
                    } else {
                        // $('[name="status"]').html(options);
                    }
                }
            },
            order: [[0, 'desc']],
            pageLength: '50',
        });


        $(document).on('change', '[name="status"]', function () {
            _this.data.dTable.draw();
        });


        $(document).on('click', '.AddAmount', function (e) {
            e.preventDefault();
            $('#userWalletForm').data('user-id', $(this).data('id'));
            $('#userWalletForm')[0].reset();
            _this.walletTableReinit();
            $('#userWallerModal').find('#client_name').html($(this).data('name'));
            $('#userWallerModal').find('#client_id').html($(this).data('client_id'));
            if ($(this).data('balance') == '' || $(this).data('balance') == null) {
                $('#userWallerModal').find('#balance').html('0.00');
            } else {
                $('#userWallerModal').find('#balance').html($(this).data('balance'));
            }
            $('#userWallerModal').modal('show');
        });

        $(document).on('submit', '#userWalletForm', function (e) {
            e.preventDefault();
            var form = '#userWalletForm';
            $.ajax({
                dataType: 'json',
                method: 'post',
                data: $('#userWalletForm').serialize(),
                url: adminAjaxURL + 'users/' + $('#userWalletForm').data('user-id') + '/wallet',
                success: function (data) {
                    _this.data.walletTable.draw(false);
                    _this.data.dTable.draw(false);
                    var user_id = $('#userWalletForm').data('user-id');
                    $('#userWalletForm')[0].reset();
                    $('#userWalletForm').data('user-id', user_id);
                },
                error: function (jqXHR, exception) {
                    var Response = jqXHR.responseText;
                    ErrorBlock = $(form);
                    Response = $.parseJSON(Response);
                    displayErrorMessages(Response, ErrorBlock, 'input');
                }
            });
        });

        $(document).on('change blur keyup keydown', '#userWalletForm input', function (e) {
            _this.totalWalletCalculate();
        });

        $(document).on('click', '.downloadCountryPdf', function (e) {
            e.preventDefault();
            _this.getLoanTypes($(this).data('id'));
        });

        $(document).on('click', _this.el.deleteUser, function (e) {
            e.preventDefault();
            $(_this.el.deleteUserModal).find(_this.el.confirmDeleteUserButton).data('id', $(this).data('id'));
            $(_this.el.deleteUserModal).modal('show');
        });

        $(document).on('click', _this.el.deleteUserModal + ' ' + _this.el.confirmDeleteUserButton, function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'delete',
                url: adminAjaxURL + 'users/' + $(this).data('id'),
                success: function (data) {
                    _this.data.dTable.draw();
                    $(_this.el.deleteUserModal).modal('hide');
                }
            })
        });

        $(document).on('click', '.js--employee-change', function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'post',
                url: adminAjaxURL + 'assign-employee',
                data: {
                    ids: $(this).data('id'),
                    employee_id: $(this).data('user-id')
                },
                success(data) {
                    _this.data.dTable.draw(false);
                }
            })
        });
    },

    totalWalletCalculate() {
        var total_received = 0;
        $('#userWalletForm').find('[name^="amount"]').each(function (key, item) {
            if ($(item).val() != '') {
                total_received += parseFloat($(item).val());
            }
        });
        $('#userWalletForm').find("[name='transaction_total[received]']").val(total_received);
        var total_cashback = 0;
        $('#userWalletForm').find('[name^="cashback_amount"]').each(function (key, item) {
            if ($(item).val() != '') {
                total_cashback += parseFloat($(item).val());
            }
        });
        $('#userWalletForm').find('[name="transaction_total[cash_back]"]').val(total_cashback);
        $('#userWalletForm').find('[name="transaction_total[payment]"]').val(total_received - total_cashback);
    },

    walletTableReinit() {
        var _this = this;
        if (_this.data.walletTable == '') {
            _this.data.walletTable = $(_this.el.walletdatatable).DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": adminAjaxURL + 'users/datatable-wallets',
                    data: function (d) {
                        d.user_id = $('#userWalletForm').data('user-id');
                    }
                },
                order: [],
                "drawCallback": function (settings) {
                    initTooltip();
                },
                columns: _this.data.wColumns,
                pageLength: 25,
            });
        } else {
            _this.data.walletTable.draw();
        }
    },

    validateCountryPdfForm: function () {
        var _this = this;
        $(_this.el.userCountryPdfForm).data('validator', null);
        $(_this.el.userCountryPdfForm).unbind();

        validator = $(_this.el.userCountryPdfForm).validate({
            // define validation rules
            rules: {
                loan_type: {required: true},
                amount_in_words: {required: true},
                amount: {required: true},
                date: {required: true},
            },

            errorPlacement: function (error, element) {
                $('span[for="' + $(element).attr('name') + '"]').html(error);
                $(element).removeClass('error');
            },

            submitHandler: function (form) {
                fullLoader.on();
                $.ajax({
                    dataType: 'json',
                    method: 'post',
                    url: adminAjaxURL + 'users/country-pdf',
                    data: $(form).serialize(),
                    success: function (data) {
                        fullLoader.off();
                        $(_this.el.userCountryPdfModal).modal('hide');
                        window.open(data['url'], '_blank');
                    }
                })
            }
        });
    },

    getLoanTypes(id) {
        let _this = this;
        $.ajax({
            dataType: 'json',
            method: 'get',
            url: adminAjaxURL + 'users/' + id + '/loan-types',
            success: function (data) {
                let str = `<option value="">Select Type</option>`;
                for (let i in data) {
                    str += `<option value="${i}">${data[i]}</option>`
                }
                $(_this.el.userCountryPdfForm).find('[name="loan_type"]').html(str);
                $(_this.el.userCountryPdfForm).find('[name="loan_type"]').select2();
                $(_this.el.userCountryPdfModal).find('[name="id"]').val(id);
                $(_this.el.userCountryPdfForm)[0].reset();
                _this.validateCountryPdfForm();
                $(_this.el.userCountryPdfModal).modal('show');
            }
        });
    }
};