var dayopen = {
    el: {
        form: '#dayopenForm',
        addButton: ".addNewDayOpenButton",
        editButton: ".editDayOpenButton",
        modal: '#dayOpenModal',
        viewDayopen: '.viewDayopen',
    },
    data: {
        datatable: '',
        type: '',
        branch_name: "",
        main_title: '',
    },
    init() {
        var _this = this;
        _this.data.type = window.type;
        _this.data.branch_name = window.branch_name;
        if (_this.data.type == 1) {
            _this.data.main_title = 'Day Open';
        } else if (_this.data.type == 2) {
            _this.data.main_title = 'Bank transfers';
        } else if (_this.data.type == 3) {
            _this.data.main_title = 'Vault';
        }

        if (_this.data.branch_name != '') {
            _this.data.main_title += ' - ' + _this.data.branch_name;
        }

        _this.validationForm(adminAjaxURL + 'daily-turnover/day-open', 'post');
        $('#country_selection').select2();
        $('#branch_selection').select2();
        _this.bindUiActions();
    },
    bindUiActions() {
        var _this = this;
        _this.data.datatable = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": adminAjaxURL + 'daily-turnover/datatable-day-open',
                data: function (d) {
                    d.branch_id = $('#branch_id').val();
                    d.type = _this.data.type;
                }
            },
            columns: [
                {data: 'date', name: 'dayopens.date', visible: false},
                {data: 'username', name: 'users.firstname', searchable: false},
                {data: 'username', name: 'users.lastname', searchable: false, visible: false},
                {data: 'branch_name', name: 'branches.title', searchable: false},
                {data: 'date', name: 'dayopens.date', searchable: false},
                {data: 'total_amount', name: 'total_amount', searchable: false},
                {data: 'custom_created_at', name: 'dayopens.custom_created_at', searchable: false},
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
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'get',
                url: adminAjaxURL + 'daily-turnover/day-open/create',
                success: function (data) {
                    $('.old-date-picker').datepicker({
                        orientation: "bottom auto",
                        clearBtn: true,
                        autoclose: true,
                        format: dateFormat
                    });
                    if (data['startDate'] != undefined) {
                        var startDate = new Date(data['startDate']);
                        startDate.setDate(startDate.getDate() + 1);
                        $('.old-date-picker').datepicker('setStartDate', startDate);
                    }
                    var endDate = new Date();
                    $('.old-date-picker').datepicker('setEndDate', endDate);
                    $(_this.el.modal).find('.jq--title').html(_this.data.main_title);
                    $(_this.el.modal).modal('show');
                }
            });
        });

        $(document).on('change', '#branch_id', function (e) {
            e.preventDefault();
            _this.data.datatable.draw();
        });

        $(document).on('change', '#country_selection', function (e) {
            e.preventDefault();
            _this.countryToggle($(this).val())
        });

        $(document).on('click', _this.el.editButton, function (e) {
            e.preventDefault();
            _this.formReset();
            var branch = $(this).data('branch');
            $.ajax({
                dataType: 'json',
                method: 'get',
                url: adminAjaxURL + 'daily-turnover/day-open/' + $(this).data('date') + '/' + $(this).data('user') + '/' + branch,
                success: function (data) {
                    var startDate = new Date();
                    $('.old-date-picker').datepicker({
                        orientation: "bottom auto",
                        clearBtn: true,
                        autoclose: true,
                        format: dateFormat
                    });
                    $('.old-date-picker').datepicker('setEndDate', startDate);
                    var date = new Date(data['dayopens'][0]['date']);
                    $('#dayopenForm').find('[name="date"]').datepicker('setDate', date);
                    $('#dayopenForm').find('[name="date"]').val(moment(date).format('DD/MM/YYYY'));
                    $('#dayopenForm').find('[name="old_date"]').val(moment(date).format('DD/MM/YYYY'));


                    $('#dayopenForm').find('[name="country_id"]').val(data['country']).select2();
                    _this.countryToggle(data['country'], branch)

                    for (var index in data['dayopens']) {
                        var dayopen = data['dayopens'][index];
                        $('#dayopenForm').find('[name="amount[' + dayopen['payment_type'] + ']"]').val(dayopen['amount']);
                    }
                    _this.total_calculate();

                    $('#dayOpenModal').find('[name="branch"]').val(data['dayopens'][0]['branch_id']);
                    $('#dayOpenModal').find('[name="user"]').val(data['dayopens'][0]['user_id']);
                    $('#dayOpenModal').find('.jq--title').html('Day Open - ' + data['branch']);
                    $('#dayOpenModal').modal('show');
                }
            })
        });

        $(document).on('click', '.viewDayopen', function (e) {
            e.preventDefault();
            _this.formReset();
            var branch = $(this).data('branch');
            $.ajax({
                dataType: 'json',
                url: adminAjaxURL + 'daily-turnover/day-open/' + $(this).data('date') + '/' + $(this).data('user') + '/' + branch,
                method: 'get',
                success: function (data) {
                    var startDate = new Date();
                    $('.old-date-picker').datepicker({
                        orientation: "bottom auto",
                        clearBtn: true,
                        autoclose: true,
                        format: dateFormat
                    });
                    $('.old-date-picker').datepicker('setEndDate', startDate);
                    var date = new Date(data['dayopens'][0]['date']);
                    $('#dayopenForm').find('[name="date"]').datepicker('setDate', date);
                    $('#dayopenForm').find('[name="date"]').val(moment(date).format('DD/MM/YYYY'));
                    $('#dayopenForm').find('[name="old_date"]').val(moment(date).format('DD/MM/YYYY'));

                    $('#dayopenForm').find('[name="country_id"]').val(data['country']).select2();
                    _this.countryToggle(data['country'], branch, 'view')

                    for (var index in data['dayopens']) {
                        var dayopen = data['dayopens'][index];
                        $('#dayopenForm').find('[name="amount[' + dayopen['payment_type'] + ']"]').val(dayopen['amount']);
                    }
                    _this.total_calculate();
                    $('#dayopenForm').find('button[type="submit"]').hide();
                    $('#dayOpenModal').find('.jq--title').html('Day Open - ' + data['branch']);
                    $('#dayOpenModal').modal('show');
                }
            });
        });


        $(document).on('change blur keyup', '#dayopenForm input', function () {
            _this.total_calculate();
        });

    },
    formReset() {
        var _this = this;
        $(_this.el.form).find('[name="id"]').val('');
        $(_this.el.form).find('[name="branch"]').val('');
        $(_this.el.form).find('[name="old_date"]').val('');
        $(_this.el.form).find('[name="user"]').val('');
        $(_this.el.form).find('[name="type  "]').val('');
        $(_this.el.form).find('input').prop('disabled', false);
        $(_this.el.form).find('select').prop('disabled', false);
        $(_this.el.form).find('button[type="submit"]').show();
        $(_this.el.form).find('.error').html('');
        $('#country_selection').val('').select2();
        $('#branch_selection').val('').select2();
        $(_this.el.form)[0].reset();
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
                        if (!data['status']) {
                            $(_this.el.modal).find('.date_error').html(data['message']);
                        } else {
                            $(_this.el.modal).modal('hide');
                            _this.data.datatable.draw(true);
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
    total_calculate() {
        var sum = 0;
        $('#dayopenForm input[name^="amount"]').each(function (key, item) {
            if ($(item).val() != '') {
                sum = sum + parseFloat($(item).val());
            }
        });
        $('#dayopenForm input[name="total"]').val(sum.toFixed(2));
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
                        $('#dayopenForm').find('input').prop('disabled', true);
                        $('#dayopenForm').find('select').prop('disabled', true);
                    }
                }
            });
        } else {
            var str = '<option>Select Branch</option>';
            $('#branch_selection').html(str);
        }
    },

};