var nlb = {
    el: {
        form: '#transaction_form',
        addButton: ".addNlb",
        editButton: ".editNlb",
        deleteButton: ".deleteNlb",
        modal: '#nlbsModal',
        deleteNlbTransactionModal: '#deleteNlbTransactionModal',
        confirmDeleteNlbTransactionButton: '.confirmDeleteNlbTransactionButton',
    },
    data: {
        datatable: '',
        admin: '0',
    },
    init() {
        var _this = this;
        _this.data.admin = window.admin;
        _this.validationForm(adminAjaxURL + 'nlbs', 'post');
        $('.select2single').select2();
        _this.bindUiActions();
        datePickerInit();
        $('#country_selection').select2();
        $('#branch_selection').select2();
    },
    bindUiActions() {
        var _this = this;
        _this.data.datatable = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": adminAjaxURL + 'datatable-nlbs',
                "data": function (d) {
                    d.branch_id = $('#branch_id').val();
                }
            },
            columns: [
                {data: 'id', name: 'nlbs.id', visible: false},
                {data: 'date', name: 'nlbs.date', searchable: false},
                {data: 'branch', name: 'branches.title'},
                {data: 'user_name', name: 'users.firstname'},
                {data: 'user_name', name: 'users.lastname', visible: false},
                {data: 'type', name: 'nlbs.type'},
                {data: 'reason_name', name: 'n_l_b_reasons.title'},
                {data: 'amount', orderable: false, searchable: false},
                {data: 'desc', name: 'nlb.desc'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            order: [[0, 'desc']],
            drawCallback(settings) {
                initTooltip();
            },
        });
        $(document).on('change', '#branch_id', function (e) {
            e.preventDefault();
            _this.data.datatable.draw();
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
            $(_this.el.deleteNlbTransactionModal).find(_this.el.confirmDeleteNlbTransactionButton).data('id', $(this).data('id'));
            $(_this.el.deleteNlbTransactionModal).modal('show');
        });

        $(document).on('click', _this.el.deleteNlbTransactionModal + ' ' + _this.el.confirmDeleteNlbTransactionButton, function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'delete',
                url: adminAjaxURL + 'nlbs/' + $(this).data('id'),
                success: function (data) {
                    _this.data.datatable.draw();
                    $(_this.el.deleteNlbTransactionModal).modal('hide');
                }
            })
        });


        $(document).on('change blur keyup', '.amount_change', function (e) {
            e.preventDefault();
            _this.totalCalculate();
        });


        $(document).on('change', '#type', function (e) {
            e.preventDefault();
            _this.getReasons($(this).val());
        });

        $(document).on('change', '#country_selection', function (e) {
            e.preventDefault();
            _this.countryToggle($(this).val())
        });

    },
    countryToggle(country, value, type) {
        if (country != '') {
            $.ajax({
                dataType: 'json',
                method: 'get',
                url: adminAjaxURL + 'countries/' + country + '/branch',
                success: function (data) {
                    var str = '<option>Select Branch</option>';
                    for (var index in data['branches']) {
                        if (value != undefined && value == index) {
                            str += '<option value="' + index + '" selected>' + data['branches'][index] + '</option>';
                        } else {
                            str += '<option value="' + index + '">' + data['branches'][index] + '</option>';
                        }
                    }
                    $('#branch_selection').html(str);
                    if (type == 'view') {
                        $('#transaction_form').find('input').prop('disabled', true);
                        $('#transaction_form').find('select').prop('disabled', true);
                    }
                }
            });
        } else {
            var str = '<option>Select Branch</option>';
            $('#branch_selection').html(str);
        }
    },
    formReset() {
        var _this = this;
        $('.select2single').val('').select2();
        $(_this.el.form).find('[name="date"]').prop('disabled', false);
        $(_this.el.form).find('[name="id"]').val('');
        $(_this.el.form).find('input').prop('disabled', false);
        $(_this.el.form).find('span.error').html('');
        $(_this.el.form).find('select').prop('disabled', false);
        $(_this.el.form).find('textarea').prop('disabled', false);
        $(_this.el.form).find('button[type="submit"]').show();
        $(_this.el.form)[0].reset();
    },
    setEdit(id, type) {
        var _this = this
        $.ajax({
            dataType: 'json',
            method: 'get',
            url: adminAjaxURL + 'nlbs/' + id + '/edit',
            success(data) {
                setForm(_this.el.form, data['inputs']);
                _this.getReasons(data.inputs.type.value, data.reason);
                for (var index in data['amounts']) {
                    $('[name="amount[' + index + ']"]').val(data['amounts'][index]);
                }
                _this.totalCalculate();
                if (_this.data.admin != 1) {
                    $(_this.el.form).find('[name="date"]').prop('disabled', true);
                }
                if (data['inputs']['country_id'] != undefined) {
                    _this.countryToggle(data['inputs']['country_id']['value'], data['inputs']['branch_id']['value']);
                }
                $(_this.el.modal).modal('show');
                if (type == 'view') {
                    $(_this.el.form).find('input').prop('disabled', true);
                    $(_this.el.form).find('select').prop('disabled', true);
                    $(_this.el.form).find('textarea').prop('disabled', true);
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
                'type': {required: true},
                'reason': {required: true},
                'date': {required: true},
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
                    error: function (jqXHR, exception) {
                        var Response = jqXHR.responseText;
                        ErrorBlock = $(form);
                        Response = $.parseJSON(Response);
                        displayErrorMessages(Response, ErrorBlock, 'input');
                        fullLoader.off();
                    }
                })
            }
        });
    },
    totalCalculate() {
        var sum = 0;
        $('.amount_change').each(function (key, item) {
            if ($(item).val() != '') {
                sum += parseFloat($(item).val());
            }
        });
        $('#total_amount').val(sum);
    },
    getReasons(type, value) {
        if (type != '') {
            $.ajax({
                dataType: 'json',
                method: 'get',
                url: adminAjaxURL + 'nlb-reasons/' + type + '/types',
                success: function (data) {
                    var str = '';
                    for (var index in data['reasons']) {
                        if (value != undefined && value == index) {
                            str += '<option selected value="' + index + '">' + data['reasons'][index] + '</option>';
                        } else {
                            str += '<option value="' + index + '">' + data['reasons'][index] + '</option>';
                        }
                    }
                    $('#reason_selection').html(str);
                }
            });
        } else {
            var str = '';
            $('#reason_selection').html(str);
        }
    }
};