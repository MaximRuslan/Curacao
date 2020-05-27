var credit = {
    el: {
        datatable: '#datatable',
        creditAddModalOpen: '.creditAddModalOpen',
        creditCreateModal: "#creditModal",
        creditCreateForm: '#credit_form',
        bank_select: '#bank_id',
        branch_select: '#branch_id',
        creditCreateButton: '.creditCreateButton',
        creditTermsCheckbox: '#loan_model_terms_checkbox',
        creditAmount: '#credit_amount',
        transactionCharge: '#transaction_charge',
        editCredit: '.editCredit',
        deleteCreditModal: '#deleteCreditModal',
        confirmDeleteCreditButton: '.confirmDeleteCreditButton',
        deleteCredit: '.deleteCredit',
    },
    data: {
        dTable: '',
    },
    init: function () {
        var _this = this;
        _this.bindUiActions();
        $(_this.el.bank_select + ',' + _this.el.branch_select).select2({width:'100%'});
    },
    bindUiActions: function () {
        var _this = this;

        _this.data.dTable = $(_this.el.datatable).DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": clientAjaxURL + 'datatable-credits'
            },
            columns: [
                {data: 'updated_at', searchable: false, visible: false},
                {data: 'payment_type', name: 'credits.payment_type', searchable: false},
                {data: 'amount', name: 'credits.amount'},
                {data: 'bank_name', name: 'banks.name', visible: false},
                {data: 'branch_name', name: 'branches.title', visible: false},
                {data: 'branch_name', name: 'branches.title_es', visible: false},
                {data: 'branch_name', name: 'branches.title_nl', visible: false},
                {data: 'transaction_charge', name: 'transaction_charge', visible: false},
                {data: 'info', name: 'transaction_charge', searchable: false, orderable: false},
                {data: 'notes', name: 'notes'},
                {data: 'status', name: 'notes', orderable: false, searchable: false},
                {data: 'created_at', name: 'credits.created_at', searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            order: [[0, 'desc']],
            pageLength: '50',
            "language": {
                "lengthMenu": keywords.Show + " _MENU_ " + keywords.Entries,
                "zeroRecords": keywords.NoMatchingRecordsFound,
                "info": keywords.Showing + " _START_ " + keywords.To + " _END_ " + keywords.Of + " _MAX_ " + keywords.Entries,
                "infoEmpty": keywords.Showing + " _START_ " + keywords.To + " _END_ " + keywords.Of + " _MAX_ " + keywords.Entries,
                "infoFiltered": "(" + keywords.FilteredFrom + " _MAX_ " + keywords.TotalRecords + ")",
                "search": keywords.Search,
                "paginate": {
                    "previous": keywords.Previous,
                    "next": keywords.Next,
                },
            },
            "drawCallback": function (settings) {
                initTooltip();
            },
        });

        $(document).on('click', _this.el.creditAddModalOpen, function (e) {
            e.preventDefault();
            _this.creditFormReset();
            $(_this.el.creditCreateModal).find('.modal-title').html($(this).data('title'));
            $(_this.el.creditCreateModal).find('[name="payment_type"]').val($(this).data('payment-type'));
            _this.payment_type_change();
            $(_this.el.creditCreateForm).find('input,textarea,select').prop('disabled', false);
            $(_this.el.creditCreateModal).modal('show');
        });

        $(document).on('submit', _this.el.creditCreateForm, function (e) {
            e.preventDefault();
            _this.saveCredit();
        });

        $(document).on('change', _this.el.creditTermsCheckbox, function () {
            if ($('#loan_model_terms_checkbox:checked').val() == 1) {
                $(_this.el.creditCreateButton).removeAttr('disabled');
            } else {
                $(_this.el.creditCreateButton).attr('disabled', true);
            }
        });
        $(document).on('change keyup', _this.el.bank_select + ',#credit_amount', function () {
            _this.transactionChargeCalculation();
        });
        $(document).on('click', _this.el.editCredit, function (e) {
            e.preventDefault();
            var view = $(this).data('type');
            _this.setEdit($(this).data('id'), view);
        });
        $(document).on('click', _this.el.deleteCredit, function (e) {
            e.preventDefault();
            $(_this.el.deleteCreditModal).find(_this.el.confirmDeleteCreditButton).data('id', $(this).data('id'));
            $(_this.el.deleteCreditModal).modal('show');
        });

        $(document).on('click', _this.el.deleteCreditModal + ' ' + _this.el.confirmDeleteCreditButton, function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'delete',
                url: clientAjaxURL + 'credits/' + $(this).data('id'),
                success: function (data) {
                    _this.data.dTable.draw();
                    $('.total_amount').html(data['wallet']);
                    $('.available_amount').html(data['available_balance']);
                    $(_this.el.deleteCreditModal).modal('hide');
                }
            })
        });
    },
    payment_type_change: function () {
        var _this = this;
        if ($(_this.el.creditCreateModal).find('[name="payment_type"]').val() == 1) {
            $('.bank_div').hide();
            $('.branch_div').show();
        } else if ($(_this.el.creditCreateModal).find('[name="payment_type"]').val() == 2) {
            $('.bank_div').show();
            $('.branch_div').hide();
        } else {
            $('.bank_div').show();
            $('.branch_div').show();
        }
    },
    saveCredit: function () {
        var _this = this;
        $.ajax({
            method: 'post',
            url: clientAjaxURL + 'credits',
            data: $(_this.el.creditCreateForm).serialize(),
            dataType: 'json',
            success: function (data) {
                $(_this.el.creditCreateModal).modal('hide');
                _this.data.dTable.draw(true);
                _this.creditFormReset();
                $('.total_amount').html(data['wallet']);
                $('.available_amount').html(data['available_balance']);
            },
            error: function (jqXHR, exception) {
                var Response = jqXHR.responseText;
                Response = $.parseJSON(Response);
                displayErrorMessages(Response, $(_this.el.creditCreateForm), 'input');
            }
        });
    },
    creditFormReset: function () {
        var _this = this;
        $('#walletDateTime').html('');
        $(_this.el.creditCreateForm).find('[name="id"]').val('');
        $(_this.el.creditCreateForm)[0].reset();
        $(_this.el.creditCreateButton).attr('disabled', true);
        $(_this.el.bank_select + ',' + _this.el.branch_select).val('').select2();
        $(".help-block").html("");
    },

    transactionChargeCalculation: function () {
        var _this = this;

        var amount = 0;
        if ($(_this.el.creditAmount).val() != '') {
            amount = round($(_this.el.creditAmount).val(), 2);
        }

        var transaction_type = $(_this.el.bank_select + ' option[value="' + $(_this.el.bank_select).val() + '"]').data('transaction-type');
        var transaction_fee = $(_this.el.bank_select + ' option[value="' + $(_this.el.bank_select).val() + '"]').data('transaction-amount');
        var transaction_charge = 0;
        if (transaction_type == 1) {
            transaction_charge = amount * transaction_fee / 100;
        } else if (transaction_type == 2) {
            transaction_charge = transaction_fee;
        }
        $(_this.el.transactionCharge).val(transaction_charge);
    },
    setEdit: function (id, view) {
        var _this = this;
        $.ajax({
            dataType: 'json',
            url: clientAjaxURL + 'credits/' + id + '/edit',
            method: 'get',
            success: function (data) {
                $(_this.el.creditCreateForm).find('input,textarea,select').prop('disabled', false);
                setForm(_this.el.creditCreateForm, data.inputs);
                $('#walletDateTime').html('Date: ' + data.inputs.created_at.value);
                $(".terms_condition").show();
                _this.payment_type_change();
                if (view != undefined && view == "view") {
                    $(".terms_condition").hide();
                    $(_this.el.creditCreateForm).find('input,textarea,select').prop('disabled', true);
                }
                $(_this.el.creditCreateModal).modal('show');
            },
            error: function (jqXHR, exception) {
                var Response = jqXHR.responseText;
                Response = $.parseJSON(Response);
                displayErrorMessages(Response, $(_this.el.creditCreateForm), 'input');
            }
        });
    },
};
