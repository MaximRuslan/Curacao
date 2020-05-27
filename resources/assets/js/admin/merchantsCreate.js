var merchantsCreate = {
    el: {
        userInfoform: '#merchantInfoForm',
    },

    data: {
        email_index: 0,
        telephone_index: 0,
        branch_index: 0,
        commission_index: 0,

        country_code: '',
        phone_length: '',

        primary: true,
        telephone_primary: true,
        first_email: true,
        first_telephone: true,
    },

    init() {
        var _this = this;
        _this.bindUiActions();
        $('.select2Single').select2();
        _this.validateUserInfoForm(adminAjaxURL + 'merchants', 'post');
        _this.showTypeWise($('[name="type"]').val());
        _this.getBranches($('[name="merchant_id"]').val(), branch_id);
        _this.setSecondaryEmails(merchant_emails);
        _this.setBranches(merchant_branches);
        _this.setCommissions(merchant_commissions);
        _this.countryWiseData($('#country_id').val());
    },

    bindUiActions() {
        var _this = this;

        $(document).on('change', '[name="type"]', function (e) {
            e.preventDefault();
            _this.showTypeWise($(this).val());
        });

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
        $(document).on('click', '.deleteTableRowTelephone', function (e) {
            e.preventDefault();
            $(this).parent().parent().remove();
        });
        $(document).on('click', '.deleteTableRowBranch', function (e) {
            e.preventDefault();
            $(this).parent().parent().remove();
        });
        $(document).on('click', '.deleteTableRowCommission', function (e) {
            e.preventDefault();
            $(this).parent().parent().remove();
        });

        $(document).on('click', '.addNewBranch', function (e) {
            e.preventDefault();
            let str = _this.branchHtml();
            $('#js--branch-tbody').append(str);
            $('[name="branches[' + _this.data.branch_index + ']"]').rules("add", {
                required: true
            });
            _this.data.branch_index++;
            initTooltip();
        });

        $(document).on('click', '.addNewCommission', function (e) {
            e.preventDefault();
            let str = _this.commissionHtml();
            $('#js--commission-tbody').append(str);
            $('[name="min_amount[' + _this.data.commission_index + ']"]').rules("add", {
                required: true
            });
            $('[name="max_amount[' + _this.data.commission_index + ']"]').rules("add", {
                required: true
            });
            $('[name="commission[' + _this.data.commission_index + ']"]').rules("add", {
                required: true
            });
            _this.data.commission_index++;
            initTooltip();
        });

        $(document).on('change blur', '[name^="min_amount"]', function (e) {
            e.preventDefault();
            let value = $(this).val();
            if (value == '') {
                value = 0;
            }
            let index = $(this).attr('name').replace('min_amount[', '').replace(']', '');
            $(`[name="max_amount[${index}]"]`).rules('add', {
                min: parseFloat(value)
            });
        });
        $(document).on('change blur', '[name^="max_amount"]', function (e) {
            e.preventDefault();
            let value = $(this).val();
            if (value == '') {
                value = 0;
            }
            let index = $(this).attr('name').replace('max_amount[', '').replace(']', '');
            $(`[name="min_amount[${index}]"]`).rules('add', {
                max: parseFloat(value)
            });
        });

        $(document).on('click', '.addNewTelephone', function (e) {
            e.preventDefault();
            if ($('#country_id').val() != '') {
                var checked = '';
                var display = '';
                if (_this.data.first_telephone && _this.data.telephone_primary == true) {
                    checked = 'checked';
                    display = 'style="display:none;"';
                }
                var primary = '<input type="radio" name="telephone_primary" value="' + _this.data.telephone_index + '" ' + checked + '>';
                str = '<tr>' +
                    '    <td>' +
                    '         <div class="input-group">' +
                    '            <span class="input-group-addon country_code_label phoneCode">' + _this.data.country_code + '</span>' +
                    '            <input type="number" name="telephone[' + _this.data.telephone_index + ']" class="form-control phoneCodeLimit">' +
                    '         </div>' +
                    '         <span class="error" for="telephone[' + _this.data.telephone_index + ']"></span>' +
                    '    </td>' +
                    '    <td>' + primary +
                    '    </td>' +
                    '    <td>' +
                    '        <input type="hidden" value="" name="telephone_id[' + _this.data.telephone_index + ']">' +
                    '        <button  type="button" ' + display + ' class="delete btn btn-danger deleteTableRowTelephone" data-id="' + _this.data.telephone_index + '">' +
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
                _this.data.first_telephone = false;
                _this.data.telephone_index++;
            } else {
                alert('Please select country first.');
            }
        });

        $(document).on('click', '.resendVerificationMail', function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'get',
                url: adminAjaxURL + 'merchants/' + $(this).data('user-id') + '/resend/' + $(this).data('id'),
                success: function (data) {
                    swal('Resend verification mail is sent to your email id.');
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
                url: adminAjaxURL + 'merchants/emails/' + $(this).data('info-id') + '/store',
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

        $(document).on('change', '[name="telephone_primary"]', function (e) {
            $('.deleteTableRowTelephone').show();
            $('.deleteTableRowTelephone[data-id=' + $('[name="telephone_primary"]:checked').val() + ']').hide();
        });
        $(document).on('change', '#js--merchant-id', function (e) {
            _this.getBranches($(this).val());
        })
    },

    countryWiseData(country) {
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
                    _this.setTelephones(merchant_telephones);
                }
            })
        }
    },

    validateUserInfoForm: function (url, method) {
        var _this = this;
        $(_this.el.userInfoform).data('validator', null);
        $(_this.el.userInfoform).unbind();

        validator = $(_this.el.userInfoform).validate({
            // define validation rules
            rules: {
                type: {required: true},
                first_name: {required: true},
                last_name: {required: true},
                lang: {required: true},
                status: {required: true},
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
                var custom_data = new FormData($(_this.el.userInfoform)[0]);
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
                        if (data['status'] == true) {
                            window.location = adminURL + 'merchants';
                        } else {
                            if (data['type'] == 'email') {
                                $('#email_error').text(data['message']);
                            }
                            if (data['type'] == 'phone') {
                                $('#phone_error').text(data['message']);
                            }
                            if (data['type'] == 'branch') {
                                $('#branch_error').text(data['message']);
                            }
                            if (data['type'] == 'commission') {
                                $('#commission_error').text(data['message']);
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

    showTypeWise(type) {
        $('.js--type').hide();
        $('.js--type-' + type).show();

        //remove
        $('[name="name"]').rules('remove', 'required');
        if ($('[name="primary"]').length > 0) {
            $('[name="primary"]').rules('remove', 'required');
        }
        $('[name="tax_id"]').rules('remove', 'required');
        if ($('[name="country_id"]').length > 0) {
            $('[name="country_id"]').rules('remove', 'required');
        }
        $('[name^="secondary_email"]').each(function (key, item) {
            $(item).rules('remove', 'required');
        });
        $('[name^="branches"]').each(function (key, item) {
            $(item).rules('remove', 'required');
        });
        $('[name^="telephone"]').each(function (key, item) {
            $(item).rules('remove', 'required');
        });
        $('[name^="min_amount"]').each(function (key, item) {
            $(item).rules('remove', 'required');
        });
        $('[name^="min_amount"]').each(function (key, item) {
            $(item).rules('remove', 'required');
        });
        $('[name^="commission"]').each(function (key, item) {
            $(item).rules('remove', 'required');
        });
        if ($('[name="telephone_primary"]').length > 0) {
            $('[name="telephone_primary"]').rules('remove', 'required');
        }

        $('[name="merchant_id"]').rules('remove', 'required');
        $('[name="email"]').rules('remove', 'required');
        $('[name="branch_id"]').rules('remove', 'required');

        //add
        if (type == 1) {
            $('[name="name"]').rules('add', {required: true});
            if ($('[name="country_id"]').length > 0) {
                $('[name="country_id"]').rules('add', {required: true});
            }
            if ($('[name="primary"]').length > 0) {
                $('[name="primary"]').rules('add', {required: true});
            }
            $('[name="tax_id"]').rules('add', {required: true});
            $('[name^="secondary_email"]').each(function (key, item) {
                $(item).rules('add', {required: true});
            });
            $('[name^="branches"]').each(function (key, item) {
                $(item).rules('add', {required: true});
            });
            $('[name^="telephone"]').each(function (key, item) {
                $(item).rules('add', {required: true});
            });
            $('[name^="min_amount"]').each(function (key, item) {
                $(item).rules('add', {required: true});
            });
            $('[name^="min_amount"]').each(function (key, item) {
                $(item).rules('add', {required: true});
            });
            $('[name^="commission"]').each(function (key, item) {
                $(item).rules('add', {required: true});
            });
            if ($('[name="telephone_primary"]').length > 0) {
                $('[name="telephone_primary"]').rules('add', {required: true});
            }
        } else if (type == 2) {
            $('[name="merchant_id"]').rules('add', {required: true});
            $('[name="email"]').rules('add', {required: true});
            $('[name="branch_id"]').rules('add', {required: true});
        }
    },

    getBranches(id, branch_id) {
        if (id != '') {
            let str = '<option value="">Select Branch</option>';
            $.ajax({
                dataType: 'json',
                method: 'get',
                url: adminAjaxURL + 'merchants/' + id + "/branches",
                success: function (data) {
                    for (let i in data['branches']) {
                        let selected = '';
                        if (i == branch_id) {
                            selected = 'selected';
                        }
                        str += `'<option value="${i}" ${selected}>${data['branches'][i]}</option>'`
                    }
                    $('[name="branch_id"]').html(str);
                },
                error: function (xhr, settings) {
                    $('[name="branch_id"]').html(str);
                }
            });
        } else {
            $('[name="branch_id"]').html('<option value="">Select Branch</option>');
        }
    },

    setTelephones(data) {
        var _this = this;
        $('#user_telephones').html('');
        for (var index in data) {
            _this.data.telephone_primary = false;
            var checked = '';
            var display = '';
            if (data[index].primary == 1) {
                checked = 'checked';
                display = 'style="display:none;"';
            }
            var primary = '<input type="radio" name="telephone_primary" value="' + _this.data.telephone_index + '" ' + checked + '>';
            str = '<tr>' +
                '    <td>' +
                '         <div class="input-group">' +
                '            <span class="input-group-addon country_code_label phoneCode">' + _this.data.country_code + '</span>' +
                '        <input type="number" name="telephone[' + _this.data.telephone_index + ']" class="form-control phoneCodeLimit" value="' + data[index].value + '">' +
                '       </div>' +
                '         <span class="error" for="telephone[' + _this.data.telephone_index + ']"></span>' +
                '    </td>' +
                '    <td>' + primary + '</td>' +
                '    <td>' +
                '       <input type="hidden" value="' + data[index].id + '" name="telephone_id[' + _this.data.telephone_index + ']">' +
                '        <button type="button" ' + display + ' class="delete btn btn-danger deleteTableRowTelephone" data-id="' + _this.data.telephone_index + '">' +
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

    setSecondaryEmails(data) {
        var _this = this;
        str = '';
        $('#user_emails').html(str);
        for (var index in data) {
            _this.data.primary = false;
            _this.data.first_email = false;
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
                resendVerification = '<a href="#nogo" class="resendVerificationMail" data-user-id="' + data[index]['merchant_id'] + '" data-id="' + data[index]['id'] + '" data-toggle="tooltip" title="Resend verification mail">' +
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

    branchHtml(branch) {
        let _this = this;
        if (branch == undefined) {
            branch = {
                id: '',
                name: ''
            };
        }
        return `
                <tr>
                    <td>
                        <input type="text" name="branches[${_this.data.branch_index}]"  class="form-control" value="${branch['name']}">
                        <span class="error" for="branches[${_this.data.branch_index}]"></span>
                        <input type="hidden" name="branch_id[${_this.data.branch_index}]" value="${branch['id']}">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger deleteTableRowBranch" data-branch-id="${branch['id']}" data-id="${_this.data.branch_index}"  data-toggle="tooltip" title="Delete Branch">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
          `;
    },

    setBranches(data) {
        let _this = this;
        for (let i in data) {
            let branch = data[i];
            let str = _this.branchHtml(branch);
            $('#js--branch-tbody').append(str);
            _this.data.branch_index++;
        }
    },

    commissionHtml(commission) {
        let _this = this;
        if (commission == undefined) {
            commission = {
                id: '',
                min_amount: '',
                max_amount: '',
                commission: '',
            };
        }
        return `
                <tr>
                    <td>
                        <input type="number" step="0.01" min="0" value="${commission.min_amount}" name="min_amount[${_this.data.commission_index}]" class="form-control">
                        <span class="error" for="min_amount[${_this.data.commission_index}]"></span>
                        <span class="error" for="min_amount.${_this.data.commission_index}"></span>
                    </td>
                    <td>
                        <input type="number" step="0.01" min="0" value="${commission.max_amount}" name="max_amount[${_this.data.commission_index}]" class="form-control">
                        <span class="error" for="max_amount[${_this.data.commission_index}]"></span>
                        <span class="error" for="max_amount.${_this.data.commission_index}"></span>
                    </td>
                    <td>
                        <input type="number" step="0.01" min="0" value="${commission.commission}" name="commission[${_this.data.commission_index}]" class="form-control">
                        <span class="error" for="commission[${_this.data.commission_index}]"></span>
                        <span class="error" for="commission.${_this.data.commission_index}"></span>
                    </td>
                    <td>
                        <input type="hidden" name="commission_id[${_this.data.commission_index}]" value="${commission.id}">
                        <button class="btn btn-danger deleteTableRowCommission" data-id="${_this.data.commission_index}" data-toggle="tooltip" title="Delete Commission">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
          `;
    },

    setCommissions(data) {
        let _this = this;
        for (let i in data) {
            let commission = data[i];
            let str = _this.commissionHtml(commission);
            $('#js--commission-tbody').append(str);
            _this.data.commission_index++;
        }
    },
};
