var wallet = {
    el: {
        form: '#walletModelForm',
        addButton: "#addPayment",
        modal: '#walletModel',
    },
    data: {
        datatable: '',
    },
    init() {
        var _this = this;
        _this.validationForm(adminAjaxURL + 'wallets', 'post');
        $('#js--user').select2();
        $('#user_select').select2();
        datePickerInit();
        _this.bindUiActions();
    },
    bindUiActions() {
        var _this = this;
        _this.data.datatable = $('#wallet-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": adminAjaxURL + 'datatable-wallet',
                "data": function (d) {
                    d.collector_id = $('#js--user').val();
                    d.start_date = $('#start_date_below').val();
                    d.end_date = $('#end_date_below').val();
                }
            },
            columns: [
                {data: 'date', name: 'wallets.created_at'},
                {data: 'loan_id', name: 'lch.loan_id'},
                {data: 'client_user', name: 'client_user'},
                {data: 'payment_amount', name: 'lch.payment_amount'},
                {data: 'amount', name: 'lch.commission'},
                {data: 'commission_percent', name: 'lch.commission_percent'},
                {data: 'collector', name: 'collector'},
                {data: 'created_by_user', name: 'created_by_user'},
            ],
            order: [[0, 'desc']],
            drawCallback(settings) {
                initTooltip();
            },
        });

        $(document).on('change', '#start_date_below', function () {
            $('#end_date_below').datepicker('setStartDate', $(this).val());
        });
        $(document).on('change', '#end_date_below', function () {
            $('#start_date_below').datepicker('setEndDate', $(this).val());
        });

        $(document).on('click', _this.el.addButton, function (e) {
            e.preventDefault();
            _this.formReset();
            $(_this.el.modal).modal('show');
        });

        $(document).on('click', '.js--search', function (e) {
            e.preventDefault();
            _this.data.datatable.draw();
        });

        $(document).on('change', '#user_select', function (e) {
            _this.getMinAmount($(this).val())
        });

    },
    formReset() {
        var _this = this;
        $(_this.el.form).find('[name="id"]').val('');
        $(_this.el.form).find('input').prop('disabled', false);
        $(_this.el.form).find('select').prop('disabled', false);
        $(_this.el.form).find('button[type="submit"]').show();
        $('#user_select').val('').select2();
        $(_this.el.form).find('span.error').text('');
        $(_this.el.form)[0].reset();
    },
    validationForm(url, method) {
        var _this = this;
        $(_this.el.form).data('validator', null);
        $(_this.el.form).unbind();

        validator = $(_this.el.form).validate({
            rules: {
                user_id: {required: true},
                amount: {required: true},
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
                    }
                })
            }
        });
    },
    getMinAmount(user_id) {
        const _this = this;
        if (user_id != '') {
            $.ajax({
                dataType: 'json',
                method: 'post',
                url: adminAjaxURL + 'users/' + user_id + '/wallet-total',
                success(data) {
                    if (data.wallet !== undefined) {
                        $(_this.el.form).find('[name="amount"]').rules('add', {
                            max: parseFloat(data['wallet'])
                        })
                    }
                }
            })
        }
    }
};