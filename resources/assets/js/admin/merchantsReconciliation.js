const merchantsReconciliation = {
    el: {
        modal: '#js--reconciliation-modal',
        form: '#js--reconciliation-form',
        addButton: '.js--reconciliation-add-button',
        editButton: '.js--reconciliation-edit-button',
        datatale: "#datatable",
        deleteModal: '#deleteReconciliationModal',
        confirmDeleteButton: '.confirmDeleteReconciliationButton',
        deleteButton: '.js--reconciliation-delete-button',
        historyButton: '.js--reconciliation-history-button',
        historyModal: "#js--reconciliation-history-modal",
        histroyTbody: "#js--reconciliation-history-tbody",
    },
    data: {
        branches_amount: {},
        dTable: '',
        columns: [
            {data: 'id', name: 'merchant_reconciliations.id', visible: false},
            {data: 'transaction_id', name: 'merchant_reconciliations.transaction_id'},
            {data: 'name', name: 'merchants.name'},
            {data: 'branch', name: 'merchant_branches.name'},
            {data: 'amount', name: 'merchant_reconciliations.amount'},
            {data: 'status', name: 'merchant_reconciliations.amount', searchable: false},
            {data: 'otp', name: 'merchant_reconciliations.otp'},
            {data: 'created_by', name: 'users.firstname'},
            {data: 'created_by', name: 'users.lastname', visible: false},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
    },
    init() {
        let _this = this;
        _this.bindUIActions();
        _this.validateForm(adminAjaxURL + 'merchants/reconciliations', 'post');
    },
    bindUIActions() {
        let _this = this;
        _this.data.dTable = $(_this.el.datatale).DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": adminAjaxURL + 'merchants/datatable-reconciliations',
            },
            columns: _this.data.columns,
            "drawCallback": function (settings) {
                initTooltip();
            },
            order: [[0, 'desc']],
            pageLength: '50',
        });

        $(document).on('change', '[name="branch_id"]', function (e) {
            $(_this.el.form).find('.js--account-payable-div').show();
            $(_this.el.form).find('#js--account-payable').text(_this.data.branches_amount[$(this).val()]);
            $(_this.el.form).find('[name="amount"]').rules('add', {
                required: true,
                max: _this.data.branches_amount[$(this).val()],
                min: 0
            });
        });

        $(document).on('click', '.js--update-account-payable', function (e) {
            e.preventDefault();
            _this.getBranches($(_this.el.form).find('[name="merchant_id"]').val(), $(_this.el.form).find('[name="branch_id"]').val(), 'payment');
        });

        $(document).on('change', '[name="merchant_id"]', function (e) {
            _this.getBranches($(this).val());
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
            $(_this.el.deleteModal).find(_this.el.confirmDeleteButton).data('id', $(this).data('id'));
            $(_this.el.deleteModal).modal('show');
        });

        $(document).on('click', _this.el.deleteModal + ' ' + _this.el.confirmDeleteButton, function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'delete',
                url: adminAjaxURL + 'merchants/reconciliations/' + $(this).data('id'),
                success: function (data) {
                    _this.data.dTable.draw();
                    $(_this.el.deleteModal).modal('hide');
                }
            })
        });
        $(document).on('click', _this.el.historyButton, function (e) {
            e.preventDefault();
            _this.showHistory($(this).data('id'));
        });
    },
    formReset() {
        let _this = this;
        $(_this.el.form)[0].reset();
        $(_this.el.form).find('.error').html('');
        $(_this.el.form).find('.select2Single').val('').select2();
        $(_this.el.form).find('input').prop('disabled', false);
        $(_this.el.form).find('select').prop('disabled', false);
        $(_this.el.form).find('.js--account-payable-div').hide();
        $(_this.el.form).find('button[type="submit"]').show();
    },
    getBranches(id, branch, type) {
        let _this = this;
        let str = '<option value="">Select Branch</option>';
        if (id != '') {
            $.ajax({
                dataType: 'json',
                method: 'get',
                url: adminAjaxURL + 'merchants/' + id + '/branches',
                data: {
                    type: type,
                },
                success: function (data) {
                    if (type == undefined || type != 'payment') {
                        for (let i in data['branches']) {
                            let selected = '';
                            if (i == branch) {
                                selected = 'selected';
                            }
                            str += `<option value="${i}" ${selected}>${data['branches'][i]}</option>`;
                        }
                        $(_this.el.form).find('[name="branch_id"]').html(str)
                    }
                    _this.data.branches_amount = data['branches_amount'];
                    let branch = $(_this.el.form).find('[name="branch_id"]').val();
                    if (branch != '') {
                        $(_this.el.form).find('.js--account-payable-div').show();
                        $(_this.el.form).find('#js--account-payable').text(_this.data.branches_amount[branch]);
                        $(_this.el.form).find('[name="amount"]').rules('add', {
                            required: true,
                            max: _this.data.branches_amount[branch],
                            min: 0
                        });
                    }
                }
            });
        } else {
            $(_this.el.form).find('[name="branch_id"]').html(str)
        }
    },
    validateForm(url, method) {
        var _this = this;
        $(_this.el.userWorkInfoform).data('validator', null);
        $(_this.el.userWorkInfoform).unbind();

        validator = $(_this.el.form).validate({
            // define validation rules
            rules: {
                merchant_id: {required: true},
                branch_id: {required: true},
                amount: {required: true},
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
                    data: $(_this.el.form).serialize(),
                    success: function (data) {
                        fullLoader.off();
                        _this.data.dTable.draw();
                        $(_this.el.modal).modal('hide');
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
    setEdit(id, type) {
        var _this = this
        $.ajax({
            dataType: 'json',
            method: 'get',
            url: adminAjaxURL + 'merchants/reconciliations/' + id + '/edit',
            success(data) {
                setForm(_this.el.form, data['inputs']);
                $(_this.el.form).find('[name="amount"]').rules('add', {
                    required: true,
                    min: 0,
                    max: data['max']
                });
                _this.getBranches(data['inputs']['merchant_id']['value'], data['inputs']['branch_id']['value']);
                if (type == 'view') {
                    $(_this.el.form).find('input').prop('disabled', true);
                    $(_this.el.form).find('select').prop('disabled', true);
                    $(_this.el.form).find('button[type="submit"]').hide();
                }
                $(_this.el.modal).modal('show');
            }
        })
    },
    showHistory(id) {
        let _this = this;
        $.ajax({
            dataType: 'json',
            method: 'get',
            url: adminAjaxURL + 'merchants/reconciliations/' + id + '/history',
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