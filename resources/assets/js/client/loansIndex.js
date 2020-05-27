var loansIndex = {
    el: {
        dTable: '',
        dataTable: '#datatable',
        editLoan: '.editLoan',
        deleteLoan: '.deleteLoan',
        deleteLoanModal: '#deleteLoanModal',
        confirmDeleteLoanButton: '.confirmDeleteLoanButton',
    },
    data: {
        select_file_text: keywords.select_file,
    },
    init: function () {
        var _this = this;
        _this.bindUiActions();
    },
    bindUiActions: function () {
        var _this = this;
        _this.el.dTable = $(_this.el.dataTable).DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": clientAjaxURL + "datatable-loans",
            },
            columns: [
                {data: 'id', name: 'loan_applications.id'},
                // {data: 'reason', name: 'loan_reasons.title'},
                // {data: 'reason', name: 'loan_reasons.title_es', visible: false},
                // {data: 'reason', name: 'loan_reasons.title_nl', visible: false},
                {data: 'amount', name: 'loan_applications.amount'},
                {data: 'type', name: 'loan_types.title'},
                {data: 'type', name: 'loan_types.title_es', visible: false},
                {data: 'type', name: 'loan_types.title_nl', visible: false},
                {data: 'status', name: 'loan_status.title'},
                {data: 'status', name: 'loan_status.title_es', visible: false},
                {data: 'status', name: 'loan_status.title_nl', visible: false},
                {data: 'decline_reason', name: 'loan_decline_reasons.title'},
                {data: 'decline_reason', name: 'loan_decline_reasons.title_es', visible: false},
                {data: 'decline_reason', name: 'loan_decline_reasons.title_nl', visible: false},
                {data: 'decline_reason', name: 'loan_on_hold_reasons.title', visible: false},
                {data: 'decline_reason', name: 'loan_on_hold_reasons.title_es', visible: false},
                {data: 'decline_reason', name: 'loan_on_hold_reasons.title_nl', visible: false},
                {data: 'created_at', name: 'created_at', searchable: false},
                {data: 'start_date', name: 'start_date', searchable: false},
                {data: 'end_date', name: 'end_date', searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            "language": {
                "lengthMenu": keywords.Show + " _MENU_ " + keywords.Entries,
                "zeroRecords": keywords.NoMatchingRecordsFound,
                "info": keywords.Showing + ('_END_' == 0 ? 0 : " _START_ ") + keywords.To + " _END_ " + keywords.Of + " _TOTAL_ " + keywords.Entries,
                "infoEmpty": keywords.Showing + " 0 " + keywords.To + " 0 " + keywords.Of + " 0 " + keywords.Entries,
                "infoFiltered": "(" + keywords.FilteredFrom + " _MAX_ " + keywords.TotalRecords + ")",
                "search": keywords.Search,
                "paginate": {
                    "previous": keywords.Previous,
                    "next": keywords.Next,
                },
            },
            order: [[0, 'desc']],
            "drawCallback": function (settings) {
                initTooltip();
            },
        });

        $(document).on('click', _this.el.editLoan, function (e) {
            e.preventDefault();
            $('.other-loan-holder').html('');
            _this.setEdit($(this).data('id'));
        });
        $(document).on('click', _this.el.deleteLoan, function (e) {
            e.preventDefault();
            $(_this.el.deleteLoanModal).find(_this.el.confirmDeleteLoanButton).data('id', $(this).data('id'));
            $(_this.el.deleteLoanModal).modal('show');
        });
        $(document).on('click', _this.el.deleteLoanModal + ' ' + _this.el.confirmDeleteLoanButton, function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'delete',
                url: clientAjaxURL + 'loans/' + $(this).data('id'),
                success: function (data) {
                    _this.el.dTable.draw();
                    $(_this.el.deleteLoanModal).modal('hide');
                }
            })
        });
    },
    setEdit: function (id) {
        var _this = this;
        $.ajax({
            type: 'GET',
            url: clientAjaxURL + 'loans/' + id,
            data: {},
            dataType: 'json',
            success: function (data) {
                $.each(data['amounts'], function (i, item) {
                    if (item.type == '1') {
                        // $('.income-holder').find('.income-item').first().clone().appendTo('.income-holder');
                        // $('.income-holder').find('.income-item').last().find('.income-id').val(item.id);
                        // $('.income-holder').find('.income-item').last().find('.income-date').val(item.date);
                        // $('.income-holder').find('.income-item').last().find('.income-amount').val(item.amount);
                        // $('.income-holder').find('.income-item').last().find('.income-type').val(item.amount_type);
                        // $('.income-holder').find('.income-item').last().find('.income-image').attr('href', data.fileFolder + item.documents.file_name);
                        // $('.income-holder').find('.income-item').last().find('.income-image').html('<button class="btn btn-primary" type="button" ><i class="material-icons">attach_file</i></button>');
                        // $('.income-holder').find('.income-item').last().find('.income-image').attr('download', '');
                        // $('.income-holder').find('.income-item').last().find('.add-btn').hide();
                        // $('.income-holder').find('.income-item').last().find('.delete-btn').show();
                        // $('.income-holder').find('.income-item').last().find('.date-holder').css({'visibility': 'hidden'});
                        item['file_name'] = data.fileFolder + item.documents.file_name;
                        if (item.date != null) {
                            $(loansCreate.el.income_holder).html(loansCreate.incomeAmountHtml('main', item));
                        } else {
                            $(loansCreate.el.income_holder).append(loansCreate.incomeAmountHtml('second', item));
                        }
                        $('.income-holder').find('.income-item').last().find('.income-proof-image-hidden').val(item.documents.file_name);
                        $('[name="income_proof_image[' + loansCreate.data.amount_index + ']"]').inputFileText({
                            text: _this.data.select_file_text
                        });
                        loansCreate.data.amount_index++;
                        loansCreate.calculate();
                        datePickerInit();
                    } else {
                        loansCreate.addNewOtherLoan(i, function () {
                            $('.other-loan-holder').find('.other-loan-item').last().find('.expense-amount').val(item.amount);
                            $('.other-loan-holder').find('.other-loan-item').last().find('.expense-id').val(item.id);
                            $('.other-loan-holder').find('.other-loan-item').last().find('.expense-type').val(item.amount_type);
                            $('.other-loan-holder').find('.other-loan-item').last().find('.add-btn').hide();
                            $('.other-loan-holder').find('.other-loan-item').last().find('.delete-btn').show();
                            loansCreate.calculate();
                        });
                    }
                });

                if (data['amounts'].length > 0) {
                    // $('.income-holder').find('.income-item').first().remove();
                    // $('.income-holder').find('.income-item').first().find('.add-btn').show();
                    // $('.income-holder').find('.income-item').first().find('.delete-btn').hide();
                    // $('.income-holder').find('.income-item').first().find('.date-holder').css({'visibility': 'visible'});
                }


                globalLoanType = data.inputs.loan_type.value;

                setForm(loansCreate.el.loanApplicationForm, data.inputs);

                loansCreate.loanTypeData(data.inputs.loan_type.value, data.inputs.amount.value);

                $('#loanApplicationModal').modal('show');
                initTooltip();
                $('#apply_load_modal_submit_button').text(keywords.Save);

                $('.income-holder').find('.income-item').find('.loan-image').css({'visibility': 'visible'});
                $('.other-loan-holder').find('.other-loan-item').find('.loan-image').css({'visibility': 'visible'});

                var image = $('.other-loan-holder').find('.other-loan-item').first().find('.loan-image').find('img').attr('src');
                if (!image) {
                    $('.other-loan-holder').find('.other-loan-item').find('.loan-image').css({'visibility': 'hidden'});
                }

                loansCreate.calculate();
                loansCreate.reArrangeIncomes()
                if ($('#loanApplicationModal').find('[name="id"]').val() != '' && $('#loanApplicationModal').find('[name="id"]').val() != '0') {
                    $('#loanApplicationModal').find('[name="client_id"]').prop('disabled', true);
                }
            },
            error: function (jqXHR, exception) {
                alert(keywords.something_went_wrong);
            }
        });
        return false;
    },
};