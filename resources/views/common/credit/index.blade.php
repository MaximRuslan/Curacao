@extends('admin.layouts.app')
@section('page_name')
    @if(request('type')==2)
        Transfer To Bank -
    @elseif(request('type')==1)
        Cash Payouts -
    @endif
    @if(request('status')==1)
        Requests
    @elseif(request('status')==2)
        @if(request('type')==1)
            In Process
        @elseif(request('type')==2)
            Approved
        @endif
    @elseif(request('status')==3)
        Completed
    @elseif(request('status')==4)
        Rejected
    @endif
@stop
@section('extra-styles')

    <link href="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.bootstrap4.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{url(config('theme.admin.plugins'))}}/datatables/buttons.bootstrap4.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{url(config('theme.admin.plugins'))}}/datatables/responsive.bootstrap4.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{url(config('theme.admin.plugins'))}}/select2/css/select2.min.css" rel="stylesheet"
          type="text/css"/>
    <style>
        .forCheckbox {
            padding: 0 !important;
        }
    </style>
    <link href="{{url(config('theme.admin.plugins'))}}/sweet-alert2/sweetalert2.css" rel="stylesheet">
@endsection


@section('content')
    <div class="row">
        <div class="col-sm-12">
            @if(!auth()->user()->hasRole('super admin|processor'))
                <div class="btn-group pull-right m-b-20">
                    <a data-toggle="modal" href="#creditModal" class="btn btn-default waves-effect waves-light">Add</a>
                </div>
            @endif
            <h4 class="page-title">
                @if(request('type')==2)
                    Transfer To Bank -
                @elseif(request('type')==1)
                    Cash Payouts -
                @endif
                @if(request('status')==1)
                    Requests
                @elseif(request('status')==2)
                    @if(request('type')==1)
                        In Process
                    @elseif(request('type')==2)
                        Approved
                    @endif
                @elseif(request('status')==3)
                    Completed
                @elseif(request('status')==4)
                    Rejected
                @endif
            </h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            @if(auth()->user()->hasRole('super admin|credit and processing'))
                <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-md-3"></div>
                    <div class="col-md-3"></div>
                    <div class="col-md-3">
                        {!!Form::label('branch_id','Branch')!!}
                        {!!Form::select('branch_id',$branches,old('branch_id'),['class'=>'form-control','placeholder'=>'All','id'=>'branch_id'])!!}
                    </div>
                </div>
            @endif
            <div class="card-box table-responsive mt-3">
                <table id="credit-table" class="table  table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th>Id</th>
                        @if(request('status')==1||request('status')==2)
                            <th style="width: 5%;"></th>
                        @endif
                        <th>User</th>
                        <th>User</th>
                        @if(!request('type'))
                            <th>Payment Type</th>
                        @endif
                        <th>Amount</th>
                        @if(request('type')==2)
                            <th>Bank</th>
                            <th>Transaction Charge</th>
                        @elseif(request('type')==1)
                            <th>Branch</th>
                        @endif
                        <th>Notes</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                </table>
                @if(request('status')==1 || request('status')==2)
                    <div class="row">
                        <div class="col-md-6">
                            <div class="text-left">
                                <button class="btn btn-primary" id="select_all">Select All</button>
                                <button class="btn btn-danger" id="deselect_all">Deselect All</button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-right">
                                @if(request('status')==1)
                                    @if(request('type')==2)
                                        <button class="btn btn-primary inprocess_selected">In Process Selected</button>
                                    @elseif(request('type')==1)
                                        <button class="btn btn-primary approved_selected">Approve Selected</button>
                                    @endif
                                @endif
                                @if(request('status')==2 && request('type')==2)
                                    <button class="btn btn-primary complete_selected">Complete Selected</button>
                                @endif
                                @if(request('status')==1 || request('status')==2)
                                    <button class="btn btn-danger reject_selected">Reject Selected</button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @include('common.delete_confirm',[
        'modalId'=>'deleteCredit',
        'action'=>route('credits.destroy','deleteId'),
        'item'=>'it',
        'callback'=>'showMsg'
        ])
    @include('common.credit.create')
    @include('common.credit.reject')
    @include('common.credit.statusHistory')
    @if(request('type')==1 && (request('status')==2 || request('status')==3))
        @include('common.credit.wallet')
    @endif
