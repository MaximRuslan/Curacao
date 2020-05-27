@extends('admin.layouts.app')
@section('page_name')
    Loans
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
    <link href="{!! asset('admin/plugins/select2/css/select2.min.css') !!}" rel="stylesheet">
@endsection

@section('content')

    <div class="row">
        <div class="col-sm-12">
            @if(auth()->user()->hasRole('super admin|admin|processor'))
                <div class="btn-group pull-right m-b-20">
                    <a id="createLoadApplicationButton" href="" class="btn btn-default waves-effect waves-light">Add</a>
                </div>
            @endif
            <h4 class="page-title">
                Loans
                @if(isset($status_name))
                    ({!! $status_name !!})
                @endif
            </h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <table id="loan-application-table" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th style="width: 10%;">Client</th>
                        <th>Client</th>
                        <th style="width: 5%;">Id Client</th>
                        <th style="width: 5%;">Loan ID</th>
                        <th style="width: 5%;">Type</th>
                        <th style="width: 4%;">Amount</th>
                        <th style="width: 4%;">Requested Date</th>
                        <th style="width: 5%;">Start Date</th>
                        <th style="width: 10%;">Outstanding Balance</th>
                        <th style="width: 10%;">Completed Date</th>
                        <th style="width: 10%;">Status</th>
                        <th style="width: 15%;">Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    @include('common.loanapplication.createOnHold')
    @hasanyrole('super admin|admin|processor|credit and processing')
    @include('common.loanapplication.transaction')
    @endhasanyrole
    @if(auth()->user()->hasRole('super admin|admin|processor'))
        @include('common.applyLoanModal')
    @endif
    {{--@include('common.loanapplication.loan_application_history_table')--}}
    @include('common.delete_confirm',[
        'modalId'=>'deleteLoanApplication',
        'action'=>route('loan-applications.destroy','deleteId'),
        'item'=>'it',
        'callback'=>'showMsg'
    ])
    @include('common.approve_loan')
    @include('common.current_loan')
    @include('common.not_approved_loan')
    @include('common.reject_loan')
    @include('common.loan_status_history')

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
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
    <script src="{!! asset('admin/plugins/select2/js/select2.full.min.js') !!}"></script>
@endsection

