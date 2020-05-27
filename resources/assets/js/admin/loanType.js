var loanType = {
    el: {
        form: '#loan_type_form',
        addButton: ".addType",
        editButton: ".editType",
        deleteButton: ".deleteType",
        modal: '#loanTypeModal',
        deleteLoanTypeModal: '#deleteLoanTypeModal',
        confirmDeleteLoanTypeButton: '.confirmDeleteLoanTypeButton',
    },
    data: {
        datatable: '',
    },
    init() {
        var _this = this;
        _this.validationForm(adminAjaxURL + 'loan-types', 'post');
        $('textarea.cms_textarea').trumbowyg({
            svgPath: '../resources/css/admin/icons.svg',
        });
        $('#origination_type').select2();
        $('#renewal_type').select2();
        $('#debt_collection_type').select2();
        $('#debt_collection_tax_type').select2();
        $('#debt_type').select2();
        $('#debt_tax_type').select2();
        $('#country_id').select2();
        _this.bindUiActions();
    },
    bindUiActions() {
        var _this = this;
        _this.data.datatable = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": adminAjaxURL + 'datatable-loan-types',
            },
            columns: [
                {data: 'id', name: 'id', visible: false},
                {data: 'country', name: 'countries.name'},
                {data: 'title', name: 'title'},
                {data: 'title_es', name: 'title_es'},
                {data: 'title_nl', name: 'title_nl'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            order: [[0, 'desc']],
            drawCallback(settings) {
                initTooltip();
            },
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
            $(_this.el.deleteLoanTypeModal).find(_this.el.confirmDeleteLoanTypeButton).data('id', $(this).data('id'));
            $(_this.el.deleteLoanTypeModal).modal('show');
        });

        $(document).on('click', _this.el.deleteLoanTypeModal + ' ' + _this.el.confirmDeleteLoanTypeButton, function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'delete',
                url: adminAjaxURL + 'loan-types/' + $(this).data('id'),
                success: function (data) {
                    _this.data.datatable.draw();
                    $(_this.el.deleteLoanTypeModal).modal('hide');
                }
            })
        });

        $(document).on('change', '.all_user_status', function (e) {
            if ($('.all_user_status:checked').val() == 0) {
                $('.user_status').prop('checked', true);
            } else {
                $('.user_status').prop('checked', false);
            }
        });

        $(document).on('change', '.user_status', function (e) {
            if ($(this).prop('checked') == false) {
                $('.all_user_status').prop('checked', false);
            }
        });

        $(document).on('click', '.showAgreementEditor', function (e) {
            e.preventDefault();
            $(this).text('Hide');
            $(this).removeClass('showAgreementEditor');
            $(this).addClass('hideAgreementEditor');
            $('.agreementDiv').show();
        });
        $(document).on('click', '.hideAgreementEditor', function (e) {
            e.preventDefault();
            $(this).text('Show');
            $(this).removeClass('hideAgreementEditor');
            $(this).addClass('showAgreementEditor');
            $('.agreementDiv').hide();
        });

    },
    formReset() {
        var _this = this;
        $('textarea.cms_textarea').trumbowyg('empty');
        $(_this.el.form).find('[name="id"]').val('');
        $(_this.el.form).find('input').prop('disabled', false);
        $(_this.el.form).find('select').prop('disabled', false);
        $(_this.el.form).find('button[type="submit"]').show();
        $(_this.el.form).find('span.error').html('');
        $('#origination_type').val('').select2();
        $('#renewal_type').val('').select2();
        $('#debt_collection_type').val('').select2();
        $('#debt_collection_tax_type').val('').select2();
        $('#debt_type').val('').select2();
        $('#debt_tax_type').val('').select2();
        $('#country_id').val('').select2();
        $(_this.el.form)[0].reset();
    },
    setEdit(id, type) {
        var _this = this
        $.ajax({
            dataType: 'json',
            method: 'get',
            url: adminAjaxURL + 'loan-types/' + id + '/edit',
            success(data) {
                $('[name="pagare"]').trumbowyg('empty');
                setForm(_this.el.form, data['inputs']);
                if (data['statuses'] != undefined) {
                    for (let i in data['statuses']) {
                        let status = data['statuses'][i];
                        if (status == 0) {
                            $('.all_user_status').prop('checked', true);
                            $('.user_status').prop('checked', true);
                        } else {
                            $(`.user_status[value="${status}"]`).prop('checked', true);
                        }
                    }
                }
                if (type == 'view') {
                    $(_this.el.form).find('input').prop('disabled', true);
                    $(_this.el.form).find('select').prop('disabled', true);
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
                title: {required: true},
                title_es: {required: true},
                title_nl: {required: true},
                country_id: {required: true},
                minimum_loan: {required: true},
                maximum_loan: {required: true},
                unit: {required: true},
                loan_component: {required: true},
                apr: {required: true},
                origination_type: {required: true},
                origination_amount: {required: true},
                number_of_days: {required: true},
                interest: {required: true},
                cap_period: {required: true},
                renewal_type: {required: true},
                renewal_amount: {required: true},
                debt_type: {required: true},
                debt_amount: {required: true},
                debt_collection_type: {required: true},
                debt_collection_percentage: {required: true},
                debt_collection_tax_type: {required: true},
                debt_collection_tax_value: {required: true},
                debt_tax_type: {required: true},
                debt_tax_amount: {required: true},
                user_status: {required: true},
                status: {required: true},
            },

            errorPlacement(error, element) {
                $('span[for="' + $(element).attr('name') + '"]').html(error);
                $(element).removeClass('error');
                $('[type="radio"].error').removeClass('error');
            },

            submitHandler(form) {
                if (parseInt($('[name="number_of_days"]').val()) > parseInt($('[name="cap_period"]').val())) {
                    $('span[for="cap_period"]').html('Cap period should be greater than period.');
                } else {
                    $.ajax({
                        dataType: 'json',
                        method: method,
                        url: url,
                        data: $(form).serialize(),
                        success: function (data) {
                            _this.data.datatable.draw();
                            $(_this.el.modal).modal('hide');
                        },
                        error: function (jqXHR, exception) {
                            var Response = jqXHR.responseText;
                            ErrorBlock = $(form);
                            Response = $.parseJSON(Response);
                            displayErrorMessages(Response, ErrorBlock, 'input');
                        }
                    });
                }
            }
        });
    }
};