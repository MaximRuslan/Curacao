var credit = {
    el: {
        form: '#credit_form',
        addButton: ".addCredit",
        editButton: ".editCredit",
        deleteButton: ".deleteCredit",
        modal: '#creditModal',
        deleteCreditModal: '#deleteCreditModal',
        confirmDeleteCreditButton: '.confirmDeleteCreditButton',
    },
    data: {
        datatable: '',
        status: '',
        type: '',
    },
    init() {
        var _this = this;
        _this.data.status = window.status;
        _this.data.type = window.type;
        $('#users_select').select2();
        $('#payment_type_select').select2();
        $('#bank_id').select2();
        $('#branch_selection_id').select2();
        _this.validationForm(adminAjaxURL + 'credits', 'post');
        _this.bindUiActions();
    },
    bindUiActions() {
        var _this = this;
        _this.data.datatable = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": adminAjaxURL + 'datatable-credits',
                "data": function (d) {
                    d.branch_id = $('#branch_id').val();
                    d.status = _this.data.status;
                    d.type = _this.data.type;
                }
            },
            columns: [
                {data: 'updated_at', name: 'credits.updated_at', visible: false},
                {
                    data: 'credit_select',
                    searchable: false,
                    orderable: false,
                    className: 'forCheckbox',
                    visible: (_this.data.status == 1 || _this.data.status == 2)
                },
                {data: 'user_name', name: 'users.firstname'},
                {data: 'user_name', name: 'users.lastname', visible: false},
                {data: 'payment_type', searchable: false, orderable: false, visible: _this.data.type == ''},
                {data: 'amount', name: 'credits.amount'},
                {data: 'bank_name', name: 'banks.name', visible: _this.data.type == 2},
                {data: 'account_number', name: 'user_banks.account_number', visible: _this.data.type == 2},
                {data: 'transaction_charge', name: 'transaction_charge', visible: _this.data.type == 2},
                {data: 'branch_name', name: 'branches.title', visible: _this.data.type == 2},
                {data: 'notes', name: 'credits.notes'},
                {data: 'status', name: 'notes', orderable: false, searchable: false},
                {data: 'created_at', name: 'credits.created_at', searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            order: [[0, 'desc']],
            drawCallback(settings) {
                initTooltip();
                if (_this.data.status == 1 || _this.data.status == 2) {
                    $('.custom-buttons').remove();
                    $('#datatable_length').append(
                        '                                <button class="btn btn-primary custom-buttons" id="export_csv">CSV</button>');
                    // $('#datatable_length').append(' <div class="text-left custom-buttons">\n' +
                    //     '                                <button class="btn btn-primary" id="select_all">Select All</button>\n' +
                    //     '                                <button class="btn btn-danger" id="deselect_all">Deselect All</button>\n' +
                    //     '                            </div>');
                    var str = '';
                    if (_this.data.status == 1) {
                        if (_this.data.type == 2) {
                            str += '<button class="custom-buttons btn btn-primary inprocess_selected">In Process</button>&nbsp;';
                        } else if (_this.data.type == 1) {
                            str += '<button class="custom-buttons btn btn-primary approved_selected">Approve</button>&nbsp;';
                        }
                    }
                    if (_this.data.status == 2 && _this.data.type == 2) {
                        str += '<button class="custom-buttons btn btn-primary complete_selected">Complete</button>&nbsp;';
                    }
                    if (_this.data.status == 1 || _this.data.status == 2) {
                        str += '<button class="custom-buttons btn btn-danger reject_selected">Reject</button>&nbsp;';
                    }
                    str += '';
                    // $('#datatable_filter').addClass('row');
                    $('#datatable_filter').prepend(str);
                }
            },
        });

        $(document).on('click', '#export_csv', function (e) {
            e.preventDefault();
            var values = [];
            $('.creditCheckbox:checked').each(function (key, item) {
                values.push($(item).val());
            });
            $.ajax({
                dataType: 'json',
                method: 'post',
                url: adminAjaxURL + 'credits/csv',
                data: {
                    values: values,
                    status: _this.data.status,
                    type: _this.data.type
                },
                success: function (data) {
                    if (data['url'] != undefined && data['url'] != '') {
                        var url = data['url'];
                        window.open(url, '_blank');
                    }
                }
            });
        });

        $(document).on('click', _this.el.addButton, function (e) {
            e.preventDefault();
            _this.formReset();
            $(_this.el.modal).modal('show');
        });

        $(document).on('click', _this.el.editButton, function (e) {
            e.preventDefault();
            _this.formReset();
            _this.setEdit($(this).data('id'), $(this).data('type'));
        });

        $(document).on('click', _this.el.deleteButton, function (e) {
            e.preventDefault();
            $(_this.el.deleteCreditModal).find(_this.el.confirmDeleteCreditButton).data('id', $(this).data('id'));
            $(_this.el.deleteCreditModal).modal('show');
        });

        $(document).on('click', _this.el.deleteCreditModal + ' ' + _this.el.confirmDeleteCreditButton, function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'delete',
                url: adminAjaxURL + 'credits/' + $(this).data('id'),
                success: function (data) {
                    _this.data.datatable.draw();
                    $(_this.el.deleteCreditModal).modal('hide');
                }
            })
        });

        $(document).on('change', '#branch_id', function (e) {
            e.preventDefault();
            _this.data.datatable.draw();
        });

        $(document).on('change', '#users_select', function () {
            _this.usersBanks($(this).val());
            _this.userWallet($(this).val());
        });
        $(document).on('change', '#bank_id,#credit_amount', function () {
            _this.transaction_charge_calculate();
        });

        $(document).on('change', '#payment_type_select', function (e) {
            e.preventDefault();
            _this.payment_type_change();
        });

        $(document).on('click', '.rejectCredit', function (e) {
            e.preventDefault();
            $('#credit_status_form')[0].reset();
            $('#credit_status_form').find('[name="id"]').val([$(this).data('id')]);
            $('#credit_status_form').find('[name="status"]').val(4);
            $('#creditStatusModal').find('.statusChange').html('Reject');
            $('#creditStatusModal').modal('show');
        });
        $(document).on('click', '.approveCredit', function (e) {
            e.preventDefault();
            $('#credit_status_form')[0].reset();
            $('#credit_status_form').find('[name="id"]').val([$(this).data('id')]);
            $('#credit_status_form').find('[name="status"]').val(2);
            $('#creditStatusModal').find('.statusChange').html('Approve');
            $('#creditStatusModal').modal('show');
        });
        $(document).on('click', '.inprocessCredit', function (e) {
            e.preventDefault();
            $('#credit_status_form')[0].reset();
            $('#credit_status_form').find('[name="id"]').val([$(this).data('id')]);
            $('#credit_status_form').find('[name="status"]').val(2);
            $('#creditStatusModal').find('.statusChange').html('In Process');
            $('#creditStatusModal').modal('show');
        });

        $(document).on('click', '.completeCredit', function (e) {
            e.preventDefault();
            $('#cashpayoutWalletForm')[0].reset();
            $('#cashpayoutWalletForm').find('[name="id"]').val([$(this).data('id')]);
            $('#cashpayoutWallet').find('input').prop('readonly', false);
            $('#cashpayoutWalletForm').find('.total_payment_amount_error').html('');
            $('#cashpayoutWallet').modal('show');
        });

        $(document).on('click', '.viewWalletDetails', function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'get',
                url: adminAjaxURL + 'credits/' + $(this).data('id') + '/wallet',
                success: function (data) {
                    $('#cashpayoutWalletForm')[0].reset();
                    for (var index in data['amount']) {
                        $('#cashpayoutWalletForm').find('[name="payment_amount[' + index + ']"]').val(0 - parseFloat(data['amount'][index]));
                    }

                    for (var index in data['cashback_amount']) {
                        $('#cashpayoutWalletForm').find('[name="cashback_amount[' + index + ']"]').val(parseFloat(data['cashback_amount'][index]));
                    }
                    _this.totalWalletCalculate();
                    $('#cashpayoutWallet').find('input').prop('readonly', true);
                    $('#cashpayoutWallet').modal('show');
                }
            })
        });

        $(document).on('change', '#select_all_checkbox', function (e) {
            e.preventDefault();
            if ($('#select_all_checkbox:checked').val() == 1) {
                $('.creditCheckbox').prop('checked', true);
            } else {
                $('.creditCheckbox').prop('checked', false);
            }
        });

        $(document).on('click', '#select_all', function () {
            $('.creditCheckbox').prop('checked', true);
        });
        $(document).on('click', '#deselect_all', function () {
            $('.creditCheckbox').prop('checked', false);
        });

        $(document).on('click', '.inprocess_selected', function (e) {
            e.preventDefault();
            $('#credit_status_form')[0].reset();
            var values = [];
            $('.creditCheckbox:checked').each(function (key, item) {
                values.push($(item).val());
            });
            $('#credit_status_form').find('[name="id"]').val(values);
            $('#credit_status_form').find('[name="status"]').val(2);
            $('#creditStatusModal').find('.statusChange').html('In Process');
            $('#creditStatusModal').modal('show');
        });
        $(document).on('click', '.approved_selected', function (e) {
            e.preventDefault();
            $('#credit_status_form')[0].reset();
            var values = [];
            $('.creditCheckbox:checked').each(function (key, item) {
                values.push($(item).val());
            });
            $('#credit_status_form').find('[name="id"]').val(values);
            $('#credit_status_form').find('[name="status"]').val(2);
            $('#creditStatusModal').find('.statusChange').html('Approve');
            $('#creditStatusModal').modal('show');
        });
        $(document).on('click', '.complete_selected', function (e) {
            e.preventDefault();
            $('#credit_status_form')[0].reset();
            var values = [];
            $('.creditCheckbox:checked').each(function (key, item) {
                values.push($(item).val());
            });
            $('#credit_status_form').find('[name="id"]').val(values);
            $('#credit_status_form').find('[name="status"]').val(3);
            $('#creditStatusModal').find('.statusChange').html('Complete');
            $('#creditStatusModal').modal('show');
        });
        $(document).on('click', '.reject_selected', function (e) {
            e.preventDefault();
            $('#credit_status_form')[0].reset();
            var values = [];
            $('.creditCheckbox:checked').each(function (key, item) {
                values.push($(item).val());
            });
            $('#credit_status_form').find('[name="id"]').val(values);
            $('#credit_status_form').find('[name="status"]').val(4);
            $('#creditStatusModal').find('.statusChange').html('Reject');
            $('#creditStatusModal').modal('show');
        });


        $(document).on('submit', '#cashpayoutWalletForm', function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'post',
                url: adminAjaxURL + 'credits/status',
                data: $('#cashpayoutWalletForm').serialize(),
                success: function (data) {
                    if (data['status']) {
                        if (data['url'] != undefined && data['url'] != '') {
                            var url = data['url'];
                            window.open(url, '_blank');
                        }
                        _this.data.datatable.draw();
                        $('#cashpayoutWallet').modal('hide');
                    } else {
                        swal({
                            'text': data['message']
                        });
                    }
                }
            });
        });

        $(document).on('submit', '#credit_status_form', function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'post',
                url: adminAjaxURL + 'credits/status',
                data: $('#credit_status_form').serialize(),
                success: function (data) {
                    if (data['url'] != undefined && data['url'] != '') {
                        var url = data['url'];
                        window.open(url, '_blank');
                    }
                    _this.data.datatable.draw();
                    $('#creditStatusModal').modal('hide');
                }
            });
        });

        $(document).on('click', '.statusHistory', function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'get',
                url: adminAjaxURL + 'credits/' + $(this).data('id') + '/history',
                success: function (data) {
                    str = '';
                    for (var index in data['history']) {
                        var history = data['history'][index];
                        if (history['notes'] == null) {
                            history['notes'] = '';
                        }
                        str += '<tr>' +
                            '<td>' + history['user_name'] + '</td>' +
                            '<td>' + history['status'] + '</td>' +
                            '<td>' + history['notes'] + '</td>' +
                            '<td>' + history['date'] + '</td>' +
                            '</tr>';
                    }
                    $('#statusHistoryModal').find('#statusHistory').html(str);
                    $('#statusHistoryModal').modal('show');
                }
            });
        });

        $(document).on('change blur keyup keydown', '#cashpayoutWalletForm input', function (e) {
            _this.totalWalletCalculate();
        });

    },
    formReset() {
        var _this = this;
        $(_this.el.form).find('[name="id"]').val('');
        $(_this.el.form).find('input').prop('disabled', false);
        $(_this.el.form).find('select').prop('disabled', false);
        $(_this.el.form).find('button[type="submit"]').prop('disabled', false);
        $('#wallet').html('');
        $('#available_wallet').html('');
        $("#walletVal").val('');
        $('#users_select').val('').select2();
        $('#payment_type_select').val('').select2();
        $('#bank_id').val('').select2();
        $('#branch_selection_id').val('').select2();
        $(_this.el.form).find('button[type="submit"]').show();
        $(_this.el.form)[0].reset();
    },
    setEdit(id, type) {
        var _this = this;
        $.ajax({
            dataType: 'json',
            method: 'get',
            url: adminAjaxURL + 'credits/' + id + '/edit',
            success(data) {
                str = '<option>Bank</option>';
                for (var index in data['banks']) {
                    var tax_transaction = 0;
                    if (data['banks'][index]['tax_transaction'] != null) {
                        tax_transaction = data['banks'][index]['tax_transaction'];
                    }
                    str += '<option value="' + data['banks'][index]['id'] + '" data-transaction-type="' + data['banks'][index]['transaction_fee_type'] + '" data-transaction-amount="' + (parseFloat(data['banks'][index]['transaction_fee']) + parseFloat(tax_transaction)) + '">' + data['banks'][index]['name'] + '-' + data['banks'][index]['account_number'] + '</option>';
                }
                $('#bank_id').html(str);
                str = '<option>Branch</option>';
                for (var index in data['branches']) {
                    str += '<option value="' + index + '">' + data['branches'][index] + '</option>';
                }
                $('#branch_selection_id').html(str);
                $('#wallet').html('<label>Wallet Balance: ' + data['wallet'] + '</label>');
                $('#available_wallet').html('<label>Available Balance: ' + data['available_balance'] + '</label>');
                $("#walletVal").val(data['wallet']);
                setForm(_this.el.form, data['inputs']);
                _this.payment_type_change();
                if (type == 'view') {
                    $(_this.el.form).find('input').prop('disabled', true);
                    $(_this.el.form).find('button[type="submit"]').hide();
                }
                $(_this.el.modal).modal('show');
            }
        })
    },
    validationForm(url, method) {
        var _this = this;
        $(_this.el.form).data('validator', null);
        $(_this.el.form).unbind();

        validator = $(_this.el.form).validate({
            rules: {
                'user_id': {required: true},
                'payment_type': {required: true},
                'amount': {required: true},
            },

            errorPlacement(error, element) {
                $('span[for="' + $(element).attr('name') + '"]').html(error);
                $(element).removeClass('error');
            },

            submitHandler(form) {
                $(_this.el.form).find('button[type="submit"]').prop('disabled', true);
                $.ajax({
                    dataType: 'json',
                    method: method,
                    url: url,
                    data: $(form).serialize(),
                    success: function (data) {
                        _this.data.datatable.draw();
                        $(_this.el.modal).modal('hide');
                    }
                });
            }
        });
    },
    usersBanks(user_id) {
        $.ajax({
            dataType: 'json',
            method: 'get',
            url: adminAjaxURL + "users/" + user_id + '/banks',
            success: function (data) {
                str = '<option value="">Bank</option>';
                for (var index in data['banks']) {
                    var tax_transaction = 0;
                    if (data['banks'][index]['tax_transaction'] != null) {
                        tax_transaction = data['banks'][index]['tax_transaction'];
                    }
                    if ($('#bank_id').val() != "") {
                        str += '<option value="' + data['banks'][index]['id'] + '" data-transaction-type="' + data['banks'][index]['transaction_fee_type'] + '" data-transaction-amount="' + (parseFloat(data['banks'][index]['transaction_fee']) + parseFloat(tax_transaction)) + '"';
                        if (data['banks'][index]['id'] == $('#bank_id').val()) {
                            str += ' selected';
                        }
                        str += '>' + data['banks_data'][data['banks'][index]['bank_id']] + " - " + data['banks'][index]['account_number'] + '</option>';
                    } else {
                        str += '<option value="' + data['banks'][index]['id'] + '" data-transaction-type="' + data['banks'][index]['transaction_fee_type'] + '" data-transaction-amount="' + (parseFloat(data['banks'][index]['transaction_fee']) + parseFloat(tax_transaction)) + '">' + data['banks_data'][data['banks'][index]['bank_id']] + " - " + data['banks'][index]['account_number'] + '</option>';
                    }
                }
                $('#bank_id').html(str);
                str = '<option value="">Branch</option>';
                for (var index in data['branches']) {
                    str += '<option value="' + index + '">' + data['branches'][index] + '</option>';
                }
                $('#branch_selection_id').html(str);
            }
        });
    },
    userWallet(user_id) {
        $.ajax({
            dataType: 'json',
            method: 'post',
            url: adminAjaxURL + "users/" + user_id + '/wallet-total',
            success: function (data) {
                $('#wallet').html('<label>Wallet Balance: ' + data['wallet'] + '</label>');
                $('#available_wallet').html('<label>Available Balance: ' + data['available_balance'] + '</label>');
                $("#walletVal").val(data['wallet']);
            }
        });
    },
    transaction_charge_calculate() {
        var amount = 0;
        if ($('#credit_amount').val() != '') {
            amount = parseFloat($('#credit_amount').val());
        }

        var transaction_type = $('#bank_id option[value="' + $('#bank_id').val() + '"]').data('transaction-type');
        var transaction_fee = $('#bank_id option[value="' + $('#bank_id').val() + '"]').data('transaction-amount');
        var transaction_charge = 0;
        if (transaction_type == 1) {
            transaction_charge = amount * transaction_fee / 100;
        } else if (transaction_type == 2) {
            transaction_charge = transaction_fee;
        }
        $('#transaction_charge').val(transaction_charge);
    },
    payment_type_change() {
        // 'bank_id': {required: $('payment_type_select').val() == 2},
        // 'transaction_charge': {required: $('payment_type_select').val() == 2},
        // 'branch_id': {required: $('payment_type_select').val() == 2},
        if ($('#payment_type_select').val() == 1) {
            $('.bank_div').hide();
            $('.branch_div').show();
            $('#branch_selection_id').rules('add', {
                required: true
            });
            $('[name="bank_id"]').rules('remove', 'required');
            $('[name="transaction_charge"]').rules('remove', 'required');
        } else if ($('#payment_type_select').val() == 2) {
            $('.bank_div').show();
            $('.branch_div').hide();
            $('[name="bank_id"]').rules('add', {
                required: true,
            });
            $('[name="transaction_charge"]').rules('add', {
                required: true,
            });
            $('#branch_selection_id').rules('remove', 'required');
        } else {
            $('.bank_div').hide();
            $('.branch_div').hide();
            $('[name="bank_id"]').rules('remove', 'required');
            $('[name="transaction_charge"]').rules('remove', 'required');
            $('#branch_selection_id').rules('remove', 'required');
        }
    },
    totalWalletCalculate() {
        var total_received = 0;
        $('#cashpayoutWalletForm').find('[name^="payment_amount"]').each(function (key, item) {
            if ($(item).val() != '') {
                total_received += parseFloat($(item).val());
            }
        });
        $('#cashpayoutWalletForm').find("[name='transaction_total[received]']").val(total_received);
        var total_cashback = 0;
        $('#cashpayoutWalletForm').find('[name^="cashback_amount"]').each(function (key, item) {
            if ($(item).val() != '') {
                total_cashback += parseFloat($(item).val());
            }
        });
        $('#cashpayoutWalletForm').find('[name="transaction_total[cash_back]"]').val(total_cashback);
        $('#cashpayoutWalletForm').find('[name="transaction_total[payment]"]').val(total_received - total_cashback);
    }


};