@extends('admin.layouts.loanviewlayout')
@section('page_name')
    Transaction History
@stop
@section('extra-styles')
    <link href="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.bootstrap4.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{url(config('theme.admin.plugins'))}}/datatables/buttons.bootstrap4.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{url(config('theme.admin.plugins'))}}/datatables/responsive.bootstrap4.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{url(config('theme.front.plugins'))}}/bootstrap-datepicker/css/bootstrap-datepicker.min.css"
          rel="stylesheet">
    <link href="{{url(config('theme.front.plugins'))}}/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
    <link href="{{url(config('theme.admin.plugins'))}}/sweet-alert2/sweetalert2.css" rel="stylesheet">
    <style>
        .red {
            background: #ffc9c9;
        }
    </style>
@endsection

@section('content')
    <div>
        <div class="modal-dialog modal-full">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title mt-0">Transaction History</h4>
                    <div class="text-right">
                        <button type="button" class="btn btn-primary" id="addNewHistory">Cron Job</button>
                    </div>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Week</th>
                            <th>Date</th>
                            <th>Transaction</th>
                            <th>Amount For Trans.</th>
                            <th>Principal</th>
                            <th>Origination</th>
                            <th>Interest</th>
                            <th>Renewal</th>
                            <th>Tax</th>
                            <th>Debt Collection</th>
                            <th>Debt Collection Tax</th>
                            <th>Admin fees (incl tax)</th>
                            {{--<th>Total Balance (Excl. tax)</th>--}}
                            <th>Total Balance</th>
                            @if(auth()->user()->hasRole('super admin'))
                                <th>Action</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody class="loan_hostory_table"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('common.loanapplication.edit_calculation')
@endsection


@section('extra-js')
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/jquery.dataTables.min.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.bootstrap4.min.js"></script>

    <script src="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.responsive.min.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/responsive.bootstrap4.min.js"></script>
    <script src="{{url(config('theme.front.plugins'))}}/moment/moment.js"></script>
    <script src="{{url(config('theme.front.plugins'))}}/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
    <script src="{{url(config('theme.front.plugins'))}}/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/sweet-alert2/sweetalert2.js"></script>
    <script src="{{url(config('theme.common.js'))}}/loan.js"></script>
@endsection

@section('custom-js')
    <script>
        var admin = false;
    </script>
    @if(auth()->user()->hasRole('super admin'))
        <script>
            admin = true;
        </script>
    @endif
    <script type="text/javascript">
        var loan_id = '{!! $loan->id !!}';
        $(document).on('click', '#addNewHistory', function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'get',
                data: {
                    newEntry: 1
                },
                url: siteURL + '/ajax/loan-applications/' + loan_id + '/history',
                success: function (data) {
                    refreshTable();
                }
            });
        });

        function refreshTable() {
            $.ajax({
                dataType: 'json',
                method: 'get',
                url: siteURL + '/ajax/loan-applications/' + loan_id + '/history',
                success: function (data) {
                    str = '';
                    for (var index in data['history']) {
                        var history = data['history'][index];
                        if (admin != undefined && admin == true) {
                            var button = '';
                            if (history['payment_amount'] != '') {
                                button += '<button class="btn btn-primary editHistory" data-id="' + history['id'] + '">' +
                                    '<i class="fa fa-pencil"></i>' +
                                    '</button>';
                            }
                        }
                        if (history['debt_collection_value'] == null) {
                            history['debt_collection_value'] = '0.00';
                        }
                        if (history['debt_collection_tax'] == null) {
                            history['debt_collection_tax'] = '0.00';
                        }
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
                            '<td>' + (parseFloat(history['debt']) + parseFloat(history['debt_tax'])) + '</td>' +
                            // '<td>' + history['total_e_tax'] + '</td>' +
                            '<td>' + history['total'] + '</td>';
                        if (admin != undefined && admin == true) {
                            str += '<td>' + button + '</td>';
                        }
                        str += '</tr>';
                    }
                    $('.loan_hostory_table').html(str);
                }
            });
        }

        function total_calculate() {
            var sum = 0;
            $('#editCalculationForm input[name^="payment_amount"]').each(function (key, item) {
                if ($(item).val() != '') {
                    sum = sum + parseFloat($(item).val());
                }
            });
            var cashback = 0;
            $('#editCalculationForm input[name^="cashback_amount"]').each(function (key, item) {
                if ($(item).val() != '') {
                    cashback = cashback + parseFloat($(item).val());
                }
            });
            $('#editCalculationForm input[name="transaction_total[received]"]').val(sum);
            $('#editCalculationForm input[name="transaction_total[cash_back]"]').val(cashback);
            $('#editCalculationForm input[name="transaction_total[payment]"]').val(sum - cashback);
        }

        $('.date-picker').datepicker({
            orientation: "bottom auto",
            clearBtn: true,
            autoclose: true,
            format: "{!! config('site.date_format.js') !!}"
        });

        $(document).on('click', '.editHistory', function (e) {
            var id = $(this).data('id');
            $('#editCalculationForm').find('[name="history_id"]').val(id);
            $.ajax({
                dataType: 'json',
                method: 'get',
                url: siteURL + '/ajax/history/' + id + '/edit',
                success: function (data) {
                    console.log(data['history']['date']);
                    var currentDate = data['history']['date'];
                    console.log(currentDate);
                    var startDate = new Date(data['first_history']['date']);
                    var endDate = new Date(data['last_history']['date']);
                    $('#editCalculationForm').find('[name^="payment_amount"]').each(function (key, item) {
                        $(item).val(0);
                    });
                    $('#editCalculationForm').find('[name^="cashback_amount"]').each(function (key, item) {
                        $(item).val(0);
                    });
                    var payments = data['payments'];
                    for (var index in payments) {
                        var amount = 0;
                        if (payments[index]['amount'] > 0) {
                            amount = payments[index]['amount'];
                        }
                        $('#editCalculationForm').find('[name="payment_amount[' + payments[index]['payment_type'] + ']"]').val(amount);
                    }
                    for (var index in payments) {
                        var amount = 0;
                        if (payments[index]['cash_back_amount'] > 0) {
                            amount = payments[index]['cash_back_amount'];
                        }
                        $('#editCalculationForm').find('[name="cashback_amount[' + payments[index]['payment_type'] + ']"]').val(amount);
                    }
                    console.log(currentDate);
                    console.log(moment(currentDate).format('DD/MM/YYYY'));
                    $('#editCalculationForm').find('[name="date"]').val(moment(currentDate).format('DD/MM/YYYY'));
                    $('#editCalculationForm').find('[name="date"]').datepicker("setStartDate", startDate);
                    $('#editCalculationForm').find('[name="date"]').datepicker("setEndDate", endDate);
                    $('#editCalculationForm').find('[name="notes"]').val(payments[0]['notes']);
                    total_calculate();
                    $('#editCalculationModal').modal('show');
                }
            })
        });

        $(document).on('change blur keyup', '#editCalculationForm input', function () {
            total_calculate();
        });

        $(document).on('submit', '#editCalculationForm', function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                url: siteURL + '/ajax/history/' + $(this).find('[name="history_id"]').val(),
                method: 'post',
                data: $(this).serialize(),
                success: function (data) {
                    $('#editCalculationModal').modal('hide');
                    refreshTable();
                }
            })
        });
        refreshTable();

        $(document).on('click', '.showLoanHistory', function (e) {
            e.preventDefault();
            $('#addNewHistory').data('id', loan_id);
        });
    </script>
@endsection