@endsection
@section('extra-js')
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/jquery.dataTables.min.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.bootstrap4.min.js"></script>

    <script src="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.responsive.min.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/responsive.bootstrap4.min.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/select2/js/select2.full.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/sweet-alert2/sweetalert2.js"></script>
@endsection

@section('custom-js')
    <script type="text/javascript">

        $(".processor_only").hide();
        var oTable = "";
        $('#users_select,#payment_type_select,#bank_id,#branch_id').val('').select2();
        $(document).ready(function () {

            oTable = $('#credit-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{route('credits-data')}}",
                    "type": "POST",
                    "data": function (d) {
                        d.branch_id = $('#branch_id').val();
                        d.status = "{!! request('status') !!}";
                        d.type = "{!! request('type') !!}";
                    }
                },
                order: [[0, 'desc']],
                "drawCallback": function (settings) {
                    InitTooltip();
                },
                columns: [
                    {data: 'updated_at', name: 'credits.updated_at', visible: false},
                        @if(request('status')==1||request('status')==2)
                    {
                        data: 'credit_select', searchable: false, orderable: false, className: 'forCheckbox'
                    },
                        @endif
                    {
                        data: 'user_name', name: 'users.firstname'
                    },
                    {data: 'user_name', name: 'users.lastname', visible: false},
                        @if(!request('type'))
                    {
                        data: 'payment_type', searchable: false, orderable: false
                    },
                        @endif
                    {
                        data: 'amount', name: 'amount'
                    },
                        @if(request('type')==2)
                    {
                        data: 'bank_name', name: 'banks.name'
                    },
                    {data: 'transaction_charge', name: 'transaction_charge'},
                        @elseif(request('type')==1)
                    {
                        data: 'branch_name', name: 'branches.title'
                    },
                        @endif
                    {
                        data: 'notes', name: 'notes'
                    },
                    {data: 'status', name: 'notes', orderable: false, searchable: false},
                    {data: 'created_at', name: 'created_at', searchable: false},
                    {
                        data: 'action', name: 'action', orderable: false, searchable: false
                    },
                ],
                pageLength: '50',
            });
            $('#creditModal').on('hidden.bs.modal', function () {
                $('#creditModal').find('form')[0].reset();
                $('#creditModal').find('form').find('input[name="id"]').val('');
                $('#creditModal').find('input').removeAttr('disabled');
                $('#creditModal').find('button[type="submit"]').show();
                $('#users_select,#payment_type_select,#bank_id,#branch_id').val('').select2();
                @if(auth()->user()->hasrole('processor'))
                $(".processor_only").hide();
                $(".admin_only").show();
                @endif
                $(".help-block").html("");
                $(".form-group").removeClass("has-error");
                $('#wallet').html('');
                $('#available_wallet').html('');
            });


            $("#status").on('change', function () {
                if ($("#status").val() == "2") {
                    $(".jq__required").html(" *");
                } else {
                    $(".jq__required").html("");
                }
            });
            $(document).on('change', '#branch_id', function (e) {
                e.preventDefault();
                oTable.draw();
            });
        });

        function setEdit(id, type) {
            @if(auth()->user()->hasrole('processor'))
            $(".processor_only").show();
            $(".admin_only").hide();
            @endif
            if (type == undefined) {
                type = '';
            }
            var action = '{{route('credits.show','')}}/' + id
            $.ajax({
                type: 'GET',
                url: action,
                data: {},
                dataType: 'json',
                success: function (data) {
                    str = '<option value="">Bank</option>';
                    for (var index in data['banks']) {
                        var tax_transaction = 0;
                        if (data['banks'][index]['tax_transaction'] != null) {
                            tax_transaction = data['banks'][index]['tax_transaction'];
                        }
                        str += '<option value="' + data['banks'][index]['id'] + '" data-transaction-type="' + data['banks'][index]['transaction_fee_type'] + '" data-transaction-amount="' + (parseFloat(data['banks'][index]['transaction_fee']) + parseFloat(tax_transaction)) + '">' + data['banks'][index]['name'] + '-' + data['banks'][index]['account_number'] + '</option>';
                    }
                    $('#bank_id').html(str);
                    str = '<option value="">Branch</option>';
                    for (var index in data['branches']) {
                        str += '<option value="' + index + '">' + data['branches'][index] + '</option>';
                    }
                    $('#branch_id').html(str);
                    $('#wallet').html('<label>Wallet Balance: ' + data['wallet'] + '</label>');
                    $('#available_wallet').html('<label>Available Balance: ' + data['available_balance'] + '</label>');
                    $("#walletVal").val(data['wallet']);
                    setFormValues('credit_form', data.inputs);
                    payment_type_change();
                    $('#creditModal').modal('show');
                    if (type == 'view') {
                        setTimeout(function () {
                            $('#creditModal').find('input,select').attr('disabled', 'disabled');
                            $('#creditModal').find('button[type="submit"]').hide();
                        }, 100);
                    }
                },
                error: function (jqXHR, exception) {
                    var Response = jqXHR.responseText;
                    ErrorBlock = $(form);
                    Response = $.parseJSON(Response);
                    DisplayErrorMessages(Response, ErrorBlock, 'input');
                }
            });
        }

        function SaveCredit(form) {
            var options = {
                target: '',
                url: $(form).attr('action'),
                type: 'POST',
                success: function (res) {
                    successMsg('Message');
                    $('#credit_form')[0].reset();
                    $('.help-block').html('');
                    $('#creditModal').modal('hide');
                    location.reload();
                },
                error: function (jqXHR, exception) {
                    var Response = jqXHR.responseText;
                    ErrorBlock = $(form);
                    Response = $.parseJSON(Response);
                    DisplayErrorMessages(Response, ErrorBlock, 'input');
                }
            }
            $(form).ajaxSubmit(options);
            return false;

        }

        $(document).on('change', '#users_select', function () {
            $.ajax({
                dataType: 'json',
                method: 'post',
                url: ajaxURL + "users/" + $(this).val() + '/banks-list',
                success: function (data) {
                    str = '<option value="">Bank</option>';
                    for (var index in data['banks']) {
                        var tax_transaction = 0;
                        if (data['banks'][index]['tax_transaction'] != null) {
                            tax_transaction = data['banks'][index]['tax_transaction'];
                        }
                        if ($('#bank_id').val() != "") {
                            str += '<option value="' + data['banks'][index]['id'] + '" data-transaction-type="' + data['banks'][index]['transaction_fee_type'] + '" data-transaction-amount="' + (parseFloat(data['banks'][index]['transaction_fee']) + parseFloat(tax_transaction)) + '"';
                            if (data['banks'][index]['id'] == $('#bank_id').val()) {
                                str += ' selected';
                            }
                            str += '>' + data['banks'][index]['name'] + " - " + data['banks'][index]['account_number'] + '</option>';
                        } else {
                            str += '<option value="' + data['banks'][index]['id'] + '" data-transaction-type="' + data['banks'][index]['transaction_fee_type'] + '" data-transaction-amount="' + (parseFloat(data['banks'][index]['transaction_fee']) + parseFloat(tax_transaction)) + '">' + data['banks'][index]['name'] + " - " + data['banks'][index]['account_number'] + '</option>';
                        }
                    }
                    $('#bank_id').html(str);
                    str = '<option value="">Branch</option>';
                    for (var index in data['branches']) {
                        str += '<option value="' + index + '">' + data['branches'][index] + '</option>';
                    }
                    $('#branch_id').html(str);
                }
            });
            $.ajax({
                dataType: 'json',
                method: 'post',
                url: ajaxURL + "get-wallet/" + $(this).val(),
                success: function (data) {
                    $('#wallet').html('<label>Wallet Balance: ' + data['wallet'] + '</label>');
                    $('#available_wallet').html('<label>Available Balance: ' + data['available_balance'] + '</label>');
                    $("#walletVal").val(data['wallet']);
                }
            });
        });

        $(document).on('change', '#bank_id,#credit_amount', function () {
            transaction_charge_calculate();
        });

        function transaction_charge_calculate() {
            var amount = 0;
            if ($('#credit_amount').val() != '') {
                amount = parseFloat($('#credit_amount').val());
            }

            var transaction_type = $('#bank_id option[value="' + $('#bank_id').val() + '"]').data('transaction-type');
            var transaction_fee = $('#bank_id option[value="' + $('#bank_id').val() + '"]').data('transaction-amount');
            var transaction_charge = 0;
            if (transaction_type == 1) {
                transaction_charge = amount * transaction_fee / 100;
            } else if (transaction_type == 2) {
                transaction_charge = transaction_fee;
            }
            $('#transaction_charge').val(transaction_charge);
        }

        $(document).on('change', '#payment_type_select', function (e) {
            e.preventDefault();
            payment_type_change();
        });

        function payment_type_change() {
            if ($('#payment_type_select').val() == 1) {
                $('.bank_div').hide();
                $('.branch_div').show();
            } else if ($('#payment_type_select').val() == 2) {
                $('.bank_div').show();
                $('.branch_div').hide();
            } else {
                $('.bank_div').show();
                $('.branch_div').show();
            }
        }

        $(document).on('click', '.rejectCredit', function (e) {
            e.preventDefault();
            $('#credit_status_form')[0].reset();
            $('#credit_status_form').find('[name="id"]').val([$(this).data('id')]);
            $('#credit_status_form').find('[name="status"]').val(4);
            $('#creditStatusModal').find('.statusChange').html('Reject');
            $('#creditStatusModal').modal('show');
        });
        $(document).on('click', '.approveCredit', function (e) {
            e.preventDefault();
            $('#credit_status_form')[0].reset();
            $('#credit_status_form').find('[name="id"]').val([$(this).data('id')]);
            $('#credit_status_form').find('[name="status"]').val(2);
            $('#creditStatusModal').find('.statusChange').html('Approve');
            $('#creditStatusModal').modal('show');
        });
        $(document).on('click', '.inprocessCredit', function (e) {
            e.preventDefault();
            $('#credit_status_form')[0].reset();
            $('#credit_status_form').find('[name="id"]').val([$(this).data('id')]);
            $('#credit_status_form').find('[name="status"]').val(2);
            $('#creditStatusModal').find('.statusChange').html('In Process');
            $('#creditStatusModal').modal('show');
        });

        $(document).on('click', '.completeCredit', function (e) {
            e.preventDefault();
            $('#cashpayoutWalletForm')[0].reset();
            $('#cashpayoutWalletForm').find('[name="id"]').val([$(this).data('id')]);
            $('#cashpayoutWallet').find('input').prop('readonly', false);
            $('#cashpayoutWalletForm').find('.total_payment_amount_error').html('');
            $('#cashpayoutWallet').modal('show');
        });

        $(document).on('click', '.viewWalletDetails', function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'get',
                url: adminSiteURL + 'credits/' + $(this).data('id') + '/wallet',
                success: function (data) {
                    $('#cashpayoutWalletForm')[0].reset();
                    for (var index in data['amount']) {
                        $('#cashpayoutWalletForm').find('[name="payment_amount[' + index + ']"]').val(0 - parseFloat(data['amount'][index]));
                    }

                    for (var index in data['cashback_amount']) {
                        $('#cashpayoutWalletForm').find('[name="cashback_amount[' + index + ']"]').val(parseFloat(data['cashback_amount'][index]));
                    }
                    totalWalletCalculate();
                    $('#cashpayoutWallet').find('input').prop('readonly', true);
                    $('#cashpayoutWallet').modal('show');
                }
            })
        });

        /*$(document).on('click', '.forCheckbox', function (e) {
            e.preventDefault();
            if ($('#checkbox_' + $(this).parent().data('id')).attr('checked') == 'checked') {
                $('#checkbox_' + $(this).parent().data('id')).attr('checked', false);
            } else {
                $('#checkbox_' + $(this).parent().data('id')).attr('checked', true);
            }
        });*/

        $(document).on('click', '#select_all', function () {
            $('.creditCheckbox').prop('checked', true);
        });
        $(document).on('click', '#deselect_all', function () {
            $('.creditCheckbox').prop('checked', false);
        });

        $(document).on('click', '.inprocess_selected', function (e) {
            e.preventDefault();
            $('#credit_status_form')[0].reset();
            var values = [];
            $('.creditCheckbox:checked').each(function (key, item) {
                values.push($(item).val());
            });
            $('#credit_status_form').find('[name="id"]').val(values);
            $('#credit_status_form').find('[name="status"]').val(2);
            $('#creditStatusModal').find('.statusChange').html('In Process');
            $('#creditStatusModal').modal('show');
        });
        $(document).on('click', '.approved_selected', function (e) {
            e.preventDefault();
            $('#credit_status_form')[0].reset();
            var values = [];
            $('.creditCheckbox:checked').each(function (key, item) {
                values.push($(item).val());
            });
            $('#credit_status_form').find('[name="id"]').val(values);
            $('#credit_status_form').find('[name="status"]').val(2);
            $('#creditStatusModal').find('.statusChange').html('Approve');
            $('#creditStatusModal').modal('show');
        });
        $(document).on('click', '.complete_selected', function (e) {
            e.preventDefault();
            $('#credit_status_form')[0].reset();
            var values = [];
            $('.creditCheckbox:checked').each(function (key, item) {
                values.push($(item).val());
            });
            $('#credit_status_form').find('[name="id"]').val(values);
            $('#credit_status_form').find('[name="status"]').val(3);
            $('#creditStatusModal').find('.statusChange').html('Complete');
            $('#creditStatusModal').modal('show');
        });
        $(document).on('click', '.reject_selected', function (e) {
            e.preventDefault();
            $('#credit_status_form')[0].reset();
            var values = [];
            $('.creditCheckbox:checked').each(function (key, item) {
                values.push($(item).val());
            });
            $('#credit_status_form').find('[name="id"]').val(values);
            $('#credit_status_form').find('[name="status"]').val(4);
            $('#creditStatusModal').find('.statusChange').html('Reject');
            $('#creditStatusModal').modal('show');
        });

        $(document).on('submit', '#cashpayoutWalletForm', function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'post',
                url: adminSiteURL + 'credits/status',
                data: $('#cashpayoutWalletForm').serialize(),
                success: function (data) {
                    if (data['status']) {
                        if (data['url'] != undefined && data['url'] != '') {
                            var url = data['url'];
                            window.open(url, '_blank');
                        }
                        // location.reload();
                        oTable.draw();
                        $('#cashpayoutWallet').modal('hide');
                    } else {
                        swal({
                            'text': data['message']
                        });
                    }
                }
            });
        });

        $(document).on('submit', '#credit_status_form', function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'post',
                url: adminSiteURL + 'credits/status',
                data: $('#credit_status_form').serialize(),
                success: function (data) {
                    if (data['url'] != undefined && data['url'] != '') {
                        var url = data['url'];
                        window.open(url, '_blank');
                    }
                    // location.reload();
                    oTable.draw();
                    $('#creditStatusModal').modal('hide');
                }
            });
        });

        $(document).on('click', '.statusHistory', function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'get',
                url: adminSiteURL + 'credits/' + $(this).data('id') + '/history',
                success: function (data) {
                    str = '';
                    for (var index in data['history']) {
                        var history = data['history'][index];
                        if (history['notes'] == null) {
                            history['notes'] = '';
                        }
                        str += '<tr>' +
                            '<td>' + history['user_name'] + '</td>' +
                            '<td>' + history['status'] + '</td>' +
                            '<td>' + history['notes'] + '</td>' +
                            '<td>' + history['date'] + '</td>' +
                            '</tr>';
                    }
                    $('#statusHistoryModal').find('#statusHistory').html(str);
                    $('#statusHistoryModal').modal('show');
                }
            });
        });

        function showMsg() {
            console.log('hello');
        }

        $(document).on('change blur keyup keydown', '#cashpayoutWalletForm input', function (e) {
            totalWalletCalculate();
        });

        function totalWalletCalculate() {
            var total_received = 0;
            $('#cashpayoutWalletForm').find('[name^="payment_amount"]').each(function (key, item) {
                if ($(item).val() != '') {
                    total_received += parseFloat($(item).val());
                }
            });
            $('#cashpayoutWalletForm').find("[name='transaction_total[received]']").val(total_received);
            var total_cashback = 0;
            $('#cashpayoutWalletForm').find('[name^="cashback_amount"]').each(function (key, item) {
                if ($(item).val() != '') {
                    total_cashback += parseFloat($(item).val());
                }
            });
            $('#cashpayoutWalletForm').find('[name="transaction_total[cash_back]"]').val(total_cashback);
            $('#cashpayoutWalletForm').find('[name="transaction_total[payment]"]').val(total_received - total_cashback);
        }
    </script>
@endsection
