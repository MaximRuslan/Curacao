var usersCreate = {
    el: {
        steps: $("#wizard-validation-form"),
        userInfoform: '#usersInfoForm',
        userWorkInfoform: '#usersWorkInfoForm',
        userBankInfoform: '#usersBankInfoForm',
        userReferenceInfoForm: '#usersReferenceInfoForm',
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

        relationShips: [],
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
        $('#role_id').val(3).select2();
        _this.toggleRole($('#role_id').val());
        _this.toggleCivilStatus($('#civil_status_select').val());
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
            _this.el.steps.bootstrapWizard('enable', 2);
            _this.el.steps.bootstrapWizard('enable', 3);
        } else {
            _this.el.steps.find('.tab2').parent().addClass('disabled');
            _this.el.steps.find('.tab3').parent().addClass('disabled');
            _this.el.steps.find('.tab4').parent().addClass('disabled');
        }
    },
    bindUiActions() {
        var _this = this;

        _this.initiateSteps();

        $(document).on('change', '#role_id', function (e) {
            e.preventDefault();
            _this.toggleRole($(this).val());
        });

        $(document).on('change', '#country_id', function (e) {
            e.preventDefault();
            _this.countryWiseData($(this).val());
        });

        $(document).on('change', '#civil_status_select', function (e) {
            e.preventDefault();
            _this.toggleCivilStatus($(this).val());

        });

        $(document).on('click', '.addNewEmailPrimary', function (e) {
            console.log($(this));
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
                '       <button ' + display + ' class="delete btn btn-danger deleteTableRow" type="button" data-id="' + _this.data.email_index + '" data-toggle="tooltip" title="Delete Email">' +
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

        $(document).on('click', '.addNewOtherDocument', function (e) {
            e.preventDefault();
            var str = '<tr>' +
                '   <td>' +
                '       <input type="file" name="other_document[' + _this.data.document_index + ']" class="form-control">' +
                '       <span class="error" for="other_document[' + _this.data.document_index + ']"></span>' +
                '   </td>' +
                '   <td>' +
                '       <input type="text" name="other_document_name[' + _this.data.document_index + ']" class="form-control">' +
                '       <span class="error" for="other_document_name[' + _this.data.document_index + ']"></span>' +
                '   </td>' +
                '   <td>' +
                '       <button type="button" class="btn btn-danger deleteOtherDocument" data-id="" data-toggle="tooltip" title="Delete Other Document">' +
                '           <i class="fa fa-trash"></i>' +
                '       </button>' +
                '   </td>' +
                '</tr>';
            $('#other_document_table').append(str);
            $('[name="other_document[' + _this.data.document_index + ']"]').rules("add", {
                required: true
            });
            $('[name="other_document_name[' + _this.data.document_index + ']"]').rules("add", {
                required: true
            });
            _this.data.document_index++;
        });

        $(document).on('click', '.deleteOtherDocument', function (e) {
            e.preventDefault();
            if ($(this).data('id') != '') {
                var _this = this;
                $.ajax({
                    dataType: 'json',
                    method: 'delete',
                    url: adminAjaxURL + 'documents/' + $(this).data('id'),
                    success: function (data) {
                        if (data['status']) {
                            $(_this).parent().parent().remove();
                        }
                    }
                });
            } else {
                $(this).parent().parent().remove();
            }
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
            } else {
                alert('Please select country first.');
            }
        });

        $(document).on('click', '.saveButtonSteps', function (e) {
            e.preventDefault();
            if ($(this).data('step') == 3) {
                $(_this.el.userReferenceInfoForm).submit();
            } else {
                $("#wizard-validation-form").data('process', 'save');
                _this.el.steps.find('.button-next').click();
            }
        });

        $(document).on('click', '.addUserWorkInfo', function (e) {
            e.preventDefault();
            $('#usersWorkInfoForm')[0].reset();
            $('#usersWorkInfoForm').find('[name="id"]').val('');
            $('#usersWorkInfoForm').find('.select2single').val('').select2();
            _this.validateWorkInfoForm(adminAjaxURL + 'users/' + _this.data.user_id + '/works', 'post');
            $('#userWorkModal').find('input,select,number,textarea').prop('disabled', false);
            $('#userWorkModal').find('button[type="submit"]').show();
            $('#userWorkModal').modal('show');
        });

        $(document).on('click', '.editUserWorkInfo', function (e) {
            e.preventDefault();
            var type = $(this).data('type');
            $.ajax({
                dataType: 'json',
                method: 'get',
                url: adminAjaxURL + 'users/' + _this.data.user_id + '/works/' + $(this).data('id') + '/edit',
                success: function (data) {
                    $('#usersWorkInfoForm')[0].reset();
                    setForm('#usersWorkInfoForm', data.work);
                    $('#userWorkModal').modal('show');
                    _this.validateWorkInfoForm(adminAjaxURL + 'users/' + _this.data.user_id + '/works', 'post');
                    if (type != undefined && type == 'view') {
                        $('#userWorkModal').find('input,select,number,textarea').prop('disabled', true);
                        $('#userWorkModal').find('button[type="submit"]').hide();
                    } else {
                        $('#userWorkModal').find('input,select,number,textarea').prop('disabled', false);
                        $('#userWorkModal').find('button[type="submit"]').show();
                    }
                }
            });
        });

        $(document).on('click', '.deleteUserWorkinfo', function (e) {
            e.preventDefault();
            if (confirm('Are you sure you want to delete this work info?')) {
                $.ajax({
                    dataType: 'json',
                    method: 'delete',
                    url: adminAjaxURL + 'users/' + _this.data.user_id + '/works/' + $(this).data('id'),
                    success: function (data) {
                        _this.getUserWorkInfo(_this.data.user_id);
                    }
                });
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

        $(document).on('click', '.addReferrenceButton', function (e) {
            _this.addNewReferenceRow();
        });

        $(document).on('click', '.resendVerificationMail', function (e) {
            e.preventDefault();
            fullLoader.on();
            $.ajax({
                dataType: 'json',
                method: 'get',
                url: adminAjaxURL + 'users/' + $(this).data('user-id') + '/resend/' + $(this).data('id'),
                success: function (data) {
                    fullLoader.off();
                    swal('Resend verification mail is sent to your email id.').then(function () {
                        $("#wizard-validation-form").data('process', 'save');
                        _this.el.steps.find('.button-next').click();
                    });
                    // $('.usersAlert').show();
                    // $('.usersAlert').html('<div class="alert alert-success" role="alert">' +
                    //     '                    Resend verification mail is sent to your email id.' +
                    //     '                </div>');
                    // setTimeout(function () {
                    //     $('.usersAlert').hide();
                    //     $('.usersAlert').html('');
                    // }, 4000)
                },
                error: function (jqxhr) {
                    fullLoader.off();
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
                    _this.setSecondaryEmails(data['infos'], data['complete_profile']);
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
                if (_this.data.init_steps == false) {
                    _this.data.init_steps = true;
                }
                _this.validateUserInfoForm(adminAjaxURL + 'users', 'post');
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
                        fullLoader.on({
                            text: 'Loading !'
                        });
                        if ($('[name="working_type"]:checked').val() == 1) {
                            $.ajax({
                                dataType: 'json',
                                method: 'get',
                                url: adminAjaxURL + 'users/' + _this.data.user_id + '/works',
                                success: function (data) {
                                    if (data['works'] != undefined && data['works'].length > 0) {
                                        _this.storeWorkingType(1);
                                        _this.data.process = true;
                                        if ($("#wizard-validation-form").data('process') == 'save') {
                                            window.location = adminURL + 'users';
                                        } else {
                                            _this.el.steps.find('.button-next').click();
                                        }
                                    } else {
                                        $('#main_message_step2').html('Employment information is required.');
                                    }
                                    fullLoader.off();
                                }
                            });
                        } else {
                            $('#main_message_step2').html('');
                            _this.storeWorkingType(2);
                            _this.data.process = true;
                            if ($("#wizard-validation-form").data('process') == 'save') {
                                window.location = adminURL + 'users';
                            } else {
                                _this.el.steps.find('.button-next').click();
                            }
                            fullLoader.off();
                        }
                    }
                    if ($(tab).data('tab') == 3) {
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
                    _this.el.steps.find('.saveButtonSteps').data('step', 0);
                }
                if (currentIndex == 1) {
                    _this.getUserWorkInfo(_this.data.user_id);
                    _this.el.steps.find('.saveButtonSteps').data('step', 1);
                }
                if (currentIndex == 2) {
                    _this.validateUserBankInfoForm(adminAjaxURL + 'users/' + _this.data.user_id + '/banks', 'post');
                    _this.getUserBankInfo(_this.data.user_id);
                    _this.el.steps.find('.saveButtonSteps').data('step', 2);
                }
                if (currentIndex == 3) {
                    _this.validateUserReferenceInfoForm(adminAjaxURL + 'users/' + _this.data.user_id + '/references', 'post');
                    _this.getUserReferenceInfo(_this.data.user_id);
                    _this.el.steps.find('.saveButtonSteps').data('step', 3);
                }
            }
        });
        _this.el.steps.find('.button-finish').click(function () {
            $(_this.el.userReferenceInfoForm).submit();
        });
    },

    toggleRole(role) {
        var _this = this;
        $('.status-group').show();
        if (role == 3) {
            $('.paginationButton').show();

            $('#wizard-validation-form').bootstrapWizard('display', 1);
            $('#wizard-validation-form').bootstrapWizard('display', 2);
            $('#wizard-validation-form').bootstrapWizard('display', 3);
            $('.jq__client').show();
            $('.jq__not_client').hide();
            $('.jq__estrick').html('');

            $('[name="status"]').html(_this.data.status_options);

            $('[name="status"]').rules('add', {
                required: true
            });
            $('[name="place_of_birth"]').rules('add', {
                required: true
            });
            $('[name="dob"]').rules('add', {
                required: true
            });
            $('[name="scan_id"]').rules('add', {
                required: true
            });
            $('[name="address_proof"]').rules('add', {
                required: true
            });
            $('[name="payslip1"]').rules('add', {
                required: true
            });
            $('[name="payslip2"]').rules('add', {
                required: true
            });

            $('[name="branch[]"]').rules("remove", "required");
        } else {
            $('.paginationButton').hide();

            $('#wizard-validation-form').bootstrapWizard('hide', 1);
            $('#wizard-validation-form').bootstrapWizard('hide', 2);
            $('#wizard-validation-form').bootstrapWizard('hide', 3);
            $('.jq__client').hide();
            $('.jq__not_client').show();
            $('a[href$="next"]').text('Save And Close');
            $('.jq__estrick').html('*');
            $('[name="status"]').rules("remove", "required");
            $('[name="place_of_birth"]').rules("remove", "required");
            $('[name="dob"]').rules("remove", "required");
            $('[name="payslip1"]').rules("remove", "required");
            $('[name="payslip2"]').rules("remove", "required");
            $('[name="scan_id"]').rules("remove", "required");
            $('[name="address_proof"]').rules("remove", "required");
            $('[name="status"] option[data-role="1"]').remove();
            if (role != 1) {
                if (role == 2) {
                    $('.jq_branch').show();
                    $('[name="branch[]"]').rules('add', {
                        required: true
                    });
                } else {
                    $('.jq_branch').hide();
                    $('[name="branch[]"]').rules("remove", "required");
                }
            } else {
                $('.status-group').hide();
                $('.jq_branch').hide();
                $('[name="branch[]"]').rules("remove", "required");
            }
        }
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

                    var str = '<option value="">Select District</option>';
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
                        if (branch != undefined && $.inArray('index', branch)) {
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

    toggleCivilStatus(status) {
        if (status == 1) {
            $('[name="spouse_first_name"]').rules("remove", "required");
            $('[name="spouse_last_name"]').rules("remove", "required");
            $('[name="spouse_first_name"]').parent().parent().hide();
            $('[name="spouse_last_name"]').parent().parent().hide();
        } else if (status == 2) {
            $('[name="spouse_first_name"]').rules('add', {
                required: true
            });
            $('[name="spouse_last_name"]').rules('add', {
                required: true
            });
            $('[name="spouse_first_name"]').parent().parent().show();
            $('[name="spouse_last_name"]').parent().parent().show();
        } else {
            $('[name="spouse_first_name"]').rules("remove", "required");
            $('[name="spouse_last_name"]').rules("remove", "required");
            $('[name="spouse_first_name"]').parent().parent().hide();
            $('[name="spouse_last_name"]').parent().parent().hide();
        }
    },

    setEdit(user_id) {
        var _this = this;
        $.ajax({
            dataType: 'json',
            method: 'get',
            url: adminAjaxURL + 'users/' + user_id + '/edit',
            data: {
                type: 'json'
            },
            success(data) {
                _this.toggleRole(data['inputs']['role_id']['value']);
                setForm($('#usersInfoForm'), data['inputs']);
                $('[name="referred_by"]').remove();
                $('#referred_by_div').html('');
                if (data['inputs']['referred_by']['value'] != null || data['inputs']['referred_by']['value']) {
                    $('#referred_by_div').html('<label>Referred By</label><br><div>' + data['inputs']['referred_by']['value'] + '</div>');
                }
                _this.data.country_code = data['inputs']['country_code']['value'];
                _this.data.phone_length = data['inputs']['phone_length']['value'];
                _this.setTelephones(data['telephones']);
                _this.setCellphones(data['cellphones']);
                _this.setOtherDocuments(data['other_documents']);
                _this.setSecondaryEmails(data['emails'], data['inputs']['complete_profile']['value']);
                _this.toggleCivilStatus(data['inputs']['civil_status']['value']);
                _this.countryWiseData(data['inputs']['country']['value'], data['inputs']['territory']['value'], data['inputs']['branches']['value'])
                if (data.inputs.scan_id.value != '') {
                    $('[name="scan_id"]').rules("remove", "required");
                }
                if (data.inputs.address_proof.value != '') {
                    $('[name="address_proof"]').rules("remove", "required");
                }
                if (data.inputs.payslip1.value != '') {
                    $('[name="payslip1"]').rules("remove", "required");
                }
                if (data.inputs.payslip2.value != '') {
                    $('[name="payslip2"]').rules("remove", "required");
                }
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
                lang: {required: true},
                role_id: {required: true},
                country: {required: true},
                id_number: {required: true},
                payslip1: {required: true},
                payslip2: {required: true},
                address_proof: {required: true},
                address: {required: true},
                sex: {required: true},
                dob: {required: true},
                place_of_birth: {required: true},
                civil_status: {required: true},
                spouse_first_name: {required: $('#civil_status_select').val() == 2},
                spouse_last_name: {required: $('#civil_status_select').val() == 2},
                exp_date: {required: true},
                // pp_number: {required: true},
                // pp_exp_date: {required: true},
                status: {required: true},
                scan_id: {required: true},
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
                            _this.el.steps.bootstrapWizard('enable', 2);
                            _this.el.steps.bootstrapWizard('enable', 3);
                            if (data['role_id'] == 3) {
                                if ($("#wizard-validation-form").data('process') == 'save') {
                                    window.location = adminURL + 'users';
                                } else {
                                    _this.el.steps.find('.button-next').click();
                                }
                            } else {
                                window.location = adminURL + 'users';
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
                            if (data['type'] == 'referred_by') {
                                $('#referred_by_error').text(data['message']);
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
                if ($('#usersBankInfoForm').valid()) {
                    $.ajax({
                        dataType: 'json',
                        method: method,
                        url: url,
                        data: $(_this.el.userBankInfoform).serialize(),
                        success: function (data) {
                            fullLoader.off();
                            if (data['status']) {
                                _this.data.process = true;
                                if ($("#wizard-validation-form").data('process') == 'save') {
                                    window.location = adminURL + 'users';
                                } else {
                                    _this.el.steps.find('.button-next').click();
                                }
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
            }
        });
    },

    validateUserReferenceInfoForm: function (url, method) {
        var _this = this;
        $(_this.el.userReferenceInfoForm).data('validator', null);
        $(_this.el.userReferenceInfoForm).unbind();
        validator = $(_this.el.userReferenceInfoForm).validate({
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
                    data: $(_this.el.userReferenceInfoForm).serialize(),
                    success: function (data) {
                        fullLoader.off();
                        if (data['status']) {
                            _this.data.process = true;
                            window.location = adminURL + 'users';
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
                '         <span class="error" for="telephone[' + _this.data.telephone_index + ']"></span>' +
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

    setOtherDocuments(data) {
        var _this = this;
        $('#other_document_table').html('');
        for (var index in data) {
            var file = data[index];
            str = '<tr>' +
                '   <td>' +
                '       <a href="' + file['document'] + '" download target="_blank" class="btn btn-primary"><i class="fa fa-paperclip"></i></a>' +
                '       <input type="hidden" name="other_document_id[' + _this.data.document_index + ']" value="' + file['id'] + '">' +
                '   </td>' +
                '   <td><input type="text" name="other_old_document_name[' + _this.data.document_index + ']" class="form-control" value="' + file['name'] + '"></td>' +
                '   <td><button type="button" class="btn btn-danger deleteOtherDocument" data-id="' + file['id'] + '"><i class="fa fa-trash"></i></button></td>' +
                '</tr>';
            $('#other_document_table').append(str);
            $('[name="other_document_id[' + _this.data.document_index + ']"]').rules("add", {
                required: true
            });
            $('[name="other_old_document_name[' + _this.data.document_index + ']"]').rules("add", {
                required: true
            });
            _this.data.document_index++;
        }
    },

    setSecondaryEmails(data, complete_profile) {
        var _this = this;
        if (complete_profile == undefined) {
            complete_profile = 0;
        }
        str = '';
        $('#user_emails').html(str);
        _this.data.primary = false;
        _this.data.first_email = false;
        for (var index in data) {
            var resendVerification = 'Yes';
            var editButton = '<button type="button" data-info-id="' + data[index]['id'] + '" class="btn btn-primary editTableEmail" data-id="' + _this.data.email_index + '"><i class="fa fa-pencil"></i></button>';
            var deleteButton = '       <button type="button" class="delete btn btn-danger deleteTableRow" data-id="' + _this.data.email_index + '">' +
                '            <i class="fa fa-trash"></i>' +
                '        </button>';
            var checked = '';
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
                if (complete_profile == 0) {
                    resendVerification = '';
                }
                if (data[index]['primary'] == 0) {
                    primary = '';
                }
            }
            str = '<tr>' +
                '    <td>' +
                '        <input type="email" name="secondary_email[' + _this.data.email_index + ']" data-id="' + _this.data.email_index + '" class="form-control secondaryEmailElement" value="' + data[index]['value'] + '" ' + disabled + '>' +
                '        <span class="error" for="secondary_email[' + _this.data.email_index + ']"></span>' +
                '    </td>' +
                '   <td>' +
                primary +
                '   </td>' +
                '   <td>' +
                resendVerification +
                '   </td>' +
                '    <td>' +
                editButton +
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

    getUserWorkInfo(user_id) {
        $.ajax({
            dataType: 'json',
            method: 'get',
            url: adminAjaxURL + 'users/' + user_id + '/works',
            success(data) {
                str = '';
                $('.tableWorkInfo').html(str);
                $('[name="working_type"]').removeAttr('checked');
                if (data['working_type'] != null && data['working_type'] != '') {
                    $('[name="working_type"][value="' + data['working_type'] + '"]').attr('checked', true);
                } else {
                    $('[name="working_type"][value="1"]').attr('checked', true);
                }
                for (var index in data['works']) {
                    var work = data['works'][index];

                    str += '<tr>' +
                        '     <td>' + work['employer'] + '</td>' +
                        '     <td>' + work['position'] + '</td>' +
                        '     <td>' + work['employed_since'] + '</td>' +
                        '     <td>' + work['contract_expires'] + '</td>' +
                        '     <td>' +
                        '         <button type="button" class="btn btn-primary editUserWorkInfo" data-id="' + work['id'] + '">' +
                        '             <i class="fa fa-pencil"></i>' +
                        '         </button>' +
                        '         <button type="button" class="deleteUserWorkinfo btn btn-danger"  data-id="' + work['id'] + '">' +
                        '             <i class="fa fa-trash"></i>' +
                        '         </button>' +
                        '         <button type="button" class="editUserWorkInfo btn btn-primary" data-type="view"  data-id="' + work['id'] + '">' +
                        '             <i class="fa fa-eye"></i>' +
                        '         </button>' +
                        '     </td>' +
                        '  </tr>';
                }
                $('.tableWorkInfo').append(str);
            }
        });
    },

    validateWorkInfoForm(url, method) {
        var _this = this;
        $(_this.el.userWorkInfoform).data('validator', null);
        $(_this.el.userWorkInfoform).unbind();

        validator = $(_this.el.userWorkInfoform).validate({
            // define validation rules
            rules: {
                employer: {required: true},
                address: {required: true},
                telephone_code: {required: true},
                telephone: {required: true},
                position: {required: true},
                employed_since: {required: true},
                employment_type: {required: true},
                salary: {required: true},
                payment_frequency: {required: true},
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
                $.ajax({
                    dataType: 'json',
                    method: method,
                    url: url,
                    data: $(_this.el.userWorkInfoform).serialize(),
                    success: function (data) {
                        _this.getUserWorkInfo(_this.data.user_id);
                        $('#userWorkModal').modal('hide');
                        $('#main_message_step2').html('');
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

    storeWorkingType(type) {
        var _this = this;
        if (type == 1 || type == 2) {
            $.ajax({
                dataType: 'json',
                method: 'post',
                url: adminAjaxURL + 'users/' + _this.data.user_id + '/working-type',
                data: {
                    type: type
                },
                success(data) {
                }
            });
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
                $('#usersBankInfoForm').find('[name="how_much_loan"]').val(data['user']['how_much_loan']);
                $('#usersBankInfoForm').find('[name="repay_loan_2_weeks"]').val(data['user']['repay_loan_2_weeks']).select2();
                $('#usersBankInfoForm').find('[name="have_bank_loan"]').val(data['user']['have_bank_loan']).select2();
                $('#usersBankInfoForm').find('[name="have_bank_account"]').val(data['user']['have_bank_account']).select2();
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
            '         <button  class="deleteTableRow btn btn-danger">' +
            '             <i class="fa fa-trash"></i>' +
            '         </button>' +
            '     </td>' +
            '  </tr>';
        return str;
    },

    addNewReferenceRow() {
        var _this = this;
        var reference = {
            first_name: '',
            last_name: '',
            relationship: '',
            telephone: '',
            cellphone: '',
            address: '',
        };
        var str = '';

        var deleteButton = '<button type="button" class="btn btn-danger deleteTableRow"><i class="fa fa-trash"></i></button>';

        str = '<tr>' +
            '     <td>' +
            '         <input class="form-control" type="text" name="first_name[' + _this.data.reference_index + ']" value="' + reference['first_name'] + '" placeholder="First Name">' +
            '         <span class="error" for="first_name[' + _this.data.reference_index + ']"></span>' +
            '     </td>' +
            '     <td>' +
            '         <input class="form-control" type="text" name="last_name[' + _this.data.reference_index + ']" value="' + reference['last_name'] + '" placeholder="Last Name">' +
            '         <span class="error" for="last_name[' + _this.data.reference_index + ']"></span>' +
            '     </td>' +
            '     <td>';
        str += '<select class="form-control" name="relationship[' + _this.data.reference_index + ']">';
        str += '<option value="">Select Relationship</option>';
        for (var index in _this.data.relationShips) {
            var relationship = _this.data.relationShips[index];
            if (index == reference['relationship']) {
                str += '<option selected value="' + index + '">' + relationship + '</option>';
            } else {
                str += '<option value="' + index + '">' + relationship + '</option>';
            }
        }
        str += '</select>' +
            '         <span class="error" for="relationship[' + _this.data.reference_index + ']"></span>';
        str += '     </td>' +
            '     <td>' +
            '         <div class="input-group">' +
            '            <span class="input-group-addon country_code_label">' + _this.data.country_code + '</span>' +
            '            <input class="form-control" type="number" name="telephone[' + _this.data.reference_index + ']" value="' + reference['telephone'] + '" placeholder="Telephone">' +
            '     </div><span class="error" for="telephone[' + _this.data.reference_index + ']"></span></td>' +
            '     <td>' +
            '         <div class="input-group">' +
            '            <span class="input-group-addon country_code_label">' + _this.data.country_code + '</span>' +
            '         <input class="form-control" type="number" name="cellphone[' + _this.data.reference_index + ']" value="' + reference['cellphone'] + '" placeholder="Cellphone">' +
            '     </div><span class="error" for="cellphone[' + _this.data.reference_index + ']"></span></td>' +
            '     <td>' +
            '         <textarea class="form-control" type="text" name="address[' + _this.data.reference_index + ']" value="" placeholder="Address" required>' + reference['address'] + '</textarea>' +
            '            <span class="error" for="address[' + _this.data.reference_index + ']"></span>' +
            '     </td>' +
            '     <td>' + deleteButton +
            '     </td>' +
            '  </tr>';
        $('.tableReferenceInfo').append(str);
        $(_this.el.userReferenceInfoForm).find('[name="first_name[' + _this.data.reference_index + ']"]').rules('add', {
            required: true,
        });
        $(_this.el.userReferenceInfoForm).find('[name="last_name[' + _this.data.reference_index + ']"]').rules('add', {
            required: true,
        });
        $(_this.el.userReferenceInfoForm).find('[name="relationship[' + _this.data.reference_index + ']"]').rules('add', {
            required: true,
        });
        $(_this.el.userReferenceInfoForm).find('[name="address[' + _this.data.reference_index + ']"]').rules('add', {
            required: true,
        });
        $(_this.el.userReferenceInfoForm).find('[name="telephone[' + _this.data.reference_index + ']"]').rules('add', {
            minlength: _this.data.phone_length,
            maxlength: _this.data.phone_length,
        });
        $(_this.el.userReferenceInfoForm).find('[name="cellphone[' + _this.data.reference_index + ']"]').rules('add', {
            required: true,
            minlength: _this.data.phone_length,
            maxlength: _this.data.phone_length,
        });
        $(_this.el.userReferenceInfoForm).find('[name="relationship[' + _this.data.reference_index + ']"]').select2();
        _this.data.reference_index++;
    },

    getUserReferenceInfo(user_id) {
        var _this = this;
        $.ajax({
            dataType: 'json',
            method: 'get',
            url: adminAjaxURL + 'users/' + user_id + '/references',
            success: function (data) {
                let readonly = '';

                let referred_by = '';

                if (data['referred_by'] == null) {
                    data['referred_by'] = '';
                }

                if (has_admin != 1 && data['referred_by'] != '') {
                    readonly = 'readonly';
                }

                referred_by += '<label>Referred By</label>' +
                    '<input ' + readonly + ' type="text" name="referred_by" value="' + data['referred_by'] + '" class="form-control">';
                referred_by += '<span class="error" for="referred_by" id="referred_by_error"></span>';

                $('#js--users-referred_by').html(referred_by);

                let str = '';
                $('.tableReferenceInfo').html(str);
                var loop = 3;
                if (data['references'].length > 3) {
                    loop = data['references'].length;
                }

                if (data['references'].length > 3) {
                    loop = data['references'].length;
                }

                for (var i = 0; i < loop; i++) {
                    var reference = data['references'][i];
                    var deleteButton = '';

                    if (reference == undefined) {
                        reference = {
                            first_name: '',
                            last_name: '',
                            relationship: '',
                            telephone: '',
                            cellphone: '',
                            address: '',
                        };
                    }

                    if (i >= 3) {
                        deleteButton = '<button type="button" class="btn btn-danger deleteTableRow"><i class="fa fa-trash"></i></button>';
                    }

                    str = '<tr>' +
                        '     <td>' +
                        '         <input class="form-control" type="text" name="first_name[' + _this.data.reference_index + ']" value="' + reference['first_name'] + '" placeholder="First Name">' +
                        '         <span class="error" for="first_name[' + _this.data.reference_index + ']"></span>' +
                        '     </td>' +
                        '     <td>' +
                        '         <input class="form-control" type="text" name="last_name[' + _this.data.reference_index + ']" value="' + reference['last_name'] + '" placeholder="Last Name">' +
                        '         <span class="error" for="last_name[' + _this.data.reference_index + ']"></span>' +
                        '     </td>' +
                        '     <td>';
                    str += '<select class="form-control" name="relationship[' + _this.data.reference_index + ']">';
                    str += '<option value="">Select Relationship</option>';
                    _this.data.relationShips = data['relationships'];
                    for (var index in _this.data.relationShips) {
                        var relationship = _this.data.relationShips[index];
                        if (index == reference['relationship']) {
                            str += '<option selected value="' + index + '">' + relationship + '</option>';
                        } else {
                            str += '<option value="' + index + '">' + relationship + '</option>';
                        }
                    }
                    str += '</select>' +
                        '         <span class="error" for="relationship[' + _this.data.reference_index + ']"></span>';
                    str += '     </td>' +
                        '     <td>' +
                        '         <div class="input-group">' +
                        '            <span class="input-group-addon country_code_label">' + _this.data.country_code + '</span>' +
                        '            <input class="form-control" type="number" name="telephone[' + _this.data.reference_index + ']" value="' + reference['telephone'] + '" placeholder="Telephone">' +
                        '     </div><span class="error" for="telephone[' + _this.data.reference_index + ']"></span></td>' +
                        '     <td>' +
                        '         <div class="input-group">' +
                        '            <span class="input-group-addon country_code_label">' + _this.data.country_code + '</span>' +
                        '         <input class="form-control" type="number" name="cellphone[' + _this.data.reference_index + ']" value="' + reference['cellphone'] + '" placeholder="Cellphone">' +
                        '     </div><span class="error" for="cellphone[' + _this.data.reference_index + ']"></span></td>' +
                        '     <td>' +
                        '         <textarea class="form-control" type="text" name="address[' + _this.data.reference_index + ']" value="" placeholder="Address" required>' + reference['address'] + '</textarea>' +
                        '            <span class="error" for="address[' + _this.data.reference_index + ']"></span>' +
                        '     </td>' +
                        '     <td>' + deleteButton +
                        '     </td>' +
                        '  </tr>';
                    $('.tableReferenceInfo').append(str);
                    $(_this.el.userReferenceInfoForm).find('[name="first_name[' + _this.data.reference_index + ']"]').rules('add', {
                        required: true,
                    });
                    $(_this.el.userReferenceInfoForm).find('[name="last_name[' + _this.data.reference_index + ']"]').rules('add', {
                        required: true,
                    });
                    $(_this.el.userReferenceInfoForm).find('[name="relationship[' + _this.data.reference_index + ']"]').rules('add', {
                        required: true,
                    });
                    $(_this.el.userReferenceInfoForm).find('[name="address[' + _this.data.reference_index + ']"]').rules('add', {
                        required: true,
                    });
                    $(_this.el.userReferenceInfoForm).find('[name="telephone[' + _this.data.reference_index + ']"]').rules('add', {
                        minlength: _this.data.phone_length,
                        maxlength: _this.data.phone_length,
                    });
                    $(_this.el.userReferenceInfoForm).find('[name="cellphone[' + _this.data.reference_index + ']"]').rules('add', {
                        required: true,
                        minlength: _this.data.phone_length,
                        maxlength: _this.data.phone_length,
                    });
                    $(_this.el.userReferenceInfoForm).find('[name="relationship[' + _this.data.reference_index + ']"]').select2();
                    _this.data.reference_index++;
                }
            }
        })
    }
};
