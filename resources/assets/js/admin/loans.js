var loans = {
    el: {
        loansDatatable: '#datatable',
        loanApplicationModal: '#loanApplicationModal',
        loanApplicationForm: '#loanApplicationForm',
        loan_reason: '#loan_reason',
        loan_type: '#loan_type',
        amount: '#amount',
        incomeAmount: '.income-amount',
        expenseAmount: '.expense-amount',
        clearIncome: '.clearIncome',
        addNewIncome: '.addNewIncome',
        addNewOtherLoan: '.addNewOtherLoan',
        removeIncome: '.removeIncome',
        clearOther: '.clearOther',
        removeOtherLoan: '.removeOtherLoan',
        income_holder: '.income-holder',
        loanTermsCheckbox: '#loan_model_terms_checkbox',
        editLoanApplication: '.editLoanApplication',
        changeStatus: '.changeStatus',
        loanStatusChangeModal: "#loanStatusChangeModal",
        loanStatusChangeForm: '#loanStatusChangeForm',
        deleteLoan: '.deleteLoan',
        deleteLoanModal: '#deleteLoanModal',
        confirmDeleteLoanButton: '.confirmDeleteLoanButton',
        showTransaction: '.showTransaction',
        loanTransactionForm: '#transactionForm',
    },
    data: {
        lTable: '',
        lColumns: [
            {data: 'checkbox', name: 'checkbox', searchable: false, orderable: false, visible: window.assign},
            {data: 'updated_at', name: 'loan_applications.updated_at', visible: false},
            {data: 'user_first_name', name: 'users.firstname'},
            {data: 'user_first_name', name: 'users.lastname', visible: false},
            {data: 'collector_first_name', name: 'coll.firstname'},
            {data: 'collector_last_name', name: 'coll.lastname', visible: false},
            {data: 'follow_up_date', name: 'follow_up_date', searchable: false, visible: window.my_client},
            {data: 'original_due_date', name: 'original_due_date', searchable: false, visible: window.my_client},
            {data: 'user_id_number', name: 'users.id_number'},
            {data: 'id', name: 'loan_applications.id'},
            {data: 'loan_type_title', name: 'loan_types.title', visible: !window.my_client},
            {data: 'amount', name: 'loan_applications.amount'},
            {data: 'created_at', name: 'created_at', searchable: false, visible: !window.my_client},
            {data: 'start_date', name: 'start_date', searchable: false},
            {data: 'outstanding_balance', name: 'outstanding_balance', searchable: false},
            {data: 'last_payment_date', name: 'last_payment_date', searchable: false, visible: window.my_client},
            {data: 'end_date', name: 'end_date', searchable: false},
            {data: 'loan_status_title', name: 'loan_status.title'},
            {data: 'deleted_user_name', name: 'deleted_users.firstname', visible: window.status == 'deleted'},
            {data: 'deleted_user_name', name: 'deleted_users.lastname', visible: false},
            {data: 'action', name: 'action', orderable: false, searchable: false, visible: window.status != 'deleted'},
        ],
        amount_index: 0,
        signaturePad: '',
        transactionDatatable: '',
        status: false,
    },

    init() {
        var _this = this;
        if (window.clients_has_active_loans != undefined) {
            for (let i in window.clients_has_active_loans) {
                let element = window.clients_has_active_loans[i];
                $('#client_id option[value="' + element + '"]').prop('disabled', true);
            }
        }
        $('#client_id').select2();
        datePickerInit();
        initTooltip();
        _this.bindUiActions();
        $.validator.addMethod(
            "regex",
            function (value, element, regexp) {
                var re = new RegExp(regexp);
                return this.optional(element) || re.test(value);
            },
            "Add Only up to two decimal."
        );

        if (window.status == 3) {
            setInterval(function () {
                _this.data.lTable.draw(false);
            }, 5000);
        }
    },

    bindUiActions() {
        var _this = this;
        let order = [[1, 'desc']];
        if (window.my_client) {
            order = [[6, 'asc']];
        }
        _this.data.lTable = $(_this.el.loansDatatable).DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: {
                "url": adminAjaxURL + 'datatable-loans',
                data(d) {
                    d.status = window.status;
                    d.assign = window.assign;
                    d.my_client = window.my_client;
                    d.employee_id = $('#js--my-client-employee').val();
                    if ($('#js--my-client-status').length == 0 && !collector) {
                        d.my_client_status = 4;
                    } else {
                        d.my_client_status = $('#js--my-client-status').val();
                    }
                }
            },
            columns: _this.data.lColumns,
            order: order,
            pageLength: '50',
            "drawCallback"(settings) {
                initTooltip();
                $('.page-total').html('');
                $('.page-assign').html('');
                $('.dataTables_length').append('<div class="page-total"><br>Total: ' + settings.jqXHR.responseJSON.recordsTotal + '</div>');
                $('#js--assign-checkbox').prop('checked', false);
                if (window.assign) {
                    $('.dataTables_filter').append(`<div class="page-assign">
                        <button class="btn btn-default" id="js--assign-button">Assign</button>
                    </div>`);
                }
                if (window.my_client && $('#js--my-client-status').length == 0) {
                    let status_options = '<option value="">All</option>';
                    for (let index in statuses) {
                        let selected = '';
                        if (index == 4) {
                            selected = 'selected';
                        }
                        status_options += `<option ${selected} value="${index}">${statuses[index]}</option>`;
                    }
                    let employee_options = '<option value="">Select Employee</option>';
                    for (let index in employees) {
                        employee_options += `<option value="${employees[index]['id']}">${employees[index]['name']}</option>`;
                    }
                    let status_selection = `<select name="status" class="form-control" style="width:400px !important;" id="js--my-client-status">${status_options}</select>`;
                    let employee_selection = `<div class="col-md-6">
                            <select name="status" class="form-control" style="width:400px !important;" id="js--my-client-employee">${employee_options}</select>
                        </div>`;
                    let extra_div = `<div class="col-md-6"></div>`;
                    if (admin) {
                        extra_div = '';
                    } else {
                        employee_selection = '';
                    }
                    let str = `<div class="row text-left mr-2" style="flex: 0 0 66%;">
                        ${extra_div}
                        <div class="col-md-6">
                            ${status_selection}
                        </div>
                        ${employee_selection}
                    </div>`;

                    $('#datatable_filter').prepend(str);
                    $('#datatable_filter').find('label').css('flex', '0 0 34%');
                    $('#js--my-client-status').select2();
                    $('#js--my-client-employee').select2();
                    $('#datatable_filter').css('display', 'flex');
                    $('#datatable_filter').css('width', '100%');
                    $('#datatable_filter').parent().removeClass('col-md-6');
                    $('#datatable_length').parent().removeClass('col-md-6');
                    $('#datatable_filter').parent().addClass('col-md-8');
                    $('#datatable_length').parent().addClass('col-md-4');
                }
            },
            "rowCallback": function (row, data) {
                // for (let i in data.DT_RowData) {
                //     $(row).find('td:last-child').attr(i, data.DT_RowData[i]);
                // }
            }
        });

        $(document).on('change', '#js--my-client-employee, #js--my-client-status', function () {
            _this.data.lTable.draw();
        });

        $(document).on('change', '#js--assign-checkbox', function () {
            $('.js--assign').prop('checked', false);
            if ($('#js--assign-checkbox:checked').val() == 1) {
                $('.js--assign').prop('checked', true);
            }
        });
        $(document).on('click', '#js--assign-button', function () {
            let ids = [];
            $('.js--assign:checked').each(function (key, item) {
                ids.push($(item).data('id'));
            });
            if (ids.length == 0) {
                swal({
                    type: 'error',
                    text: 'Please select minimum one loan.'
                })
            } else {
                $('#js--assign-loan-form').find('[name="ids"]').val(ids);
                $('#js--assign-loan-form').find('[name="employee_id"]').val('').select2();
                $('#js--assign-loan-modal').modal('show');
            }
        });

        $(document).on('submit', '#js--assign-loan-form', function (e) {
            e.preventDefault();
            if ($(this).find('[name="employee_id"]').val() != '') {
                $.ajax({
                    dataType: "json",
                    method: 'post',
                    url: adminAjaxURL + 'assign-employee',
                    data: $('#js--assign-loan-form').serialize(),
                    success(data) {
                        _this.data.lTable.draw();
                        swal({
                            type: 'success',
                            text: 'Loans assigned successfully.'
                        });
                        $('#js--assign-loan-modal').modal('hide');
                    }
                });
            } else {
                $('#js--assign-loan-form').find('span[for="employee_id"]').text('This field is required.');
            }
        });

        $(document).on('change keyup', _this.el.amount + ',' + _this.el.incomeAmount + ',' + _this.el.expenseAmount, function (e) {
            e.preventDefault();
            _this.calculate();
        });

        $(document).on('click', '#createNewLoan', function (e) {
            e.preventDefault();
            _this.loansFormReset();
            $(_this.el.income_holder).html(_this.incomeAmountHtml('main'));
            $(_this.el.income_holder).find('[name="date_of_payment[' + _this.data.amount_index + ']"]').rules('add', {
                required: true,
            });
            $(_this.el.income_holder).find('[name="income_amount[' + _this.data.amount_index + ']"]').rules('add', {
                required: true,
                regex: /^\d{0,22}(\.\d{0,2})?$/
            });
            $(_this.el.income_holder).find('[name="income_proof_image[' + _this.data.amount_index + ']"]').rules('add', {
                required: true,
            });
            _this.data.amount_index++;
            datePickerInit();
            initTooltip();
            $(_this.el.loanApplicationModal).modal('show');
        });

        $(document).on('change', '#client_id', function (e) {
            e.preventDefault();
            _this.userMasterRelatedData($(this).val());
        });

        $(document).on('change', _this.el.loan_type, function () {
            if ($(this).val() != '' && $(this).val() != null) {
                _this.loanTypeData($(this).val(), '');
            }
        });

        $(document).on('click', _this.el.clearIncome, function (e) {
            e.preventDefault();
            _this.clearIncome($(this));
        });

        $(document).on('click', _this.el.clearOther, function (e) {
            e.preventDefault();
            _this.clearOther($(this));
        });

        $(document).on('click', _this.el.addNewIncome, function (e) {
            e.preventDefault();
            _this.addNewIncome();
        });

        $(document).on('click', _this.el.removeIncome, function (e) {
            e.preventDefault();
            _this.removeIncome($(this));
        });

        $(document).on('click', _this.el.removeOtherLoan, function (e) {
            e.preventDefault();
            _this.removeOtherLoan($(this));
        });

        $(document).on('click', _this.el.addNewOtherLoan, function (e) {
            e.preventDefault();
            _this.addNewOtherLoan();
        });

        $(document).on('change', _this.el.loanTermsCheckbox, function () {
            if ($('#loan_model_terms_checkbox:checked').val() == 1) {
                if (_this.data.signaturePad.isEmpty()) {
                    $('.signature_error').html("Please provide a signature first.");
                    setTimeout(function () {
                        $('#loan_model_terms_checkbox').prop('checked', false);
                    }, 100)
                } else {
                    $('.confirmConfirmLoanApplication').removeAttr('disabled');
                }
            } else {
                $('.confirmConfirmLoanApplication').attr('disabled', true);
            }
        });

        $(document).on('click', '.clearSignature', function (e) {
            e.preventDefault();
            _this.data.signaturePad.clear();
            $('#loan_model_terms_checkbox').prop('checked', false);
            $('.confirmConfirmLoanApplication').attr('disabled', true);
        });

        $(document).on('click', '.cancelConfirmLoanApplication', function (e) {
            e.preventDefault();
            $(_this.el.loanApplicationForm).find('[name="signature"]').val('');
        });

        $(document).on('click', '.confirmConfirmLoanApplication', function (e) {
            e.preventDefault();
            if (_this.data.signaturePad.isEmpty()) {
                $('.signature_error').html("Please provide a signature first.");
            } else {
                $(_this.el.loanApplicationForm).find('[name=signature]').val(_this.data.signaturePad.toDataURL('image/png'));
                $(_this.el.loanApplicationForm).submit();
            }
        });

        $(document).on('click', _this.el.editLoanApplication, function (e) {
            e.preventDefault();
            _this.loansFormReset();
            _this.setEditLoan($(this).data('id'));
        });

        $(document).on('click', _this.el.deleteLoan, function (e) {
            e.preventDefault();
            $(_this.el.deleteLoanModal).find(_this.el.confirmDeleteLoanButton).data('id', $(this).data('id'));
            $(_this.el.deleteLoanModal).modal('show');
        });

        $(document).on('click', _this.el.deleteLoanModal + ' ' + _this.el.confirmDeleteLoanButton, function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'delete',
                url: adminAjaxURL + 'loans/' + $(this).data('id'),
                success: function (data) {
                    _this.data.lTable.draw();
                    $(_this.el.deleteLoanModal).modal('hide');
                }
            })
        });

        $(document).on('click', _this.el.changeStatus, function (e) {
            e.preventDefault();
            _this.loanStatusToggleDiv($(this).data('status'), $(this).data('notes'), $(this).data('amount'), $(this).data('suggest'));
            $(_this.el.loanStatusChangeModal).find(_this.el.loanStatusChangeForm).data('id', $(this).data('id'));
            $(_this.el.loanStatusChangeModal).find(_this.el.loanStatusChangeForm).data('status', $(this).data('status'));
            $(_this.el.loanStatusChangeModal).find('.error[for="description"]').html('');
            $(_this.el.loanStatusChangeModal).modal('show');
        });

        $(document).on('submit', _this.el.loanStatusChangeForm, function (e) {
            e.preventDefault();
            fullLoader.on();
            // $(_this.el.loanStatusChangeModal).modal('hide');
            $('span.error[for="decline_reason"]').html('');
            $('span.error[for="hold_reason"]').html('');
            var status = true;
            if ($(_this.el.loanStatusChangeModal).find(_this.el.loanStatusChangeForm).data('status') == 2 || $(_this.el.loanStatusChangeModal).find(_this.el.loanStatusChangeForm).data('status') == 11) {
                if ($(_this.el.loanStatusChangeModal).find(_this.el.loanStatusChangeForm).data('status') == 2 && $('#change_status_hold_reason').val() == '') {
                    $('span.error[for="hold_reason"]').html('This field is required.');
                    status = false;
                }
                if ($(_this.el.loanStatusChangeModal).find(_this.el.loanStatusChangeForm).data('status') == 11 && $('#change_status_decline_reason').val() == '') {
                    $('span.error[for="decline_reason"]').html('This field is required.');
                    status = false;
                }
            }
            if (status) {
                $.ajax({
                    dataType: 'json',
                    method: "post",
                    url: adminAjaxURL + 'loans/' + $(this).data('id') + '/status/' + $(this).data('status'),
                    data: $(_this.el.loanStatusChangeForm).serialize(),
                    success: function (data) {
                        _this.data.lTable.draw(false);
                        if (data['status']) {
                            $(_this.el.loanStatusChangeModal).modal('hide');
                        } else {
                            if (data['message'] != undefined && data['message'] != '') {
                                swal({
                                    type: 'warning',
                                    text: data['message']
                                });
                                $(_this.el.loanStatusChangeModal).modal('hide');
                            } else {
                                $(_this.el.loanStatusChangeModal).find('.error[for="description"]').html('Description field is required.');
                            }
                        }
                        fullLoader.off();
                    }
                });
            }
        });

        $(document).on('click', _this.el.showTransaction, function (e) {
            e.preventDefault();
            _this.showTransaction($(this).data('id'), $(this).data('status'));
        });

        $(document).on('change blur keyup', '#transactionForm input', function () {
            var sum = 0;
            $('#transactionForm input[name^="payment_amount"]').each(function (key, item) {
                if ($(item).val() != '') {
                    sum = sum + parseFloat($(item).val());
                }
            });
            sum = sum.toFixed(2);
            var cashback = 0;
            $('#transactionForm input[name^="cashback_amount"]').each(function (key, item) {
                if ($(item).val() != '') {
                    cashback = cashback + parseFloat($(item).val());
                }
            });
            cashback = cashback.toFixed(2);
            $('#transactionForm input[name="transaction_total[received]"]').val(sum);
            $('#transactionForm input[name="transaction_total[cash_back]"]').val(cashback);
            $('#transactionForm input[name="transaction_total[payment]"]').val((sum - cashback).toFixed(2));
        });

        $(document).on('click', '.write_off_loan_application', function () {
            // e.preventDefault();
            $('#transactionForm [name="write_off"]').val(true);
        });

        $(document).on('click', '.confirmConfirmWriteOff', function (e) {
            e.preventDefault();
            _this.data.status = false;
            _this.transactionFormSubmit('#transactionForm', 'post', $(this).data('url'));
        });

        $(document).on('click', '.showLoanStatusHistory', function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'get',
                url: adminAjaxURL + 'loans/' + $(this).data('id') + '/status-history',
                success: function (data) {
                    str = '';
                    for (var index in data['history']) {
                        var history = data['history'][index];
                        str += '<tr>' +
                            '   <td>' + history['user_name'] + '</td>' +
                            '   <td>' + history['loan_status'] + '</td>' +
                            '   <td>' + history['note'] + '</td>' +
                            '   <td>' + history['date'] + '</td>' +
                            '</tr>';
                    }
                    $('#loan_status_history_table').html(str);
                    $('#loan_status_history_model').modal('toggle');
                }
            });
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
                    _this.data.lTable.draw(false);
                }
            })
        });
    },

    loansFormReset() {
        var _this = this;
        _this.data.amount_index = 0;
        $(_this.el.loanApplicationForm).find('[name="id"]').val('');
        $(_this.el.loanApplicationModal).find(_this.el.loanApplicationForm)[0].reset();
        $(_this.el.loanApplicationForm).find('[name="client_id"]').val('').select2();
        $(_this.el.loanApplicationForm).find('[name="loan_reason"]').val('').select2();
        $(_this.el.loanApplicationForm).find('[name="loan_type"]').val('').select2();
        $(_this.el.loanApplicationForm).find('[name="amount"]').val('').select2();
        $(_this.el.loanApplicationForm).find('[name="signature"]').val('');
        _this.validateLoanApplicationFrom(adminAjaxURL + 'loans', 'post');
        $('.other-loan-holder').html('');
        initTooltip();
        datePickerInit();
    },

    userMasterRelatedData(user_id, loan_type, amount) {
        var _this = this;
        $.ajax({
            dataType: 'json',
            method: 'get',
            url: adminAjaxURL + 'users/' + user_id + '/loans-master',
            success(data) {
                var country = data['country'];
                if (country != null) {
                    $('#tax_percentage').val(country['tax_percentage']);
                    $('#tax_name').val(country['tax_name']);
                    $('#territory_tax_name').text(country['tax']);
                    $('#territory_tax_name').text(country['tax']);
                    $('#territory_tax_percentage').text(country['tax_percentage']);
                }

                $('#loan_type').html('<option>Select Loan Type</option>');
                var loan_types = data['loan_types'];
                for (var index in loan_types) {
                    str = '';
                    if (loan_type != undefined && index == loan_type) {
                        str = "<option value='" + index + "' selected>" + loan_types[index] + "</option>";
                    } else {
                        str = "<option value='" + index + "'>" + loan_types[index] + "</option>";
                    }
                    $('#loan_type').append(str);
                }

                $('input[name="user_status"]').val(data.user.status);

                if (amount != undefined) {
                    _this.loanTypeData(loan_type, amount);
                }
            }
        });
    },

    validateLoanApplicationFrom(url, method) {
        var _this = this;
        $(_this.el.loanApplicationForm).data('validator', null);
        $(_this.el.loanApplicationForm).unbind();

        validator = $(_this.el.loanApplicationForm).validate({
            // define validation rules
            rules: {
                client_id: {required: true},
                loan_reason: {required: true},
                loan_type: {required: true},
                amount: {required: true},
            },

            errorPlacement(error, element) {
                $('span[for="' + $(element).attr('name') + '"]').html(error);
                $(element).removeClass('error');
            },

            submitHandler(form) {
                if ($(_this.el.loanApplicationForm).find('[name="signature"]').val() != '') {
                    fullLoader.on({
                        text: 'Loading !'
                    });
                    $('.error').html('');
                    var custom_data = new FormData($(_this.el.loanApplicationForm)[0]);
                    $.ajax({
                        dataType: 'json',
                        method: method,
                        url: url,
                        data: custom_data,
                        processData: false,
                        contentType: false,
                        cache: false,
                        crossDomain: true,
                        success(data) {
                            if (data['status']) {
                                _this.data.lTable.draw();
                                $(_this.el.loanApplicationModal).modal('hide');
                            }
                            fullLoader.off();
                        },
                        error(jqXHR, exception) {
                            var Response = jqXHR.responseText;
                            ErrorBlock = $(form);
                            Response = $.parseJSON(Response);
                            displayErrorMessages(Response, ErrorBlock, 'input');
                            fullLoader.off();
                        }
                    });
                } else {
                    var htmlContent = '<div class="text-center"><table class="table table-bordered">' +
                        '<tr><td>Requested Amount</td><td>' + parseFloat($('[name="amount"]').val()).toFixed(2) + '</td></tr>' +
                        '<tr><td>Origination Fee</td><td>' + $('#origination_fee_amount').html() + '</td></tr>' +
                        '<tr><td>Tax on Origination Fee</td><td>' + $('#territory_tax').html() + '</td></tr>';
                    if ($("#apr").val() != '') {
                        htmlContent += '<tr><td>APR (%)</td><td>' + parseFloat($('#apr').val()).toFixed(2) + '</td></tr>';
                    }
                    htmlContent += '<tr><td>Interest</td><td>' + $('#interest_amount_span').html() + '</td></tr>' +
                        '<tr><td>Tax on Interest</td><td>' + $('#tax_interest').html() + '</td></tr>' +
                        '<tr><td>Credit Amount</td><td>' + $('#credit_amount').html() + '</td></tr>' +
                        '</table></div>' +
                        '<h5>Signature</h5>' +
                        '<canvas style="border: 1px solid #000;" id="loan_signature" width="400" height="200"></canvas>' +
                        '<p class="error signature_error"></p>' +
                        '<button class="btn btn-danger clearSignature">Clear</button><br><br>' +
                        '<label style="mt-2">' +
                        '<input type="checkbox" name="terms" value="1" id="loan_model_terms_checkbox"> I Agree with' +
                        '<a href="' + siteURL + 'loan-contract/' + $('#loan_type').val() + '?user_id=' + $(_this.el.loanApplicationForm).find('[name="client_id"]').val() + '&loan_amount=' + parseFloat($('[name="amount"]').val()).toFixed(2) + '" target="_blank"> Loan agreement</a>.' +
                        '</label>';
                    swal({
                        html: htmlContent,
                        showCancelButton: true,
                        cancelButtonClass: 'btn-danger btn-md waves-effect cancelConfirmLoanApplication',
                        confirmButtonClass: 'btn-primary btn-md waves-effect waves-light confirmConfirmLoanApplication',
                        confirmButtonText: 'Confirm',
                        cancelButtonText: 'Go back',
                    });
                    canvas = document.getElementById('loan_signature');
                    _this.data.signaturePad = new SignaturePad(canvas, {
                        backgroundColor: "rgb(255,255,255)"
                    });
                    $('.signature_error').html('');
                    $('.confirmConfirmLoanApplication').attr('disabled', true);
                }
            }
        });
    },

    addNewIncome() {
        var _this = this;
        $(_this.el.income_holder).append(_this.incomeAmountHtml('second'));
        $('[data-toggle="tooltip"]').tooltip();
        $('.income-holder').find('[name="income_amount[' + _this.data.amount_index + ']"]').rules('add', {
            required: true,
            regex: /^\d{0,22}(\.\d{0,2})?$/
        });
        $('.income-holder').find('[name="income_proof_image[' + _this.data.amount_index + ']"]').rules('add', {
            required: true,
        });
        _this.data.amount_index++;
        datePickerInit();
        initTooltip();
        _this.calculate();
    },

    addNewOtherLoan() {
        var _this = this;
        $('.other-loan-holder').append(_this.newOtherLoanHtml());
        $('[name="expense_type[' + _this.data.amount_index + ']"]').select2();
        $('.other-loan-holder').find('[name="expense_type[' + _this.data.amount_index + ']"]').rules('add', {
            required: true,
        });
        $('.other-loan-holder').find('[name="other_amount[' + _this.data.amount_index + ']"]').rules('add', {
            required: true,
            regex: /^\d{0,22}(\.\d{0,2})?$/
        });
        _this.data.amount_index++;
        initTooltip();
        _this.calculate();
    },

    newOtherLoanHtml(otherAmount) {
        var _this = this;
        if (otherAmount == undefined) {
            otherAmount = {
                'amount_type': '',
                'amount': '',
                'id': '',
            };
        }
        str = '' +
            '<div class="row other-loan-item">' +
            '                                        <div class="column col-md-3">' +
            '                                            <div class="form-group">';
        str += '                                                <label class="control-label">Type</label>';
        str += '                                                <select name="expense_type[' + _this.data.amount_index + ']" class="expense-type form-control">' +
            $('.expense_types_options').html() +
            '                                                </select>' +
            '<span class="error" for="expense_type[' + _this.data.amount_index + ']"></span>' +
            '                                            </div>' +
            '                                        </div>' +
            '                                        <div class="column col-md-3">';
        str += '                                                <label class="control-label">Monthly Amount *</label>';
        str += '    <input type="number" step="0.01" min="0" value="' + otherAmount['amount'] + '" name="other_amount[' + _this.data.amount_index + ']" class="expense-amount form-control numeric-input">' +
            '<span class="error" for="other_amount[' + _this.data.amount_index + ']"></span>' +
            '    </div>' +
            '    <div class="column col-md-2">' +
            '    </div>' +
            '    <div class="column actions text-right col-md-2">' +
            '        <input type="hidden" name="expense_id[' + _this.data.amount_index + ']" class="expense-id" value="' + otherAmount['id'] + '">' +
            '        <button class="clear-btn btn btn-info clearOther" type="button">' +
            '           <i class="fa fa-minus"></i>' +
            '        </button>' +
            '        <button class="delete-btn btn btn-danger removeOtherLoan" style="" type="button"><i class="fa fa-trash test-trash"></i></button>' +
            '    </div>' +
            '</div>';
        return str;
    },

    incomeAmountHtml(type, incomeAmount) {
        var _this = this;
        if (incomeAmount == undefined) {
            incomeAmount = {
                'date': '',
                'amount': '',
                'file_name': '#',
                'html': '',
                'id': '',
            };
        } else {
            incomeAmount['html'] = '<button class="btn btn-primary" type="button"><i class="fa fa-paperclip"></i></button>';
        }
        var addbuttonhtml = '<button class="add-btn btn btn-success addNewIncome" type="button"><i class="fa fa-plus"></i></button>';
        var datehtml = '           <label class="control-label">Date of payment *</label>' +
            '           <input type="text" name="date_of_payment[' + _this.data.amount_index + ']" id="date_of_payment" class="income-date form-control date-picker" value="' + incomeAmount['date'] + '">' +
            '<span class="error" for="date_of_payment[' + _this.data.amount_index + ']"></span>';
        var deleteButton = '';
        var income_type = 1;
        var income_name = 'Last Payslip';
        if (type != 'main') {
            addbuttonhtml = '';
            datehtml = '';
            deleteButton = '<button class="delete-btn btn btn-danger removeIncome" type="button"><i class="fa fa-trash"></i></button>';
            income_type = 2;
            income_name = 'Other Income';
        }
        str = ' <div class="row income-item">' +
            '       <div class="column col-md-2">' +
            '           <div class="form-group">' +
            '               <label class="control-label">Type</label>' +
            '               <label class="income-type-name form-control">' + income_name + '</label>' +
            '               <input type="hidden" class="income-type" name="income_type[' + _this.data.amount_index + ']" value="' + income_type + '">' +
            '           </div>' +
            '       </div>' +
            '       <div class="column col-md-2">' +
            '           <label class="control-label">Amount *</label>' +
            '           <input value="' + incomeAmount['amount'] + '" type="number" step="0.01" min="0" name="income_amount[' + _this.data.amount_index + ']" class="income-amount form-control numeric-input">' +
            '<span class="error" for="income_amount[' + _this.data.amount_index + ']"></span>' +
            '       </div>' +
            '       <div class="column col-md-2 date-holder">' + datehtml + '</div>' +
            '       <div class="column col-md-3">' +
            '           <label class="control-label" data-toggle="tooltip" title="png,gif,jpg,jpeg,doc,docx,pdf">Upload Last Payslip *</label>' +
            '           <input type="file" name="income_proof_image[' + _this.data.amount_index + ']" id="income_proof_image" onchange="filesizeValidation(this)" class="proof-photo form-control" accept="image/png,image/gif,image/jpeg,image/jpg,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf">' +
            '           <div class="has-error">' +
            '               <span class="help-block"></span>' +
            '           </div>' +
            '<span class="error" for="income_proof_image[' + _this.data.amount_index + ']"></span>' +
            '           <input type="hidden" name="image_hidden[' + _this.data.amount_index + ']" class="income-proof-image-hidden">' +
            '       </div>' +
            '       <div class="column actions text-right col-md-3">' +
            '           <button class="clear-btn btn btn-info clearIncome" type="button"><i class="fa fa-minus"></i></button>' +
            '           <input type="hidden" name="income_id[' + _this.data.amount_index + ']" class="income-id" value="' + incomeAmount['id'] + '">' + addbuttonhtml + deleteButton +
            '           <a href="' + incomeAmount['file_name'] + '" class="income-image">' + incomeAmount['html'] + '</a>' +
            '       </div>' +
            '   </div>';
        return str;
    },

    loanTypeData(type, amount) {
        var _this = this;
        $.ajax({
            dataType: 'json',
            url: adminAjaxURL + 'loan-type/' + type,
            method: 'get',
            success(data) {
                var type = data['type'];
                $('#loan_component').val(type['loan_component']);
                $('#loan_component_amount').html(' (' + type['loan_component'] + '%)');
                $("#apr").val(type['apr']);

                $('#origination_type').val(type['origination_type']);
                $('#origination_amount').val(type['origination_amount']);

                if (type['origination_amount'] == 1) {
                    $('#origination_fee_percentage').text('(' + type['origination_amount'] + ' %)');
                } else {
                    $('#origination_fee_percentage').text('');
                }

                $('#renewal_type').val(type['renewal_type']);
                $('#renewal_amount').val(type['renewal_amount']);

                $('#debt_type').val(type['debt_type']);
                $('#debt_amount').val(type['debt_amount']);

                $('#debt_tax_type').val(type['debt_tax_type']);
                $('#debt_tax_amount').val(type['debt_tax_amount']);

                $('#debt_collection_percentage').val(type['debt_collection_percentage']);
                $('#debt_collection_type').val(type['debt_collection_type']);
                $('#debt_collection_tax_type').val(type['debt_collection_tax_type']);
                $('#debt_collection_tax_value').val(type['debt_collection_tax_value']);

                $('#period').val(type['number_of_days']);

                $('#interest').val(type['interest']);

                $('#interest_percentage').text('(' + type['interest'] + ' %)');

                $('#cap_period').val(type['cap_period']);

                $(_this.el.amount).html('<option value="">Select Amount</option>');
                for (var i = parseInt(type['minimum_loan']); i <= parseInt(type['maximum_loan']); i = i + parseInt(type['unit'])) {
                    if (amount != undefined && amount == i) {
                        $(_this.el.amount).append('<option value="' + i + '" selected>' + i + '</option>');
                    } else {
                        $(_this.el.amount).append('<option value="' + i + '">' + i + '</option>');
                    }
                }
                $(_this.el.amount).select2();

                _this.calculate();
            }
        });
    },

    calculate() {
        var totalIncome = 0;
        var totalExpense = 0;
        var avaAmt = 0;
        var loan_component = round($('#loan_component').val(), 2);

        var requestedAmount = round($('[name="amount"]').val(), 2);

        var origination_type = $('#origination_type').val();
        var origination_amount = round($('#origination_amount').val(), 2);

        var tax_percentage = round($('#tax_percentage').val(), 2);

        var interest = round($('#interest').val(), 2);

        if (!(requestedAmount > 0)) {
            requestedAmount = 0;
        }


        $('.income-amount').each(function (item) {
            if ($(this).val() == '') {
                totalIncome = parseFloat(totalIncome);
            } else {
                totalIncome = parseFloat(totalIncome) + parseFloat($(this).val());
            }
        });
        totalIncome = round(totalIncome, 2);

        $('.expense-amount').each(function (item) {
            if ($(this).val() == '') {
                totalExpense = parseFloat(totalExpense) + parseFloat(0);
            } else {
                totalExpense = parseFloat(totalExpense) + parseFloat($(this).val());
            }
        });
        totalExpense = round(totalExpense, 2);

        $('input[name="salary"]').val(totalIncome);
        $('#salary_amount').text(totalIncome);

        $('input[name="other_loan_deduction"]').val(totalExpense)
        $('#existing_loan_amount').html(totalExpense);


        avaAmt = round(totalIncome - totalExpense, 2);
        $('#available_loan_amount').html(avaAmt);

        $('input[name="max_amount"]').val(avaAmt)

        var maxAmt = 0;
        if (avaAmt > 0 && loan_component > 0) {
            maxAmt = round(avaAmt * loan_component / 100, 2);
        }
        $('#max_loan_amount').val(maxAmt);
        $('#max_amount').val(maxAmt);

        var origination_fee = 0;
        if (origination_amount > 0) {
            if (origination_type == 1 && requestedAmount > 0) {
                origination_fee = requestedAmount * origination_amount / 100;
            } else {
                origination_fee = origination_amount;
            }
        }
        origination_fee = round(origination_fee, 2);
        $('#origination_fee').val(origination_fee);
        $('#origination_fee_amount').text(origination_fee);


        var tax = 0;
        if (origination_fee > 0) {
            tax = round(origination_fee * tax_percentage / 100, 2);
        }
        $('#tax').val(tax);
        $('#territory_tax').text(tax);

        var interest_amount = 0;
        if (requestedAmount > 0) {
            interest_amount = round(requestedAmount * interest / 100, 2);
        }
        $('#interest_amount').val(interest_amount);
        $('#interest_amount_span').text(interest_amount);

        var tax_on_interest = round(interest_amount * tax_percentage / 100, 2);

        $('#tax_interest').html(tax_on_interest);

        if (requestedAmount > 0) {
            $('#credit_amount').text(round(requestedAmount - origination_fee - tax - interest_amount - tax_on_interest, 2));
        } else {
            $('#credit_amount').text(0);
        }
    },

    clearIncome(element) {
        var _this = this;
        $(element).parents('.income-item').find('input[type="text"], input[type="file"]').val('');
        $(element).parents('.income-item').find('.income-date').datepicker('update', '');
        _this.calculate();
    },

    clearOther(element) {
        var _this = this;
        $(element).parents('.other-loan-item').find('input[type="text"], input[type="file"]').val('');
        _this.calculate();
    },

    removeIncome(element) {
        var _this = this;
        $(element).parents('.income-item').remove();
        _this.calculate();
    },

    removeOtherLoan(element) {
        var _this = this;
        $(element).parents('.other-loan-item').remove();
        _this.calculate();
    },

    setEditLoan(loan_id) {
        var _this = this;
        $.ajax({
            type: 'GET',
            url: adminAjaxURL + 'loans/' + loan_id + '/edit',
            data: {},
            dataType: 'json',
            success: function (data) {
                $.each(data['amounts'], function (i, item) {
                    if (item.type == '1') {
                        item['file_name'] = data['folder'] + item['file_name'];
                        if (item.date != null) {
                            $(_this.el.income_holder).html(_this.incomeAmountHtml('main', item));
                            $(_this.el.income_holder).find('[name="date_of_payment[' + _this.data.amount_index + ']"]').rules('add', {
                                required: true,
                            });
                        } else {
                            $(_this.el.income_holder).append(_this.incomeAmountHtml('second', item));
                        }
                        $(_this.el.income_holder).find('[name="income_amount[' + _this.data.amount_index + ']"]').rules('add', {
                            required: true,
                            regex: /^\d{0,22}(\.\d{0,2})?$/
                        });
                        _this.data.amount_index++;
                    } else {
                        $('.other-loan-holder').append(_this.newOtherLoanHtml(item));
                        $('[name="expense_type[' + _this.data.amount_index + ']"]').val(item['amount_type']).select2();
                        $('.other-loan-holder').find('[name="expense_type[' + _this.data.amount_index + ']"]').rules('add', {
                            required: true,
                        });
                        $('.other-loan-holder').find('[name="other_amount[' + _this.data.amount_index + ']"]').rules('add', {
                            required: true,
                            regex: /^\d{0,22}(\.\d{0,2})?$/
                        });
                        _this.data.amount_index++;
                        _this.calculate();
                    }
                });

                datePickerInit();

                setForm(_this.el.loanApplicationForm, data['loan']);

                _this.userMasterRelatedData(data['loan']['client_id']['value'], data['loan']['loan_type']['value'], data['loan']['amount']['value']);

                initTooltip();

                $('#loanApplicationModal').find('[name="signature"]').val('');
                $('#loanApplicationModal').modal('show');
            },
            error: function (jqXHR, exception) {
                var Response = jqXHR.responseText;
                Response = $.parseJSON(Response);
                displayErrorMessages(Response, $(_this.el.loanApplicationForm), 'ul');
            }
        });
    },

    loanStatusToggleDiv(status, notes_required, amount, suggest) {
        var _this = this;
        $(_this.el.loanStatusChangeModal).find('[name="employee_id"]').val('').select2();
        $(_this.el.loanStatusChangeModal).find('[name="decline_reason"]').val('').select2();
        $(_this.el.loanStatusChangeModal).find('[name="hold_reason"]').val('').select2();
        $(_this.el.loanStatusChangeModal).find('[name="description"]').val('');
        if (status == 2) {
            $(_this.el.loanStatusChangeModal).find('.modal-title').html('On Hold');
            $(_this.el.loanStatusChangeModal).find('#status_desc').html('Are you sure you want to put this loan on Hold?');
            $(_this.el.loanStatusChangeModal).find('.currentEmployeeDiv').hide();
            $(_this.el.loanStatusChangeModal).find('.declineReasonDiv').hide();
            $(_this.el.loanStatusChangeModal).find('.holdReasonDiv').show();
            $(_this.el.loanStatusChangeModal).find('.descriptionDiv').show();
        } else if (status == 3 || status == 12) {
            var title = 'Approve';
            if (status == 12) {
                title = 'Pre-approve';
            }
            $(_this.el.loanStatusChangeModal).find('.modal-title').html(title);
            $(_this.el.loanStatusChangeModal).find('.currentEmployeeDiv').hide();
            $(_this.el.loanStatusChangeModal).find('.declineReasonDiv').hide();
            $(_this.el.loanStatusChangeModal).find('.holdReasonDiv').hide();
            if (notes_required != undefined && notes_required == 1) {
                $(_this.el.loanStatusChangeModal).find('#status_desc').html('The loan amount ' + amount + ' is higher than the suggested amount . Please add a reason.');
                $(_this.el.loanStatusChangeModal).find('.descriptionDiv').show();
                $(_this.el.loanStatusChangeModal).find('#description_required').html('*');
                $(_this.el.loanStatusChangeModal).find('[name="description_required"]').val(1);
            } else {
                $(_this.el.loanStatusChangeModal).find('#status_desc').html('Are you sure you want to ' + title + ' this loan?');
                $(_this.el.loanStatusChangeModal).find('.descriptionDiv').hide();
                $(_this.el.loanStatusChangeModal).find('[name="description_required"]').val(0);
            }
        } else if (status == 4) {
            $(_this.el.loanStatusChangeModal).find('.modal-title').html('Current');
            $(_this.el.loanStatusChangeModal).find('#status_desc').html('Are you sure you want to change loan status to "Current"?');
            $(_this.el.loanStatusChangeModal).find('.currentEmployeeDiv').show();
            $(_this.el.loanStatusChangeModal).find('.declineReasonDiv').hide();
            $(_this.el.loanStatusChangeModal).find('.holdReasonDiv').hide();
            $(_this.el.loanStatusChangeModal).find('.descriptionDiv').hide();
        } else if (status == 11) {
            $(_this.el.loanStatusChangeModal).find('.modal-title').html('Declined');
            $(_this.el.loanStatusChangeModal).find('#status_desc').html('Are you sure you want to decline this loan?');
            $(_this.el.loanStatusChangeModal).find('.currentEmployeeDiv').hide();
            $(_this.el.loanStatusChangeModal).find('.declineReasonDiv').show();
            $(_this.el.loanStatusChangeModal).find('.holdReasonDiv').hide();
            $(_this.el.loanStatusChangeModal).find('.descriptionDiv').show();
        }
    },

    showTransaction(loan_id, status) {
        var _this = this;
        if ($.fn.dataTable.isDataTable('#loan-transaction-table')) {
            _this.data.transactionDatatable.destroy();
        }
        _this.data.transactionDatatable = $('#loan-transaction-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": adminAjaxURL + 'loans/' + loan_id + '/transactions',
            },
            columns: [
                {data: 'id', name: 'loan_transactions.id', visible: false},
                {data: 'created_user_name', name: 'users.firstname'},
                {data: 'created_user_name', name: 'users.lastname', visible: false},
                {data: 'transaction_type_name', name: 'transaction_types.title'},
                {data: 'payment_type', name: 'loan_transactions.type', searchable: false},
                {data: 'notes', name: 'notes'},
                {data: 'amount', name: 'amount'},
                {data: 'cash_back_amount', name: 'cash_back_amount'},
                {data: 'payment_date', name: 'payment_date'},
                {data: 'created_at', name: 'created_at'},
            ],
            'order': [[0, 'desc']],
            "pageLength": 50
        });
        $.ajax({
            dataType: 'json',
            method: 'get',
            url: adminAjaxURL + "loans/" + loan_id + "/history",
            data: {
                not_country_related: 1
            },
            success: function (data) {
                var loan_current = data['history'][0];
                if (loan_current != undefined) {
                    $('input[name="principal_balance"]').val(loan_current['principal']);
                    $('input[name="renewal_balance"]').val(loan_current['renewal']);
                    $('input[name="interest_balance"]').val(loan_current['interest']);
                    $('input[name="renewal_interest_tax"]').val(loan_current['tax']);
                    $('input[name="debt_balance"]').val(loan_current['debt']);
                    $('input[name="debt_tax"]').val(loan_current['debt_tax']);
                    $('input[name="total_balance"]').val(loan_current['total']);
                }
            }
        });

        $.ajax({
            dataType: 'json',
            method: 'get',
            url: adminAjaxURL + 'loans/' + loan_id + '/user-branches',
            success: function (data) {
                var str = '<option value="">Select Branch</option>';
                for (var index in data['branches']) {
                    str += '<option value="' + index + '">' + data['branches'][index] + '</option>';
                }
                $('#branch_selection').html(str);
            }
        });
        $('#loanTransactionModal').find('.js--add-form').show();
        if (status != undefined && status != 4 && status != 5 && status != 6) {
            $('#loanTransactionModal').find('.js--add-form').hide();
        }
        $('#loanTransactionModal').find('.js--submit-button').attr('disabled', false);
        $('#loanTransactionModal').find('.write_off_loan_application').attr('disabled', false);
        $('#loanTransactionModal').find('.error').html('');
        $('#loanTransactionModal').find('.has-error').removeClass('has-error');
        $('#transactionForm [name="write_off"]').val(false);
        _this.validateTranasactionFrom(adminAjaxURL + 'loans/' + loan_id + '/transactions', 'post');
        $('#loanTransactionModal').modal('show');
        $('#transactionForm')[0].reset();
        $('#transactionForm').find('input[name="loan_id"]').val(loan_id);
        $('#transactionForm').find('.write_off_loan_application').data('id', loan_id);
        $('#clearLoanButton').data('id', loan_id);
    },

    validateTranasactionFrom(url, method, write_off) {
        var _this = this;
        $(_this.el.loanTransactionForm).data('validator', null);
        $(_this.el.loanTransactionForm).unbind();
        _this.data.status = true;
        validator = $(_this.el.loanTransactionForm).validate({
            rules: {
                'transaction_type': {required: true},
                'payment_date': {required: true},
                'branch_id': {required: true,},
                'payment_amount[1]': {regex: /^\d{0,22}(\.\d{0,2})?$/},
                'payment_amount[2]': {regex: /^\d{0,22}(\.\d{0,2})?$/},
                'payment_amount[3]': {regex: /^\d{0,22}(\.\d{0,2})?$/},
                'payment_amount[4]': {regex: /^\d{0,22}(\.\d{0,2})?$/},
                'payment_amount[5]': {regex: /^\d{0,22}(\.\d{0,2})?$/},
                'payment_amount[6]': {regex: /^\d{0,22}(\.\d{0,2})?$/},
                'cashback_amount[1]': {regex: /^\d{0,22}(\.\d{0,2})?$/},
                'cashback_amount[2]': {regex: /^\d{0,22}(\.\d{0,2})?$/},
                'cashback_amount[3]': {regex: /^\d{0,22}(\.\d{0,2})?$/},
                'cashback_amount[4]': {regex: /^\d{0,22}(\.\d{0,2})?$/},
                'cashback_amount[5]': {regex: /^\d{0,22}(\.\d{0,2})?$/},
                'cashback_amount[6]': {regex: /^\d{0,22}(\.\d{0,2})?$/},
            },

            errorPlacement(error, element) {
                $('span[for="' + $(element).attr('name') + '"]').html(error);
                $(element).removeClass('error');
                $('#loanTransactionModal').find('.js--submit-button').attr('disabled', false);
                $('#loanTransactionModal').find('.write_off_loan_application').attr('disabled', false);
            },

            submitHandler(form) {
                let write_off = $('#transactionForm [name="write_off"]').val();
                $('#loanTransactionModal').find('.js--submit-button').attr('disabled', true);
                $('#loanTransactionModal').find('.write_off_loan_application').attr('disabled', true);
                if (!write_off && parseFloat($('[name="transaction_total[payment]"]').val()) < parseFloat($('[name="total_balance"]').val().replace(',', '')) && $('[name="next_payment_date"]').val() == '') {
                    $('span[for="next_payment_date"]').html('This field is required');
                    $('#loanTransactionModal').find('.js--submit-button').attr('disabled', false);
                    $('#loanTransactionModal').find('.write_off_loan_application').attr('disabled', false);
                } else {
                    $('span[for="next_payment_date"]').html('');
                    if (write_off || ($('[name="transaction_total[payment]"]').val() > 0 && parseFloat($('[name="transaction_total[payment]"]').val()) <= parseFloat($('[name="total_balance"]').val().replace(',', '')))) {
                        _this.transactionFormSubmit(form, method, url);
                    } else {
                        if ($('[name="transaction_total[payment]"]').val() < 0) {
                            swal({text: "Total cash back should be less than the received amount."});
                        } else if ($('[name="transaction_total[payment]"]').val() == 0) {
                            swal({text: "The total payment should be greater than 0."});
                        } else {
                            swal({text: "The total payment exceeds the outstanding balance."});
                        }
                        $('#loanTransactionModal').find('.js--submit-button').attr('disabled', false);
                        $('#loanTransactionModal').find('.write_off_loan_application').attr('disabled', false);
                    }
                }
            }
        });
    },

    transactionFormSubmit(form, method, url) {
        var _this = this;
        let write_off = $('#transactionForm [name="write_off"]').val();
        if (write_off == 'true' && _this.data.status == true) {
            var htmlContent = '<div class="text-center">';
            htmlContent += '<h4>Do you want to write off this loan?</h4>';
            htmlContent += '</div>';
            htmlContent += '<div class="text-center mt-2">';
            htmlContent += 'It still has ' + $('#loanTransactionModal').find('[name="total_balance"]').val() + ' remaining balance.';
            htmlContent += '</div>';
            swal({
                html: htmlContent,
                showCancelButton: true,
                cancelButtonClass: 'btn-danger btn-md waves-effect cancelConfirmLoanApplication',
                confirmButtonClass: 'btn-primary btn-md waves-effect waves-light confirmConfirmWriteOff',
                confirmButtonText: 'Confirm',
                cancelButtonText: 'Go back'
            });
            $('.confirmConfirmWriteOff').data('url', url);
        } else {
            _this.data.status = false;
        }
        if (!_this.data.status) {
            $.ajax({
                dataType: 'json',
                method: method,
                url: url,
                data: $(form).serialize(),
                success(data) {
                    if (data['status']) {
                        $(form)[0].reset();
                        $("#loanTransactionModal").modal("hide");
                        _this.data.lTable.draw();
                        if (data['receipt_pdf'] != undefined) {
                            window.open(data['receipt_pdf'], 'download');
                        }
                        window.open(adminURL + 'loan-applications/' + data['loan_id'] + '/history', '_blank');
                    } else {
                        $(form)[0].reset();
                        _this.data.lTable.draw();
                        _this.showTransaction(data['loan_id']);
                        $('#loanTransactionModal').find('.js--submit-button').attr('disabled', false);
                        $('#loanTransactionModal').find('.write_off_loan_application').attr('disabled', false);
                        swal({text: "The entered payment exceeds the outstanding balance."});
                    }
                },
                error: function (jqXHR, exception) {
                    $('#loanTransactionModal').find('.js--submit-button').attr('disabled', false);
                    $('#loanTransactionModal').find('.write_off_loan_application').attr('disabled', false);
                    var Response = jqXHR.responseText;
                    ErrorBlock = $(form);
                    Response = $.parseJSON(Response);
                    displayErrorMessages(Response, ErrorBlock, 'input');
                }
            });
        }
    }
};