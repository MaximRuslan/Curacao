var loanCalculation = {
    el: {},
    data: {
        loan_id: '',
        receipt_on: false,
    },
    init() {
        var _this = this;
        _this.data.loan_id = window.loan_id;
        _this.data.receipt_on = window.receipt_on;
        _this.bindUiActions();
        _this.refreshTable();
    },
    bindUiActions() {
        var _this = this;
        $(document).on('click', '.paymentReceipt', function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'post',
                url: clientAjaxURL + 'loans/' + _this.data.loan_id + '/history/' + $(this).data('id') + '/receipt',
                success: function (data) {
                    window.open(data['receipt_pdf'], 'download');
                }
            });
        });
    },
    refreshTable() {
        var _this = this;
        $.ajax({
            dataType: 'json',
            method: 'get',
            url: clientAjaxURL + 'loans/' + _this.data.loan_id + '/history',
            success: function (data) {
                str = '';
                for (var index in data['history']) {
                    var history = data['history'][index];
                    var button = '';
                    if (history['payment_amount'] != '' && _this.data.receipt_on == true) {
                        button += '   <button class="btn btn-primary paymentReceipt" data-id="' + history['id'] + '">' +
                            '<i class="material-icons">picture_as_pdf</i>' +
                            '</button>';
                    }
                    if (history['debt_collection_value'] == null) {
                        history['debt_collection_value'] = '0.00';
                    }
                    if (history['debt_collection_tax'] == null) {
                        history['debt_collection_tax'] = '0.00';
                    }
                    // if (history['transaction_name'] != null && history['transaction_name'] != '') {
                    //     history['transaction_name'] = '<span data-trigger="hover" data-container="body" title="" data-toggle="popover" data-placement="top" data-content="' + history['user_info'] + '" data-original-title="" class="label label-info">' + history['transaction_name'] + '</span>';
                    // }
                    str += '<tr>' +
                        '<td>' + history['week_iterations'] + '</td>' +
                        '<td>' + history['date'] + '</td>' +
                        '<td>' + history['transaction_name'] + '</td>' +
                        '<td>' + history['payment_amount'] + '</td>' +
                        '<td>' + history['principal'] + '</td>' +
                        '<td>' + history['origination'] + '</td>' +
                        '<td>' + history['interest'] + '</td>' +
                        '<td>' + history['renewal'] + '</td>' +
                        '<td>' + history['tax'] + '</td>' +
                        '<td>' + history['debt_collection_value'] + '</td>' +
                        '<td>' + history['debt_collection_tax'] + '</td>' +
                        '<td>' + (parseFloat(history['debt']) + parseFloat(history['debt_tax'])).toFixed(2) + '</td>' +
                        // '<td>' + history['total_e_tax'] + '</td>' +
                        '<td>' + history['total'] + '</td>';

                    str += '<td>' + button + '</td>';
                    str += '</tr>';
                }
                $('.loan_history_table').html(str);
                // $('[data-toggle="popover"]').popover();
            }
        });
    },
};