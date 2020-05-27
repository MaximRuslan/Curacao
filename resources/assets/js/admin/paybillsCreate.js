var paybillsCreate = {
    el: {
        steps: $("#rootwizard"),
        userInfoform: '#usersInfoForm',
        userBankInfoform: '#usersBankInfoForm',
    },
    data: {
        steps_el: '',
        enableAllSteps: false,
        steps_init: 0,
        init_steps: false,
        type: '',
        user_id: '',
        process: false,

        email_index: 0,
        document_index: 0,
        telephone_index: 0,
        cellphone_index: 0,

        bank_index: 0,
        reference_index: 0,

        country_code: '',
        phone_length: '',

        status_options: '',

        newIndex: 0,
        currentIndex: 0,
        primary: true,
        first_email: true,
    },
    init() {
        var _this = this;
        _this.data.type = window.type;
        _this.data.user_id = window.user_id;
        _this.bindUiActions();
        initTooltip();
        datePickerInit();
        _this.data.status_options = $('[name="status"]').html();
        $('.select2single').select2();
        $('#branch_id').select2({
            placeholder: 'Select Branch'
        });

        if ($('#country_id').val() != '') {
            _this.countryWiseData($('#country_id').val());
        }

        if (_this.data.type == 'edit') {
            _this.setEdit(_this.data.user_id);
            _this.el.steps.bootstrapWizard('enable', 0);
            _this.el.steps.bootstrapWizard('enable', 1);
        } else {
            _this.el.steps.find('.tab2').parent().addClass('disabled');
        }
    },
    bindUiActions() {
        var _this = this;

        _this.initiateSteps();

        $(document).on('change', '#country_id', function (e) {
            e.preventDefault();
            _this.countryWiseData($(this).val());
        });

        $(document).on('click', '.addNewEmailPrimary', function (e) {
            e.preventDefault();
            var checked = '';
            var display = '';
            if (_this.data.first_email) {
                checked = 'checked';
                display = 'style="display:none;"';
            }
            var primary = '<input type="radio" name="primary" value="' + _this.data.email_index + '" ' + checked + '>';
            if (!_this.data.primary) {
                primary = '';
            }
            var str = '<tr>' +
                '   <td>' +
                '       <input type="email" name="secondary_email[' + _this.data.email_index + ']" class="form-control">' +
                '       <span class="error" for="secondary_email[' + _this.data.email_index + ']"></span>' +
                '   </td>' +
                '   <td>' + primary +
                '   </td>' +
                '   <td>' +
                '   </td>' +
                '   <td>' +
                '       <input type="hidden" name="secondary_email_id[' + _this.data.email_index + ']" value="">' +
                '       <button type="button" ' + display + ' class="delete btn btn-danger deleteTableRow" data-id="' + _this.data.email_index + '" data-toggle="tooltip" title="Delete Email">' +
                '           <i class="fa fa-trash"></i>' +
                '       </button>' +
                '   </td>' +
                '</tr>';

            $('#user_emails').append(str);
            _this.data.first_email = false;
            $('[name="secondary_email[' + _this.data.email_index + ']"]').rules("add", {
                required: true
            });
            _this.data.email_index++;
            initTooltip();
        });

        $(document).on('click', '.deleteTableRow', function (e) {
            e.preventDefault();
            $(this).parent().parent().remove();
        });

        $(document).on('click', '.addNewCellphone', function (e) {
            e.preventDefault();
            if ($('#country_id').val() != '') {
                str = '<tr>' +
                    '    <td>' +
                    '         <div class="input-group">' +
                    '            <span class="input-group-addon country_code_label phoneCode">' + _this.data.country_code + '</span>' +
                    '            <input type="number" name="cellphone[' + _this.data.cellphone_index + ']" class="form-control phoneCodeLimit">' +
                    '         </div>' +
                    '         <span class="error" for="cellphone[' + _this.data.cellphone_index + ']"></span>' +
                    '    </td>' +
                    '    <td>' +
                    '        <button type="button" class="delete btn btn-danger deleteTableRow">' +
                    '            <i class="fa fa-trash"></i>' +
                    '        </button>' +
                    '    </td>' +
                    '</tr>';

                $('#user_cellphones').append(str);
                $('[name="cellphone[' + _this.data.cellphone_index + ']"]').rules("add", {
                    required: true,
                    minlength: _this.data.phone_length,
                    maxlength: _this.data.phone_length,
                });
                _this.data.cellphone_index++;
            } else {
                alert('Please select country first.');
            }
        });

        $(document).on('click', '.addNewTelephone', function (e) {
            e.preventDefault();
            if ($('#country_id').val() != '') {
                str = '<tr>' +
                    '    <td>' +
                    '         <div class="input-group">' +
                    '            <span class="input-group-addon country_code_label phoneCode">' + _this.data.country_code + '</span>' +
                    '            <input type="number" name="telephone[' + _this.data.telephone_index + ']" class="form-control phoneCodeLimit">' +
                    '         </div>' +
                    '         <span class="error" for="telephone[' + _this.data.telephone_index + ']"></span>' +
                    '    </td>' +
                    '    <td>' +
                    '        <button  type="button" class="delete btn btn-danger deleteTableRow">' +
                    '            <i class="fa fa-trash"></i>' +
                    '        </button>' +
                    '    </td>' +
                    '</tr>';

                $('#user_telephones').append(str);
                $('[name="telephone[' + _this.data.telephone_index + ']"]').rules("add", {
                    required: true,
                    minlength: _this.data.phone_length,
                    maxlength: _this.data.phone_length,
                });
                _this.data.telephone_index++;
            } else {
                alert('Please select country first.');
            }
        });

        $(document).on('click', '.saveButtonSteps', function (e) {
            e.preventDefault();
            if ($(this).data('step') == 1) {
                $(_this.el.userBankInfoform).submit();
            } else {
                $("#usersInfoForm").data('process', 'save');
                _this.el.steps.find('.button-next').click();
            }
        });

        $(document).on('click', '.addUserBankInfo', function (e) {
            e.preventDefault();
            $.ajax({
                datatype: 'json',
                method: 'get',
                url: adminAjaxURL + 'users/' + _this.data.user_id + '/country-banks',
                success(data) {
                    $('.tableBankInfo').append(_this.banksHtml('', data['banks']));
                    $('#usersBankInfoForm').find('[name="bank_id[' + _this.data.bank_index + ']"]').rules('add', {
                        required: true,
                    });
                    $('#usersBankInfoForm').find('[name="account_number[' + _this.data.bank_index + ']"]').rules('add', {
                        required: true,
                    });
                    $('#usersBankInfoForm').find('[name="name_on_account[' + _this.data.bank_index + ']"]').rules('add', {
                        required: true,
                    });
                    $('#usersBankInfoForm').find('[name="address_on_account[' + _this.data.bank_index + ']"]').rules('add', {
                        required: true,
                    });
                    $('#usersBankInfoForm').find('[name="bank_id[' + _this.data.bank_index + ']"]').select2();
                    _this.data.bank_index++;
                }
            });
        });

        $(document).on('click', '.resendVerificationMail', function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'get',
                url: adminAjaxURL + 'users/' + $(this).data('user-id') + '/resend/' + $(this).data('id'),
                success: function (data) {
                    $('.usersAlert').show();
                    $('.usersAlert').html('<div class="alert alert-success" role="alert">' +
                        '                    Resend verification mail is sent to your email id.' +
                        '                </div>');
                    setTimeout(function () {
                        $('.usersAlert').hide();
                        $('.usersAlert').html('');
                    }, 4000)
                }
            });
        });

        $(document).on('click', '.editTableEmail', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            $('[name="primary"][value="' + id + '"]').hide();
            $('.secondaryEmailElement[data-id="' + id + '"]').removeAttr('readonly');
            $(this).html('<i class="fa fa-save"></i>');
            $(this).removeClass('editTableEmail');
            $(this).addClass('saveTableEmail');
        });

        $(document).on('click', '.saveTableEmail', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            $.ajax({
                dataType: 'json',
                method: 'post',
                url: adminAjaxURL + 'users/emails/' + $(this).data('info-id') + '/store',
                data: {
                    value: $('.secondaryEmailElement[data-id="' + id + '"]').val()
                },
                success: function (data) {
                    _this.setSecondaryEmails(data['infos']);
                }
            });
        });

        $(document).on('change', '[name="primary"]', function (e) {
            $('.editTableEmail').show();
            $('.deleteTableRow').show();
            $('.editTableEmail[data-id=' + $('[name="primary"]:checked').val() + ']').hide();
            $('.deleteTableRow[data-id=' + $('[name="primary"]:checked').val() + ']').hide();
        });
    },
    initiateSteps() {
        var _this = this;

        _this.data.process = false;
        _this.data.steps_el = _this.el.steps.bootstrapWizard({
            'nextSelector': '.button-next',
            'previousSelector': '.button-previous',
            'firstSelector': '.button-first',
            'lastSelector': '.button-last',
            onInit() {
                _this.validateUserInfoForm(adminAjaxURL + 'pay-bills', 'post');
            },
            onNext(tab, navigation, index) {
                if (_this.data.process) {
                    _this.data.process = false;
                    return true;
                } else {
                    if ($(tab).data('tab') == 1) {
                        _this.data.newIndex = index;
                        _this.data.currentIndex = index - 1;
                        $(_this.el.userInfoform).submit();
                    }
                    if ($(tab).data('tab') == 2) {
                        _this.data.newIndex = index;
                        _this.data.currentIndex = index - 1;
                        $(_this.el.userBankInfoform).submit();
                    }
                    return false;
                }
            },
            onTabShow(tab, navigation, currentIndex) {
                if (currentIndex == 0 && _this.data.user_id != '') {
                    _this.setEdit(_this.data.user_id);
                    _this.el.steps.find('.saveButtonSteps').data('step',0);
                }
                if (currentIndex == 1) {
                    _this.validateUserBankInfoForm(adminAjaxURL + 'users/' + _this.data.user_id + '/banks', 'post');
                    _this.getUserBankInfo(_this.data.user_id);
                    _this.el.steps.find('.saveButtonSteps').data('step',1);
                }
            }
        });
        _this.el.steps.find('.button-finish').click(function () {
            $(_this.el.userBankInfoform).submit();
        });
    },

    countryWiseData(country, district, branch) {
        var _this = this;
        if (country != '') {
            $.ajax({
                dataType: 'json',
                method: 'get',
                url: adminAjaxURL + 'countries/' + country + '/data',
                success(data) {
                    _this.data.country_code = data['country_code'];
                    _this.data.phone_length = data['phone_length'];

                    $('.phoneCode').html(_this.data.country_code);
                    if ($('.phoneCodeLimit').length > 0) {
                        $('.phoneCodeLimit').rules('add', {
                            minlength: _this.data.phone_length,
                            maxlength: _this.data.phone_length,
                        });
                    }

                    var str = '<option>Select District</option>';
                    for (var index in data['districts']) {
                        if (district != undefined && index == district) {
                            str += '<option value="' + index + '" selected>' + data['districts'][index] + '</option>';
                        } else {
                            str += '<option value="' + index + '">' + data['districts'][index] + '</option>';
                        }
                    }
                    $('#territory_id').html(str);

                    var str = '';
                    for (var index in data['branches']) {
                        if (branch != undefined && branch != [] && $.inArray(index, branch) >= 0) {
                            str += '<option value="' + index + '" selected>' + data['branches'][index] + '</option>';
                        } else {
                            str += '<option value="' + index + '">' + data['branches'][index] + '</option>';
                        }
                    }
                    $('#branch_id').html(str);
                }
            })
        } else {
            $('#territory_id').html('');
            $('#branch_id').html('');
        }
    },

    setEdit(user_id) {
        var _this = this;
        $.ajax({
            dataType: 'json',
            method: 'get',
            url: adminAjaxURL + 'pay-bills/' + user_id + '/edit',
            data: {
                type: 'json'
            },
            success(data) {
                setForm($('#usersInfoForm'), data['inputs']);
                _this.data.country_code = data['inputs']['country_code']['value'];
                _this.data.phone_length = data['inputs']['phone_length']['value'];
                _this.setTelephones(data['telephones']);
                _this.setCellphones(data['cellphones']);
                _this.setSecondaryEmails(data['emails']);
                _this.countryWiseData(data['inputs']['country']['value'], data['inputs']['territory']['value'], data['inputs']['branches']['value'])
            }
        })
    },

    validateUserInfoForm: function (url, method) {
        var _this = this;
        $(_this.el.userInfoform).data('validator', null);
        $(_this.el.userInfoform).unbind();

        validator = $(_this.el.userInfoform).validate({
            // define validation rules
            rules: {
                firstname: {
                    required: true,
                },
                lastname: {
                    required: true,
                },
                primary: {
                    required: true,
                },
                country: {required: true},
                address: {required: true},
                status: {required: true},
                transaction_type: {required: true},
                transaction_fee: {required: true},
                commission_type: {required: true},
                commission_fee: {required: true},
                // "branch[]": {required: true},
            },

            errorPlacement: function (error, element) {
                $('span[for="' + $(element).attr('name') + '"]').html(error);
                $(element).removeClass('error');
            },

            submitHandler: function (form) {
                fullLoader.on({
                    text: 'Loading !'
                });
                $('.error').html('');
                var custom_data = new FormData($('#usersInfoForm')[0]);
                $.ajax({
                    dataType: 'json',
                    method: method,
                    url: url,
                    data: custom_data,
                    processData: false,
                    contentType: false,
                    cache: false,
                    crossDomain: true,
                    success: function (data) {
                        if (data['status']) {
                            _this.data.process = true;
                            $('#usersInfoForm').find('[name="id"]').val(data['user_id']);
                            _this.data.type = 'edit';
                            _this.data.user_id = data['user_id'];
                            _this.el.steps.bootstrapWizard('enable', 1);
                            if ($("#usersInfoForm").data('process') == 'save') {
                                window.location = adminURL + 'pay-bills';
                            } else {
                                _this.el.steps.find('.button-next').click();
                            }
                        } else {
                            if (data['type'] == 'email') {
                                $('#email_error').text(data['message']);
                            }
                            if (data['type'] == 'phone') {
                                $('#phone_error').text(data['message']);
                            }
                            if (data['type'] == 'id_number') {
                                $('#id_number_error').text(data['message']);
                            }
                        }
                        fullLoader.off();
                    },
                    error: function (jqXHR, exception) {
                        var Response = jqXHR.responseText;
                        ErrorBlock = $(form);
                        Response = $.parseJSON(Response);
                        displayErrorMessages(Response, ErrorBlock, 'input');
                        fullLoader.off();
                    }
                });
            }
        });
    },

    validateUserBankInfoForm: function (url, method) {
        var _this = this;
        $(_this.el.userBankInfoform).data('validator', null);
        $(_this.el.userBankInfoform).unbind();
        validator = $(_this.el.userBankInfoform).validate({
            errorPlacement: function (error, element) {
                $('span[for="' + $(element).attr('name') + '"]').html(error);
                $(element).removeClass('error');
            },

            submitHandler: function (form) {
                fullLoader.on({
                    text: 'Loading !'
                });
                $.ajax({
                    dataType: 'json',
                    method: method,
                    url: url,
                    data: $(_this.el.userBankInfoform).serialize(),
                    success: function (data) {
                        fullLoader.off();
                        if (data['status']) {
                            _this.data.process = true;
                            window.location = adminURL + 'pay-bills';
                        }
                    },
                    error: function (jqXHR, exception) {
                        var Response = jqXHR.responseText;
                        ErrorBlock = $(form);
                        Response = $.parseJSON(Response);
                        displayErrorMessages(Response, ErrorBlock, 'input');
                        fullLoader.off();
                    }
                });
            }
        });
    },

    setTelephones(data) {
        var _this = this;
        $('#user_telephones').html('');
        for (var index in data) {
            str = '<tr>' +
                '    <td>' +
                '         <div class="input-group">' +
                '            <span class="input-group-addon country_code_label phoneCode">' + _this.data.country_code + '</span>' +
                '        <input type="number" name="telephone[' + _this.data.telephone_index + ']" class="form-control phoneCodeLimit" value="' + data[index] + '">' +
                '       </div>' +
                '         <span class="error" for="cellphone[' + _this.data.telephone_index + ']"></span>' +
                '    </td>' +
                '    <td>' +
                '        <button type="button" class="delete btn btn-danger deleteTableRow">' +
                '            <i class="fa fa-trash"></i>' +
                '        </button>' +
                '    </td>' +
                '</tr>';

            $('#user_telephones').append(str);
            $('[name="telephone[' + _this.data.telephone_index + ']"]').rules("add", {
                required: true,
                minlength: _this.data.phone_length,
                maxlength: _this.data.phone_length,
            });
            _this.data.telephone_index++;
        }
    },

    setCellphones(data) {
        var _this = this;
        $('#user_cellphones').html('');
        for (var index in data) {
            str = '<tr>' +
                '    <td>' +
                '         <div class="input-group">' +
                '            <span class="input-group-addon country_code_label phoneCode">' + _this.data.country_code + '</span>' +
                '        <input type="number" name="cellphone[' + _this.data.cellphone_index + ']" class="form-control phoneCodeLimit" value="' + data[index] + '">' +
                '           </div>' +
                '         <span class="error" for="cellphone[' + _this.data.cellphone_index + ']"></span>' +
                '    </td>' +
                '    <td>' +
                '        <button type="button" class="delete btn btn-danger deleteTableRow">' +
                '            <i class="fa fa-trash"></i>' +
                '        </button>' +
                '    </td>' +
                '</tr>';
            $('#user_cellphones').append(str);
            $('[name="cellphone[' + _this.data.cellphone_index + ']"]').rules("add", {
                required: true,
                minlength: _this.data.phone_length,
                maxlength: _this.data.phone_length,
            });
            _this.data.cellphone_index++;
        }

    },

    setSecondaryEmails(data) {
        var _this = this;
        str = '';
        $('#user_emails').html(str);
        _this.data.primary = false;
        _this.data.first_email = false;
        for (var index in data) {
            var resendVerification = 'Yes';
            var checked = '';
            var editButton = '<button type="button" data-info-id="' + data[index]['id'] + '" class="btn btn-primary editTableEmail" data-id="' + _this.data.email_index + '"><i class="fa fa-pencil"></i></button>';
            var deleteButton = '<button type="button" class="delete btn btn-danger deleteTableRow" data-id="' + _this.data.email_index + '">' +
                '            <i class="fa fa-trash"></i>' +
                '        </button>';
            var disabled = 'readonly';
            if (data[index]['primary'] == 1) {
                checked = 'checked';
                editButton = '';
                deleteButton = '';
            }
            var primary = '<input type="radio" name="primary" value="' + _this.data.email_index + '" ' + checked + '>';
            if (data[index]['is_verified'] == null || data[index]['is_verified'] == 0) {
                resendVerification = '<a href="#nogo" class="resendVerificationMail" data-user-id="' + data[index]['user_id'] + '" data-id="' + data[index]['id'] + '" data-toggle="tooltip" title="Resend verification mail">' +
                    'Verify' +
                    '</a>';
                if (data[index]['primary'] == 0) {
                    primary = '';
                }
            }
            str = '<tr>' +
                '    <td>' +
                '        <input type="email" name="secondary_email[' + _this.data.email_index + ']" data-id="' + _this.data.email_index + '" class="form-control secondaryEmailElement" value="' + data[index]['value'] + '" ' + disabled + '>' +
                '        <span class="error" for="secondary_email[' + _this.data.email_index + ']"></span>' +
                '    </td>' +
                '   <td>' + primary +
                '   </td>' +
                '   <td>' + resendVerification +
                '   </td>' +
                '    <td>' + editButton +
                '       <input type="hidden" name="secondary_email_id[' + _this.data.email_index + ']" value="' + data[index]['id'] + '">' +
                deleteButton +
                '    </td>' +
                '</tr>';
            $('#user_emails').append(str);
            $('[name="secondary_email[' + _this.data.email_index + ']"]').rules("add", {
                required: true
            });
            _this.data.email_index++;
            initTooltip();
        }
    },
    getUserBankInfo(user_id) {
        var _this = this;
        $.ajax({
            dataType: 'json',
            method: 'get',
            url: adminAjaxURL + 'users/' + user_id + '/banks',
            success: function (data) {
                str = '';
                $('.tableBankInfo').html(str);
                for (var index in data['banks']) {
                    var bank = data['banks'][index];
                    $('.tableBankInfo').append(_this.banksHtml(bank, data['banks_data']));
                    $('#usersBankInfoForm').find('[name="bank_id[' + _this.data.bank_index + ']"]').rules('add', {
                        required: true,
                    });
                    $('#usersBankInfoForm').find('[name="account_number[' + _this.data.bank_index + ']"]').rules('add', {
                        required: true,
                    });
                    $('#usersBankInfoForm').find('[name="name_on_account[' + _this.data.bank_index + ']"]').rules('add', {
                        required: true,
                    });
                    $('#usersBankInfoForm').find('[name="address_on_account[' + _this.data.bank_index + ']"]').rules('add', {
                        required: true,
                    });
                    $('#usersBankInfoForm').find('[name="bank_id[' + _this.data.bank_index + ']"]').select2();
                    _this.data.bank_index++;
                }
            }
        });
    },

    banksHtml(bank, banks) {
        var _this = this;
        if (bank == undefined || bank == '') {
            bank = {
                bank_id: '',
                account_number: '',
                name_on_account: '',
                address_on_account: '',
            };
        }
        str = '<tr>' +
            '     <td>';
        str += '<select name="bank_id[' + _this.data.bank_index + ']" class="form-control">' +
            '<option value="">Select Bank</option>';
        for (var i in banks) {
            var selected = '';
            if (i == bank['bank_id']) {
                selected = 'selected';
            }
            str += '<option value="' + i + '" ' + selected + '>' + banks[i] + '</option>';
        }
        str += '</select>' +
            '   <span class="error" for="bank_id[' + _this.data.bank_index + ']"></span>';
        str += '     <td>' +
            '         <input class="form-control" type="text" name="account_number[' + _this.data.bank_index + ']"' +
            '                value="' + bank['account_number'] + '"' +
            '                placeholder="Account Number">' +
            '   <span class="error" for="account_number[' + _this.data.bank_index + ']"></span>' +
            '     </td>' +
            '     <td>' +
            '         <input class="form-control" type="text" name="name_on_account[' + _this.data.bank_index + ']"' +
            '                value="' + bank['name_on_account'] + '"' +
            '                placeholder="Name on Account">' +
            '           <span class="error" for="name_on_account[' + _this.data.bank_index + ']"></span>' +
            '     </td>' +
            '     <td>' +
            '         <textarea class="form-control" type="text" name="address_on_account[' + _this.data.bank_index + ']" ' +
            '                placeholder="Address On Account">' + bank['address_on_account'] + '</textarea>' +
            '           <span class="error" for="address_on_account[' + _this.data.bank_index + ']"></span>' +
            '     </td>' +
            '     <td>' +
            '         <button type="button" class="deleteTableRow btn btn-danger">' +
            '             <i class="fa fa-trash"></i>' +
            '         </button>' +
            '     </td>' +
            '  </tr>';
        return str;
    },
};
