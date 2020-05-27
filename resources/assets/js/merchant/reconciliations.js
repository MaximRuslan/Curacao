const reconciliations = {
    el: {
        modal: '#js--reconciliation-approve-modal',
        form: '#js--reconciliation-approve-form',
        button: '.js--reconciliation-approve-button',
        historyButton: '.js--reconciliation-history-button',
        histroyTbody: '#js--reconciliation-history-tbody',
        historyModal: '#js--reconciliation-history-modal',
    },
    data: {
        reconciliations: {
            url: 'datatable-reconciliations',
            columns: [
                {name: 'merchant_reconciliations.id', data: 'DT_Row_Index'},
                {name: 'merchant_reconciliations.transaction_id', data: 'transaction_id'},
                {name: 'merchant_branches.name', data: 'branch', visible: merchant},
                {name: 'merchant_reconciliations.amount', data: 'amount'},
                {name: 'merchant_reconciliations.status', data: 'status'},
                {name: 'merchant_reconciliations.created_at', data: 'date', searchable: false},
                {name: 'action', data: 'action', searchable: false, orderable: false},
            ],
            pageLength: 50
        }
    },
    init() {
        let _this = this;
        _this.bindUIActions();
        common.validationForm({
            form: _this.el.form,
            rules: {
                otp: {required: true},
            },
            method: 'post',
            url: 'reconciliations',
            callback: _this.reconciliationSuccess,
        });
    },
    bindUIActions() {
        let _this = this;
        _this.data.dtable = common.datatable(_this.data.reconciliations);

        $(document).on('click', _this.el.button, function (e) {
            e.preventDefault();
            $(_this.el.form)[0].reset();
            $(_this.el.form).find('.error').html('');
            $(_this.el.modal).find('[name="id"]').val($(this).data('id'));
            $(_this.el.modal).modal('show');
        });

        $(document).on('click', _this.el.historyButton, function (e) {
            e.preventDefault();
            _this.showHistory($(this).data('id'));
        });
    },

    reconciliationSuccess() {
        let _this = reconciliations;
        _this.data.dtable.draw();
        $(_this.el.modal).modal('hide');
    },
    showHistory(id) {
        let _this = this;
        $.ajax({
            dataType: 'json',
            method: 'get',
            url: merchant_ajax_url + 'reconciliations/' + id + '/history',
            success(data) {
                let str = ``;
                for (let i in data['history']) {
                    let histroy = data['history'][i];
                    str += `<tr>
                        <td>${histroy['username']}</td>
                        <td>${histroy['status']}</td>
                        <td>${histroy['date_time']}</td>
                    </tr>`;
                }
                $(_this.el.histroyTbody).html(str);
                $(_this.el.historyModal).modal('show');
            }
        });
    }

};