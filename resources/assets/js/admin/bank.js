var bank = {
    el: {
        form: '#bank_form',
        addButton: ".addBank",
        editButton: ".editBank",
        deleteButton: ".deleteBank",
        modal: '#bankModal',
        deleteBankModal: '#deleteBankModal',
        confirmDeleteBankButton: '.confirmDeleteBankButton',
    },
    data: {
        datatable: '',
    },
    init() {
        var _this = this;
        _this.validationForm(adminAjaxURL + 'banks', 'post');
        $('#country_id').select2();
        _this.bindUiActions();
    },
    bindUiActions() {
        var _this = this;
        _this.data.datatable = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": adminAjaxURL + 'datatable-banks',
            },
            columns: [
                {data: 'id', name: 'banks.id', visible: false},
                {data: 'name', name: 'banks.name'},
                {data: 'contact_person', name: 'banks.contact_person'},
                {data: 'email', name: 'banks.email'},
                {data: 'phone', name: 'banks.phone'},
                {data: 'country_name', name: 'countries.name'},
                {data: 'transaction_fee', name: 'banks.transaction_fee'},
                {data: 'tax_transaction', name: 'banks.tax_transaction'},
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
            $(_this.el.deleteBankModal).find(_this.el.confirmDeleteBankButton).data('id', $(this).data('id'));
            $(_this.el.deleteBankModal).modal('show');
        });

        $(document).on('click', _this.el.deleteBankModal + ' ' + _this.el.confirmDeleteBankButton, function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'delete',
                url: adminAjaxURL + 'banks/' + $(this).data('id'),
                success: function (data) {
                    _this.data.datatable.draw();
                    $(_this.el.deleteBankModal).modal('hide');
                }
            })
        });

        $(document).on('change', '#country_id', function (e) {
            e.preventDefault();
            _this.countryChange($(this).val());
        });

        $(document).on('change blur keyup', '[name="transaction_fee"]', function (e) {
            e.preventDefault();
            _this.calculate();
        });

    },
    countryChange(country) {
        var _this = this;
        if (country != '') {
            $.ajax({
                dataType: 'json',
                method: "get",
                url: adminAjaxURL + 'countries/' + country + '/edit',
                success: function (data) {
                    $('[name="country_tax_percentage"]').val(data['inputs']['tax_percentage']['value']);
                    _this.calculate();
                }
            });
        } else {
            $('[name="country_tax_percentage"]').val(0);
            _this.calculate();
        }
    },
    calculate() {
        var value = $('[name="transaction_fee"]').val();
        var percentage = $('[name="country_tax_percentage"]').val();
        var tax_percentage = value * percentage / 100;
        $('[name="tax_transaction"]').val(tax_percentage.toFixed(2));
    },
    formReset() {
        var _this = this;
        $(_this.el.form).find('[name="id"]').val('');
        $(_this.el.form).find('input').prop('disabled', false);
        $(_this.el.form).find('select').prop('disabled', false);
        $(_this.el.form).find('button[type="submit"]').show();
        $('#country_id').val('').select2();
        $(_this.el.form)[0].reset();
    },
    setEdit(id, type) {
        var _this = this
        $.ajax({
            dataType: 'json',
            method: 'get',
            url: adminAjaxURL + 'banks/' + id + '/edit',
            success(data) {
                setForm(_this.el.form, data['inputs']);
                _this.countryChange(data['inputs']['country_id']['value']);
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
                "name": {required: true},
                "country_id": {required: true},
                'contact_person': {required: true},
                'email': {required: true},
                'phone': {required: true},
                'transaction_fee_type': {required: true},
                'transaction_fee': {required: true},
                'tax_transaction': {required: true},
            },

            errorPlacement(error, element) {
                $('span[for="' + $(element).attr('name') + '"]').html(error);
                $(element).removeClass('error');
            },

            submitHandler(form) {
                $.ajax({
                    dataType: 'json',
                    method: method,
                    url: url,
                    data: $(form).serialize(),
                    success: function (data) {
                        _this.data.datatable.draw();
                        $(_this.el.modal).modal('hide');
                    },
                    error: function (jqXHR) {
                        var Response = jqXHR.responseText;
                        ErrorBlock = $(form);
                        Response = $.parseJSON(Response);
                        displayErrorMessages(Response, ErrorBlock, 'input');
                    }
                })
            }
        });
    }
};