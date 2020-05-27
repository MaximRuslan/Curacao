var referralHistory = {
    el: {},
    data: {},
    init() {
        let _this = this;
        $('.select2Single').select2();
        datePickerInit();
        _this.bindUiActions();
    },
    bindUiActions() {
        let _this = this;
        _this.data.datatable = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": adminAjaxURL + 'datatable-referral-histories',
                data: function (d) {
                    d.start_date = $('[name="start_date"]').val();
                    d.end_date = $('[name="end_date"]').val();
                    d.loan_status = $('[name="loan_status"]').val();
                }
            },
            columns: [
                {data: 'id', name: 'referral_histories.id', visible: false},
                {data: 'date', name: 'referral_histories.date'},
                {data: 'bonus_payout', name: 'referral_histories.bonus_payout'},
                {data: 'name', name: 'users.firstname'},
                {data: 'name', name: 'users.lastname', visible: false},
                {data: 'name', name: 'users.id_number', visible: false},
                {data: 'status', name: 'referral_histories.status'},
                {data: 'ref_name', name: 'ref.firstname'},
                {data: 'ref_name', name: 'ref.lastname', visible: false},
                {data: 'ref_name', name: 'ref.id_number', visible: false},
            ],
            order: [[0, 'desc']],
        });

        $(document).on('click', '#js--filter-data', function (e) {
            e.preventDefault();
            _this.data.datatable.draw();
        });
        $(document).on('click', '#js--filter-excel', function (e) {
            e.preventDefault();
            _this.excelDownload();
        });

        $(document).on('change', '[name="start_date"]', function () {
            let start_date = null;
            if ($(this).val() != '') {
                start_date = new Date(moment($(this).val(), 'DD-MM-YYYY'));
            }
            $('[name="end_date"]').datepicker('setStartDate', start_date);
        });
    },
    excelDownload() {
        $.ajax({
            dataType: 'json',
            method: 'post',
            url: adminAjaxURL + 'referral-histories/excel',
            data: {
                start_date: $('[name="start_date"]').val(),
                end_date: $('[name="end_date"]').val(),
                loan_status: $('[name="loan_status"]').val(),
            },
            success: function (data) {
                var url = data['url'];
                $('#exportPDFLink').attr('href', url);
                $('#exportPDFLink').attr('download', '');
                $('#exportPDFLink')[0].click();
            }
        });
    }
};