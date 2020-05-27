var loanCalculation = {
    el: {},
    data: {
        admin: '',
        loan_id: '',
        receipt_on: false,
    },
    init() {
        var _this = this;
        if (window.admin != undefined) {
            _this.data.admin = window.admin;
        }
        _this.data.receipt_on = window.receipt_on;
        _this.data.loan_id = window.loan_id;
        datePickerInit();
        _this.bindUiActions();
        _this.refreshTable();
    },
    bindUiActions() {
        var _this = this;
        $(document).on('click', '#addNewHistory', function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'get',
                data: {
                    newEntry: 1
                },
                url: adminAjaxURL + 'loans/' + _this.data.loan_id + '/history',
                success: function (data) {
                    _this.refreshTable();
                }
            });
        });
        $(document).on('click', '.editHistory', function (e) {
            var id = $(this).data('id');
            _this.editCalculation(id);
        });
        $(document).on('click', '.deleteHistory', function (e) {
            var id = $(this).data('id');
            _this.deleteCalculation(id);
        });
        $(document).on('change blur keyup', '#editCalculationForm input', function () {
            _this.total_calculate();
        });

        $(document).on('submit', '#editCalculationForm', function (e) {
            e.preventDefault();
            $('#editCalculationModal').find('.js--submit-button').attr('disabled', true);
            $('#common_error').html('');
            $('#common_error').hide();
            if (parseFloat($('[name="transaction_total[payment]"]').val()) <= parseFloat($('[name="open_balance"]').val())) {
                $.ajax({
                    dataType: 'json',
                    url: adminAjaxURL + 'history/' + $(this).find('[name="history_id"]').val(),
                    method: 'post',
                    data: $(this).serialize(),
                    success: function (data) {
                            $('#editCalculationModal').modal('hide');
                            _this.refreshTable();
                    },
                    error: function (jqXHR) {
                        var Response = jqXHR.responseText;
                        ErrorBlock = $('#editCalculationForm');
                        Response = $.parseJSON(Response);
                        displayErrorMessages(Response, ErrorBlock, 'input');
                        $('#editCalculationModal').find('.js--submit-button').attr('disabled', false);
                    }
                });
            } else {
                $('#common_error').html('Total amount should be less than open balance.');
                $('#common_error').show();
                $('#editCalculationModal').find('.js--submit-button').attr('disabled', false);
            }
        });
        $(document).on('click', '.paymentReceipt', function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'post',
                url: adminAjaxURL + 'history/' + $(this).data('id') + '/receipt',
                success: function (data) {
                    window.open(data['receipt_pdf'], 'download');
                }
            });
        });
        $(document).on('click', '.showLoanHistory', function (e) {
            e.preventDefault();
            $('#addNewHistory').data('id', loan_id);
        });
    },
    refreshTable() {
        var _this = this;
        $.ajax({
            dataType: 'json',
            method: 'get',
            url: adminAjaxURL + 'loans/' + _this.data.loan_id + '/history',
            success: function (data) {
                str = '';
                for (var index in data['history']) {
                    var history = data['history'][index];
                    var button = '';
                    if (_this.data.admin == true) {
                        if (history['payment_amount'] != '') {
                            if (_this.data.admin == true) {
                                button += `<button class="btn btn-danger deleteHistory" data-id="${history['id']}">
                                                <i class="fa fa-trash"></i>
                                           </button>
                                            <button class="btn btn-primary editHistory" data-id="${history['id']}">
                                                <i class="fa fa-pencil"></i>
                                           </button>`;
                            }
                        }
                    }
                    if (history['payment_amount'] != '' && _this.data.receipt_on == true) {
                        button += '   <button class="btn btn-primary paymentReceipt" data-id="' + history['id'] + '">' +
                            '<i class="fa fa-file-pdf-o"></i>' +
                            '</button>';
                    }
                    if (history['debt_collection_value'] == null) {
                        history['debt_collection_value'] = '0.00';
                    }
                    if (history['debt_collection_tax'] == null) {
                        history['debt_collection_tax'] = '0.00';
                    }
                    if (history['transaction_name'] != null && history['transaction_name'] != '') {
                        history['transaction_name'] = '<span data-trigger="hover" data-container="body" title="" data-toggle="popover" data-placement="top" data-content="' + history['user_info'] + '" data-original-title="" class="label label-info">' + history['transaction_name'] + '</span>';
                    }
                    str += '<tr>' +
                        '<td>' + history['week_iterations'] + '</td>' +
                        '<td>' + history['date'] + '</td>' +
                        '<td>' + history['transaction_name'] + '</td>' +
                        '<td>' + history['payment_amount'] + '</td>' +
                        '<td>' + history['principal'] + '</td>' +
                        '<td>' + history['origination'] + '</td>' +
                        '<td>' + history['interest'] + '</td>' +
                        '<td>' + history['renewal'] + '</td>' +
                        '<td>' + history['tax'] + '</td>' +
                        '<td>' + history['debt_collection_value'] + '</td>' +
                        '<td>' + history['debt_collection_tax'] + '</td>' +
                        '<td>' + history['admin_fees_tax'] + '</td>' +
                        // '<td>' + history['total_e_tax'] + '</td>' +
                        '<td>' + history['total'] + '</td>' +
                        '<td>' + history['collector'] + '</td>';

                    str += '<td>' + button + '</td>';
                    str += '</tr>';
                }
                $('.loan_history_table').html(str);
                $('[data-toggle="popover"]').popover();
            }
        });
    },
    total_calculate() {
        var sum = 0;
        $('#editCalculationForm input[name^="payment_amount"]').each(function (key, item) {
            if ($(item).val() != '') {
                sum = sum + parseFloat($(item).val());
            }
        });
        var cashback = 0;
        $('#editCalculationForm input[name^="cashback_amount"]').each(function (key, item) {
            if ($(item).val() != '') {
                cashback = cashback + parseFloat($(item).val());
            }
        });
        $('#editCalculationForm input[name="transaction_total[received]"]').val(sum);
        $('#editCalculationForm input[name="transaction_total[cash_back]"]').val(cashback);
        $('#editCalculationForm input[name="transaction_total[payment]"]').val(round2(sum - cashback));

        var tax = parseFloat($('#editCalculationForm input[name="transaction[tax_for_renewal]"]').val()) + parseFloat($('#editCalculationForm input[name="transaction[tax_for_interest]"]').val());
        $('#editCalculationForm input[name="transaction[tax]"]').val(tax.toFixed(2));

        var total = parseFloat($('#editCalculationForm input[name="transaction[principal]"]').val()) + parseFloat($('#editCalculationForm input[name="transaction[interest]"]').val()) +
            parseFloat($('#editCalculationForm input[name="transaction[tax_for_interest]"]').val()) + parseFloat($('#editCalculationForm input[name="transaction[renewal]"]').val()) +
            parseFloat($('#editCalculationForm input[name="transaction[tax_for_renewal]"]').val()) + parseFloat($('#editCalculationForm input[name="transaction[debt]"]').val()) +
            parseFloat($('#editCalculationForm input[name="transaction[debt_tax]"]').val()) + parseFloat($('#editCalculationForm input[name="transaction[debt_collection_value]"]').val()) +
            parseFloat($('#editCalculationForm input[name="transaction[debt_collection_tax]"]').val());
        $('#editCalculationForm input[name="transaction[total]"]').val(total.toFixed(2));
    },
    editCalculation(id) {
        var _this = this;
        $('#editCalculationForm').find('[name="history_id"]').val(id);
        $.ajax({
            dataType: 'json',
            method: 'get',
            url: adminAjaxURL + 'history/' + id + '/edit',
            success: function (data) {
                var currentDate = data['history']['date'];
                var startDate = new Date(data['first_history']['date']);
                var endDate = new Date(data['last_history']['date']);
                $('#editCalculationForm').find('[name^="payment_amount"]').each(function (key, item) {
                    $(item).val(0);
                });
                $('#editCalculationForm').find('[name^="cashback_amount"]').each(function (key, item) {
                    $(item).val(0);
                });
                var payments = data['payments'];
                for (var index in payments) {
                    var amount = 0;
                    if (payments[index]['amount'] > 0) {
                        amount = payments[index]['amount'];
                    }
                    $('#editCalculationForm').find('[name="payment_amount[' + payments[index]['payment_type'] + ']"]').val(amount);
                }
                for (var index in payments) {
                    var amount = 0;
                    if (payments[index]['cash_back_amount'] > 0) {
                        amount = payments[index]['cash_back_amount'];
                    }
                    $('#editCalculationForm').find('[name="cashback_amount[' + payments[index]['payment_type'] + ']"]').val(amount);
                }
                $('#editCalculationForm').find('[name="open_balance"]').val(data['before']['total']);
                $('#editCalculationForm').find('[name="date"]').val(moment(currentDate).format('DD/MM/YYYY'));
                $('#editCalculationForm').find('[name="date"]').datepicker("setStartDate", startDate);
                $('#editCalculationForm').find('[name="date"]').datepicker("setEndDate", endDate);
                $('#editCalculationForm').find('[name="notes"]').val('');
                if (payments[0] != undefined) {
                    $('#editCalculationForm').find('[name="notes"]').val(payments[0]['notes']);
                }

                $('#editCalculationForm').find('[name="transaction[principal]"]').val(data['history']['principal']);
                $('#editCalculationForm').find('[name="transaction[interest]"]').val(data['history']['interest']);
                $('#editCalculationForm').find('[name="transaction[tax_for_interest]"]').val(data['history']['tax_for_interest']);
                $('#editCalculationForm').find('[name="transaction[renewal]"]').val(data['history']['renewal']);
                $('#editCalculationForm').find('[name="transaction[tax_for_renewal]"]').val(data['history']['tax_for_renewal']);
                $('#editCalculationForm').find('[name="transaction[debt]"]').val(data['history']['debt']);
                $('#editCalculationForm').find('[name="transaction[debt_tax]"]').val(data['history']['debt_tax']);
                $('#editCalculationForm').find('[name="transaction[debt_collection_value]"]').val(data['history']['debt_collection_value']);
                $('#editCalculationForm').find('[name="transaction[debt_collection_tax]"]').val(data['history']['debt_collection_tax']);

                _this.total_calculate();
                $('#editCalculationModal').modal('show');
                $('#editCalculationModal').find('.js--submit-button').attr('disabled', false);
            }
        });
    },
    deleteCalculation(id) {
        var _this = this;
        if (confirm('Are you sure you want delete this entry?')) {
            $.ajax({
                dataType: 'json',
                method: 'delete',
                url: adminAjaxURL + 'history/' + id,
                success: function (data) {
                    _this.refreshTable();
                }
            });
        }
    }
};