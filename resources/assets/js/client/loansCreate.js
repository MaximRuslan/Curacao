var loansCreate = {
    el: {
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
        loanErrors: '.loan-errors',
        loanTermsCheckbox: '#loan_model_terms_checkbox',
        income_holder: '.income-holder',
    },
    data: {
        process: false,
        amount_index: 1,
        signaturePad: '',
        select_file_text: keywords.select_file,
    },
    init: function () {
        var _this = this;
        _this.bindUiActions();
        $(_this.el.loan_reason + ',' + _this.el.loan_type + ',' + _this.el.amount).select2();
        datePickerInit();
        initTooltip();
        $('[name="income_proof_image[0]"]').inputFileText({
            text: _this.data.select_file_text
        });
    },
    bindUiActions: function () {
        var _this = this;
        $(document).on('change', _this.el.loan_type, function () {
            if ($(this).val() != '' && $(this).val() != null) {
                _this.loanTypeData($(this).val(), '');
            }
        });
        $(document).on('change keyup', _this.el.amount + ',' + _this.el.incomeAmount + ',' + _this.el.expenseAmount, function (e) {
            e.preventDefault();
            _this.calculate();
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
        $(document).on('submit', _this.el.loanApplicationForm, function (e) {
            e.preventDefault();
            _this.submitLoanApplication($(this));
        });

        $(document).on('change', _this.el.loanTermsCheckbox, function () {
            if ($('#loan_model_terms_checkbox:checked').val() == 1) {
                if (_this.data.signaturePad.isEmpty()) {
                    if (keywords.provide_signature != undefined) {
                        $('.signature_error').html(keywords.provide_signature);
                    } else {
                        $('.signature_error').html(keywords.provide_signature);
                    }
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
        });

        $(document).on('click', '.confirmConfirmLoanApplication', function (e) {
            e.preventDefault();
            if (_this.data.signaturePad.isEmpty()) {
                if (keywords.provide_signature != undefined) {
                    $('.signature_error').html(keywords.provide_signature);
                } else {
                    $('.signature_error').html(keywords.provide_signature);
                }
            } else {
                $(_this.el.loanApplicationForm).find('[name=signature]').val(_this.data.signaturePad.toDataURL('image/png'));
                _this.data.process = true;
                _this.submitLoanApplication($(_this.el.loanApplicationForm));
            }
        });

    },
    loanTypeData: function (type, amount) {
        var _this = this;
        $.ajax({
            dataType: 'json',
            url: clientAjaxURL + 'loan-type/' + type,
            method: 'get',
            success: function (data) {
                var type = data['type'];
                $('#loan_component').val(type['loan_component']);
                $('#loan_component_amount').html(' (' + type['loan_component'] + ')');
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
    calculate: function () {
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

    clearIncome: function (element) {
        var _this = this;
        $(element).parents('.income-item').find('input[type="text"], input[type="file"]').val('');
        $(element).parents('.income-item').find('.income-date').datepicker('update', '');
        $(element).parent().parent().find('span').html('');
        // getDays('');
        _this.calculate();
    },
    clearOther: function (element) {
        var _this = this;
        $(element).parents('.other-loan-item').find('input[type="text"], input[type="file"]').val('');
        _this.calculate();
    },
    removeIncome: function (element) {
        var _this = this;
        $(element).parents('.income-item').remove();
        _this.calculate();
        _this.reArrangeIncomes();
    },
    removeOtherLoan: function (element) {
        var _this = this;
        $(element).parents('.other-loan-item').remove();
        _this.calculate();
        _this.reArrangeExpense();
    },

    addNewIncome: function () {
        /* var _this = this;
         $('.income-holder').find('.income-item').first().clone().appendTo('.income-holder');
         $('.income-holder').find('.income-item').last().find('input').val('');
         $('.income-holder').find('.income-item').last().find('.income-type-name').html('Other Income');
         $('.income-holder').find('.income-item').last().find('.income-type').val('2');
         $('.income-holder').find('.income-item').last().find('.add-btn').hide();
         $('.income-holder').find('.income-item').last().find('.delete-btn').show();
         $('.income-holder').find('.income-item').last().find('.date-holder').css({'visibility': 'hidden'});
         $('.income-holder').find('.income-item').last().find('.loan-image').css({'visibility': 'hidden'});
         $('.income-holder').find('.income-item').last().find('.income-image').last().attr('href', '#');
         $('.income-holder').find('.income-item').last().find('.income-image').last().html('');
         $('.income-holder').find('.income-proof-image-hidden').last().val('');
         $('[data-toggle="tooltip"]').tooltip();
         _this.calculate();
         _this.reArrangeIncomes();*/
        var _this = this;
        $(_this.el.income_holder).append(_this.incomeAmountHtml('second'));
        $('[data-toggle="tooltip"]').tooltip();
        $('[name="income_proof_image[' + _this.data.amount_index + ']"]').inputFileText({
            text: _this.data.select_file_text
        });
        _this.data.amount_index++;
        datePickerInit();
        initTooltip();
        _this.calculate();
    },
    addNewOtherLoan: function (element, callback = '') {
        var _this = this;
        $(_this.newOtherLoanHtml()).appendTo('.other-loan-holder');
        $('.other-loan-holder').find('.other-loan-item').last().find('input').val('');
        $('.other-loan-holder').find('.other-loan-item').last().find('.add-btn').hide();
        $('.other-loan-holder').find('.other-loan-item').last().find('.delete-btn').show();
        $('.other-loan-holder').find('.other-loan-item').last().find('.loan-image').css({'visibility': 'hidden'});
        _this.calculate();
        _this.reArrangeExpense();
        if (callback != '') {
            callback();
        }
    },
    newOtherLoanHtml: function () {
        str = '' +
            '<div class="row other-loan-item">' +
            '                                        <div class="column col-md-3">' +
            '                                            <div class="form-group">';
        if (typeof keywords != 'undefined') {

            str += '                                                <label class="control-label">' + keywords.Type + '</label>';
        } else {
            str += '                                                <label class="control-label">Type</label>';
        }
        str += '                                                <select name="expense_type[0]" class="expense-type form-control">' +
            $('.expense_types_options').html() +
            '                                                </select>' +
            '                                            </div>' +
            '                                        </div>' +
            '                                        <div class="column col-md-3">';
        if (typeof keywords != 'undefined') {

            str += '                                                <label class="control-label">' + keywords.MonthlyAmount + '</label>';
        } else {
            str += '                                                <label class="control-label">Monthly Amount</label>';
        }
        str += '    <input type="number" min="0" step="0.01" name="other_amount[0]" class="expense-amount form-control numeric-input">' +
            '    </div>' +
            '    <div class="column col-md-2">' +
            '    </div>' +
            '    <div class="column actions text-right col-md-2">' +
            '        <button class="clear-btn btn btn-info clearOther" type="button">' +
            '           <i class="material-icons">remove</i>' +
            '        </button>' +
            '        <input type="hidden" name="expense_id[0]" class="expense-id">' +
            '        <button class="delete-btn btn btn-danger removeOtherLoan" style="" type="button">' +
            '           <i class="material-icons">delete</i>' +
            '       </button>' +
            '    </div>' +
            '</div>';
        return str;
    },
    reArrangeIncomes: function () {
        $(".income-item").each(function (i, item) {
            $(item).find('.income-id').attr('name', 'income_id[' + i + ']');
            $(item).find('.income-date').attr('name', 'date_of_payment[' + i + ']');
            $(item).find('.income-amount').attr('name', 'income_amount[' + i + ']');
            $(item).find('.income-type').attr('name', 'income_type[' + i + ']');
            $(item).find('.proof-photo').attr('name', 'income_proof_image[' + i + ']');
            $(item).find('.income-proof-image-hidden').attr('name', 'image_hidden[' + i + ']');
        });
    },

    reArrangeExpense: function () {
        $(".other-loan-item").each(function (i, item) {
            console.log('=========================');
            console.log(i);
            console.log('=========================');
            $(item).find('.expense-amount').attr('name', 'other_amount[' + i + ']');
            $(item).find('.proof-photo').attr('name', 'expense_proof_image[' + i + ']');
            $(item).find('.expense-id').attr('name', 'expense_id[' + i + ']');
            $(item).find('.expense-type').attr('name', 'expense_type[' + i + ']');
        });
    },
    submitLoanApplication: function (form) {
        var _this = this;
        var errors = _this.errorsArray();
        if (errors != '') {
            displayErrorMessages(errors, $(_this.el.loanErrors), 'ul');
            $("#applyLoanModal").scrollTop(0);
        } else {
            _this.applicationStore(form);
        }
    },
    errorsArray: function () {
        var _this = this;
        var errors = [];
        if (has_active_loan!=undefined && has_active_loan) {
            errors.push(has_active_loan_error);
        } else {
            if ($("#client_id").val() == '') {
                if (keywords.client_is_required != undefined) {
                    errors.push(keywords.client_is_required);
                } else {
                    errors.push(keywords.client_is_required);
                }
            }
            if ($("#loan_reason").val() == '') {
                if (keywords.loan_reason_is_required != undefined) {
                    errors.push(keywords.loan_reason_is_required);
                } else {
                    errors.push(keywords.loan_reason_is_required);
                }
            }
            if ($("#loan_type").val() == '') {
                if (keywords.loan_type_is_required != undefined) {
                    errors.push(keywords.loan_type_is_required);
                } else {
                    errors.push(keywords.loan_type_is_required);
                }
            }
            if ($("#amount").val() == '' || $("#amount").val() == null) {
                if (keywords.requested_amount_is_required != undefined) {
                    errors.push(keywords.requested_amount_is_required);
                } else {
                    errors.push(keywords.requested_amount_is_required);
                }
            }
            if ($("#date_of_payment").val() == '') {
                if (keywords.salary_date_is_required != undefined) {
                    errors.push(keywords.salary_date_is_required);
                } else {
                    errors.push(keywords.salary_date_is_required);
                }
            }
            $('input[name^="other_amount"]').each(function () {
                if ($(this).val() == '') {
                    if (keywords.expanse_amount_is_required != undefined) {
                        errors.push(keywords.expanse_amount_is_required);
                    } else {
                        errors.push(keywords.expanse_amount_is_required);
                    }
                }
            });
            $('input[name^="expense_proof_image"]').each(function () {
                if ($(this).val() == '') {
                    if (keywords.expanse_image_is_required != undefined) {
                        errors.push(keywords.expanse_image_is_required);
                    } else {
                        errors.push(keywords.expanse_image_is_required);
                    }
                }
            });
            $('input[name^="income_amount"]').each(function () {
                if ($(this).val() == '') {
                    if (keywords.income_amount_is_required != undefined) {
                        errors.push(keywords.income_amount_is_required);
                    } else {
                        errors.push(keywords.income_amount_is_required);
                    }
                }
            });
            $('input[name^="income_proof_image"]').each(function (i, val) {
                if ($(this).val() == '' && ($('input[name="image_hidden[' + i + ']"]').val() == '' || typeof $('input[name="image_hidden[' + i + ']"]').val() == 'undefined')) {
                    if (keywords.income_image_is_required != undefined) {
                        errors.push(keywords.income_image_is_required);
                    } else {
                        errors.push(keywords.income_image_is_required);
                    }
                }
            });
        }
        return errors;
    },
    applicationStore: function (form) {
        var _this = this;
        if (has_active_loan!=undefined && has_active_loan) {
            $('.loan-errors').find('.list-group').html('<li>' + has_active_loan_error + '</li>');
        } else {
            if (_this.data.process == false) {
                var htmlContent = '<div class="text-center"><table class="table table-bordered">' +
                    '<tr><td>' + keywords['Requested Amount'] + '</td><td>' + $('[name="amount"]').val() + '</td></tr>' +
                    '<tr><td>' + keywords['Origination Fee'] + '</td><td>' + $('#origination_fee_amount').html() + '</td></tr>' +
                    '<tr><td>' + keywords['Tax on Origination Fee'] + '</td><td>' + $('#territory_tax').html() + '</td></tr>';
                if ($("#apr").val() != '') {
                    htmlContent += '<tr><td>' + keywords['APR'] + ' (%)</td><td>' + $('#apr').val() + '</td></tr>';
                }
                htmlContent += '<tr><td>' + keywords['Interest'] + '</td><td>' + $('#interest_amount_span').html() + '</td></tr>' +
                    '<tr><td>' + keywords['Tax On Interest'] + '</td><td>' + $('#tax_interest').html() + '</td></tr>' +
                    '<tr><td>' + keywords['Credit Amount'] + '</td><td>' + $('#credit_amount').html() + '</td></tr>' +
                    '</table></div>' +
                    '<h5>' + keywords['Signature'] + '</h5>' +
                    '<canvas style="border: 1px solid #000;" id="loan_signature" width="400" height="200"></canvas>' +
                    '<p class="error signature_error"></p>' +
                    '<button class="btn btn-danger clearSignature">' + keywords['Clear'] + '</button><br><br>' +
                    '<label style="mt-2">' +
                    '<input type="checkbox" name="terms" value="1" id="loan_model_terms_checkbox">  ' + keywords['IAgreewith'] +
                    '<a href="' + siteURL + 'loan-contract/' + $('#loan_type').val() + '" target="_blank"> ' + keywords['Loan agreement'] + '</a>.' +
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
            } else {
                _this.data.process = false;
                $('.income-type').prop('disabled', false);
                $('.loan-type').prop('disabled', false);
                $('.loan-errors').hide();
                var options = {
                    target: '',
                    url: $(form).attr('action'),
                    type: 'POST',
                    beforeSend: function () {
                        $('.income-type').prop('disabled', false);
                        $('.loan-type').prop('disabled', false);
                    },
                    success: function (res) {
                        $("#applyLoanModal").scrollTop(0);
                        $('.loan-success').show();
                        window.location.href = siteURL + '/client/loans';
                    },
                    error: function (jqXHR, exception) {
                        var Response = jqXHR.responseText;
                        Response = $.parseJSON(Response);
                        displayErrorMessages(Response, $(_this.el.loanErrors), 'ul');
                        $("#applyLoanModal").scrollTop(0);
                    }
                };
                $(form).ajaxSubmit(options);
                return false;
            }
        }
    },
    incomeAmountHtml: function (type, incomeAmount) {
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
            incomeAmount['html'] = '<button class="btn btn-primary" type="button"><i class="material-icons">attach_file</i></button>';
        }
        var addbuttonhtml = '<button class="add-btn btn btn-success addNewIncome" type="button"><i class="material-icons">add</i></button>';
        var datehtml = '           <label class="control-label">' + keywords['Date of payment'] + ' *</label>' +
            '           <input type="text" name="date_of_payment[' + _this.data.amount_index + ']" id="date_of_payment" class="income-date form-control date-picker" value="' + incomeAmount['date'] + '">' +
            '<span class="error" for="date_of_payment[' + _this.data.amount_index + ']"></span>';
        var deleteButton = '';
        var income_type = 1;
        var income_name = keywords['Gross salary'];
        if (type != 'main') {
            addbuttonhtml = '';
            datehtml = '';
            deleteButton = '<button class="delete-btn btn btn-danger removeIncome" type="button"><i class="material-icons">delete</i></button>';
            income_type = 2;
            income_name = keywords['Other Income'];
        }
        str = ' <div class="row income-item">' +
            '       <div class="column col-md-2">' +
            '           <div class="form-group">' +
            '               <label class="control-label">' + keywords['Type'] + '</label>' +
            '               <label class="income-type-name form-control">' + income_name + '</label>' +
            '               <input type="hidden" class="income-type" name="income_type[' + _this.data.amount_index + ']" value="' + income_type + '">' +
            '           </div>' +
            '       </div>' +
            '       <div class="column col-md-2">' +
            '           <label class="control-label">' + keywords['Amount'] + ' *</label>' +
            '           <input value="' + incomeAmount['amount'] + '" type="number" min="0" step="0.01" name="income_amount[' + _this.data.amount_index + ']" class="income-amount form-control numeric-input">' +
            '<span class="error" for="income_amount[' + _this.data.amount_index + ']"></span>' +
            '       </div>' +
            '       <div class="column col-md-2 date-holder">' + datehtml + '</div>' +
            '       <div class="column col-md-4">' +
            '           <label class="control-label" data-toggle="tooltip" title="png,gif,jpg,jpeg,doc,docx,pdf">' + keywords['UploadLastPaySlip'] + ' *</label><br>' +
            '           <input type="file" name="income_proof_image[' + _this.data.amount_index + ']" id="income_proof_image" onchange="filesizeValidation(this)" class="proof-photo form-control" accept="image/png,image/gif,image/jpeg,image/jpg,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf">' +
            '           <div class="has-error">' +
            '               <span class="help-block"></span>' +
            '           </div>' +
            '           <span class="error" for="income_proof_image[' + _this.data.amount_index + ']"></span>' +
            '           <input type="hidden" name="image_hidden[' + _this.data.amount_index + ']" class="income-proof-image-hidden">' +
            '       </div>' +
            '       <div class="column actions text-right col-md-2">' +
            '           <button class="clear-btn btn btn-info clearIncome" type="button"><i class="material-icons">remove</i></button>' +
            '           <input type="hidden" name="income_id[' + _this.data.amount_index + ']" class="income-id" value="' + incomeAmount['id'] + '">' + addbuttonhtml + deleteButton +
            '           <a href="' + incomeAmount['file_name'] + '" class="income-image">' + incomeAmount['html'] + '</a>' +
            '       </div>' +
            '   </div>';
        return str;
    },
};