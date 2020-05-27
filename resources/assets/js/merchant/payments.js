const payments = {
    el: {
        addPayment: '.js--add-payment',
        addPaymentForm: '#transactionForm',
        addPaymentSubmit: '.js--submit-button',
        addModal: '#js--payment-modal',
        userFind: '#js--user-find',
        userId: '#js--user-id',
    },

    data: {
        payments: {
            url: 'datatable-payments',
            columns: [
                {name: 'loan_transactions.id', data: 'DT_Row_Index'},
                {name: 'users.firstname', data: 'user'},
                {name: 'users.lastname', data: 'user', visible: false},
                {name: 'merchant_branches.name', data: 'branch_name', visible: merchant},
                {name: 'loan_transactions.amount', data: 'amount'},
                {name: 'loan_transactions.created_at', data: 'created_at', searchable: false},
                {name: 'merchants.first_name', data: 'merchant'},
                {name: 'merchants.last_name', data: 'merchant', visible: false},
            ],
            "lengthMenu": [50, 100, 250]
        }
    },

    init() {
        let _this = this;
        _this.data.dtable = common.datatable(_this.data.payments);
        _this.bindUIActions();
        common.validationForm({
            form: _this.el.addPaymentForm,
            rules: {
                loan_id: {required: true},
                amount: {required: true},
                branch_id: {required: true}
            },
            method: 'post',
            url: 'payments',
            beforeSubmit: _this.beforeSubmit,
            callback: _this.paymentSuccess,
        });
    },

    bindUIActions() {
        let _this = this;
        $(document).on('click', _this.el.addPayment, function (e) {
            e.preventDefault();
            _this.formReset();
            $(_this.el.addModal).modal('show');
        });

        $(document).on('click', _this.el.userFind, function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'post',
                url: merchant_ajax_url + 'users-loans/' + $(_this.el.userId).val(),
                success: function (data) {
                    if (data['status'] == true) {
                        $(_this.el.addPaymentForm).find('.js--type-user').hide();
                        if (data['country']) {
                            $(_this.el.addPaymentForm).find(_this.el.addPaymentSubmit).prop('disabled', false);
                            $(_this.el.addPaymentForm).find('.js--user-message').text('');
                            $(_this.el.addPaymentForm).find('.js--type-user').show();
                            $(_this.el.addPaymentForm).find('.js--user-name').text(data['name']);
                            $(_this.el.addPaymentForm).find('#js--open-balance').text(data['open_balance']);
                            $(_this.el.addPaymentForm).find('[name="loan_id"]').val(data['loan_id']);
                            $(_this.el.addPaymentForm).find('[name="amount"]').rules('add', {
                                max: parseFloat(data['open_balance'])
                            })
                        } else {
                            $(_this.el.addPaymentForm).find('.js--user-message').text(data['message']);
                            $(_this.el.addPaymentForm).find('.js--user-message').show();
                        }
                    } else {
                        swal(keywords.data_not_found)
                        $(_this.el.addPaymentForm).find('.js--user-message').text('');
                        $(_this.el.addPaymentForm).find('.js--type-user').hide();
                    }
                }
            });
        });
    },
    formReset() {
        let _this = this;
        $(_this.el.addPaymentForm).find(_this.el.addPaymentSubmit).prop('disabled', true);
        $(_this.el.addPaymentForm).find('.js--type-user').hide();
        $(_this.el.addPaymentForm).find('[name="branch_id"]').val('').select2();
        $(_this.el.addPaymentForm)[0].reset();
    },
    paymentSuccess(data) {
        let _this = payments;
        _this.data.dtable.draw();
        $(_this.el.addModal).modal('hide');
        if (data['url'] != undefined) {
            window.open(data['url']);
        }
    },
    beforeSubmit() {
        let _this = payments;
        $(_this.el.addPaymentForm).find(_this.el.addPaymentSubmit).prop('disabled', true);
    }
};