@section('custom-js')
    <script type="text/javascript">
        var startDate = new Date();
        startDate.setDate(startDate.getDate() + 1);

        $('.date-picker').datepicker({
            orientation: "bottom auto",
            clearBtn: true,
            startDate: startDate,
            autoclose: true,
            format: dateFormat
        });
        startDate.setDate(startDate.getDate() - 1);
        $('.date-picker-today').datepicker({
            orientation: "bottom auto",
            clearBtn: true,
            startDate: startDate,
            autoclose: true,
            format: dateFormat
        });
        $('.old-date-picker').datepicker({
            orientation: "bottom auto",
            clearBtn: true,
            endDate: startDate,
            autoclose: true,
            format: dateFormat
        });
        $('.all-date-picker').datepicker({
            orientation: "bottom auto",
            clearBtn: true,
            autoclose: true,
            format: dateFormat
        });

        var oTable = "";

        $(document).ready(function () {
            oTable = $('#loan-application-table').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: {
                    "url": "{{route('loan-application-data')}}",
                    "type": "POST",
                    data: function (d) {
                        d.status = "{!! request('status') !!}";
                    }
                },
                order: [],
                "drawCallback": function (settings) {
                    InitTooltip();
                },
                columns: [
                    {data: 'updated_at', name: 'loan_applications.updated_at', visible: false},
                    {data: 'user_first_name', name: 'users.firstname'},
                    {data: 'user_first_name', name: 'users.lastname', visible: false},
                    {data: 'user_id_number', name: 'users.id_number'},
                    {data: 'id', name: 'loan_applications.id'},
                    {data: 'loan_type_title', name: 'loan_types.title'},
                    // {data: 'reason_title', name: 'loan_reasons.title'},
                    {data: 'amount', name: 'loan_applications.amount'},
                    {data: 'created_at', name: 'created_at', searchable: false},
                    {data: 'start_date', name: 'start_date', searchable: false},
                    {data: 'outstanding_balance', searchable: false, orderable: false},
                    {data: 'end_date', name: 'end_date', searchable: false},
                    {data: 'loan_status_title', name: 'loan_status.title'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                order: [[0, 'desc']],
                pageLength: '50'
            });
        });

        function confirmUpdateStatus(element, reasonRequired) {
            if (reasonRequired == undefined) {
                reasonRequired = true;
            }
            var modalId = $(element).attr('data-modal-id');
            var Id = $(element).attr('data-id');
            $('#' + modalId).modal('show');
            $('#' + modalId).find('input[name=id]').val(Id);
            $('#' + modalId).find('input[name=reason_required]').val(reasonRequired);
        }

        function updateStatus(modal, status) {
            $('#' + modal).modal('hide');
            if ($('#' + modal).find('input[name=reason_required]').length > 0) {
                return action($('#' + modal).find('input[name=id]').val(), status, '', '', false);
            } else {
                return action($('#' + modal).find('input[name=id]').val(), status);
            }
        }

        function SaveStatusApplication(form) {
            $('#loanApplicationModal').modal('hide');
            $('#loanOnHoldApplicationModal').modal('hide');
            var id = $(form).find('input[name="id"]').val();
            var reason = $(form).find('#decline_reason').val();
            var status = $(form).find('input[name="js__status"]').val();
            var description = $(form).find('#decline_description').val();
            action(id, status, reason, description);
            return false;
        }

        var notApproved = false;
        var confirmVal = false;

        function action(id, status, reason, description, reasonRequired) {
            if (reasonRequired == undefined) {
                reasonRequired = true;
            }
            console.log(status);
            if (reason == undefined) {
                reason = '';
            }
            if (description == undefined) {
                description = '';
            }
            var note = '';
            if (status == 'approved' || status == "current") {
                fullLoader.on({
                    text: 'Loading !'
                });
                var action = '{{route('loan-applications.update','editId')}}'
                action = action.replace('editId', id);
                var data = {'status': status, note: note};
                $.ajax({
                    type: 'PUT',
                    url: action,
                    data: data,
                    dataType: 'json',
                    success: function (data) {
                        successMsg('Message');
                        oTable.draw(true);
                        fullLoader.off();
                    },
                });
            } else if (status == 'On Hold') {
                fullLoader.on({
                    text: 'Loading !'
                });
                $(".jq__title").html("On Hold Loan");
                $("#on_hold_form").find('input[name="js__status"]').val('On Hold');
                var action = '{{route('loan-applications.update','editId')}}';
                note = $('#not_approved_note').val();
                action = action.replace('editId', id);
                if (reason == '') {
                    $('#loanOnHoldApplicationModal').find('input[name="id"]').val(id);
                    $('#loanOnHoldApplicationModal').modal('show');
                    return;
                } else {
                    var data = {'status': status, 'reason': reason, 'description': description, note: note};
                }
                $.ajax({
                    type: 'PUT',
                    url: action,
                    data: data,
                    dataType: 'json',
                    success: function (data) {
                        successMsg('Message');
                        oTable.draw(true);
                        fullLoader.off();
                    },
                });
            } else {
                fullLoader.on({
                    text: 'Loading !'
                });
                $(".jq__title").html("Reject Loan");
                var action = '{{route('loan-applications.update','editId')}}'
                action = action.replace('editId', id);
                note = $('#reject_note').val();
                console.log(note);
                if (status == 'Declined' && reasonRequired) {
                    $("#decline_form").find('input[name="js__status"]').val('Declined');
                    if (reason == '') {
                        $('#loanApplicationModal').find('input[name="id"]').val(id);
                        $('#loanApplicationModal').modal('show');
                        return;
                    } else {
                        var data = {'status': status, 'reason': reason, 'description': description, note: note};
                    }
                } else {
                    var data = {'status': status, note: note};
                }
                console.log(data);
                $.ajax({
                    type: 'PUT',
                    url: action,
                    data: data,
                    dataType: 'json',
                    success: function (data) {
                        successMsg('Message');
                        fullLoader.off();
                        oTable.draw(true);
                    },
                });
            }
        }

        $(document).on('click', '#addNewHistory', function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'get',
                data: {
                    newEntry: 1
                },
                url: ajaxURL + 'loan-applications/' + $(this).data('id') + '/history',
                success: function (data) {
                    str = '';
                    for (var index in data['history']) {
                        var history = data['history'][index];
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
                            '<td>' + history['debt'] + '</td>' +
                            '<td>' + history['debt_tax'] + '</td>' +
                            '<td>' + history['total_e_tax'] + '</td>' +
                            '<td>' + history['total'] + '</td>' +
                            '</tr>';
                    }
                    $('.loan_hostory_table').html(str);
                    //    $('#loanHistoryModal').modal('show');
                }
            });
        });

        $(document).on('click', '.showLoanHistory', function (e) {
            e.preventDefault();
            $('#addNewHistory').data('id', $(this).data('id'));
            $.ajax({
                dataType: 'json',
                method: 'get',
                url: ajaxURL + 'loan-applications/' + $(this).data('id') + '/history',
                success: function (data) {
                    str = '';
                    for (var index in data['history']) {
                        var history = data['history'][index];
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
                            '<td>' + history['debt'] + '</td>' +
                            '<td>' + history['debt_tax'] + '</td>' +
                            '<td>' + history['total_e_tax'] + '</td>' +
                            '<td>' + history['total'] + '</td>' +
                            '</tr>';
                    }
                    $('.loan_hostory_table').html(str);
                    $('#loanHistoryModal').modal('show');
                }
            });
        });
        $('.close').click(function () {
            $('#loanHistoryModal').modal('hide');
        });
        $('#loan_transaction_type').change(function () {
            if ($(this).val() == '1') {
                $('#loan_payment_types').show();
                $('[name="payment_type"]').val('');
            } else {
                $('#loan_payment_types').hide();
            }
        });
        if ($('#loan_transaction_type').val() == '1') {
            $('#loan_payment_types').show();
            $('#loan_notes').show();
            $('[name="payment_type"]').val('');
        } else {
            $('#loan_payment_types').hide();
            $('#loan_notes').hide();
        }

        $(document).on('click', '.editTransaction', function () {
            $.ajax({
                dataType: 'json',
                url: ajaxURL + 'transactions/' + $(this).data('id'),
                method: 'get',
                success: function (data) {
                    var htmlContent = '' +
                        '<form id="loan_transaction_edit_form" data-id="' + data['transaction']['id'] + '">' +
                        '<div class="text-center">' +
                        '   <div class="col-md-12">' +
                        '       <h5>Payment Type</h5>';
                    if (data['payment_types'][data['transaction']['payment_type']]) {
                        htmlContent += '       <input type="text" class="form-control" value="' + data['payment_types'][data['transaction']['payment_type']] + '" readonly>';
                    } else {
                        htmlContent += '       <input type="text" class="form-control" value="" readonly>';
                    }
                    htmlContent += '   </div>' +
                        '   <div class="col-md-12">' +
                        '       <h5>Amount</h5>' +
                        '       <input type="text" class="form-control" value="' + data['transaction']['amount'] + '" readonly>' +
                        '   </div>' +
                        '   <div class="col-md-12">' +
                        '       <h5>Notes</h5>' +
                        '       <textarea name="notes" id="notes" cols="30" rows="10" class="form-control">' + data['transaction']['notes'] + '</textarea>' +
                        '   </div>' +
                        '</div>' +
                        '</form>';
                    swal({
                        html: htmlContent,
                        showCancelButton: true,
                        cancelButtonClass: 'btn-danger btn-md waves-effect',
                        confirmButtonClass: 'btn-primary btn-md waves-effect waves-light updateTransactionButton',
                        confirmButtonText: 'Save',
                        cancelButtonText: 'Go back',
                        title: "Edit Transaction"
                    });
                    $('#payment_type').attr('disabled', true);
                }
            });
        });

        $(document).on('click', '.updateTransactionButton', function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                url: ajaxURL + 'transactions/' + $('#loan_transaction_edit_form').data('id'),
                method: 'post',
                data: $('#loan_transaction_edit_form').serialize(),
                success: function (data) {
                    transactionTable.draw(false);
                }
            })
        });

        $('#loanTransactionModal').on('shown.bs.modal', function () {
            $(document).off('focusin.modal');
            $('#country_selection').select2();
            $('#branch_selection').select2();
            $('#loan_transaction_type').select2();
        });

        $('#loanOnHoldApplicationModal').on('hidden.bs.modal', function () {
            fullLoader.off();
        });

        $('#loanApplicationModal').on('hidden.bs.modal', function () {
            fullLoader.off();
        });
        $(document).on('click', '.showLoanStatusHistory', function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'get',
                url: ajaxURL + 'loan-applications/' + $(this).data('id') + '/status-history',
                success: function (data) {
                    str = '';
                    for (var index in data['history']) {
                        var history = data['history'][index];
                        str += '<tr>' +
                            '   <td>' + history['user_name'] + '</td>' +
                            '   <td>' + history['loan_status'] + '</td>' +
                            '   <td>' + history['note'] + '</td>' +
                            '   <td>' + history['date'] + '</td>' +
                            '</tr>';
                    }
                    $('#loan_status_history_table').html(str);
                    $('#loan_status_history_model').modal('toggle');
                }
            });
        });
    </script>
@endsection
