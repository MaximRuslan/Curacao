@extends('layouts.app')

@section('extra-styles')
    <link href="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.bootstrap4.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{url(config('theme.admin.plugins'))}}/datatables/buttons.bootstrap4.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{url(config('theme.admin.plugins'))}}/datatables/responsive.bootstrap4.min.css" rel="stylesheet"
          type="text/css"/>
    <style>
        .card-box {
            display: flex;
            align-items: center;
            height: 100%;
        }

        .card-box i {
            margin-right: 5px;
        }

        /*.dropbtn {
          background-color: #007bff;
          border-color: #007bff;
            padding: 16px;
            font-size: 16px;
            border: none;
            cursor: pointer;
        }*/

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }

        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .dropdown:hover .dropbtn {
            background-color: #007bff;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-container .select2-selection--single {
            border: 1px solid #E3E3E3 !important;
            height: 38px !important;
        }

        .select2-container .select2-selection--single .select2-selection__rendered {
            line-height: 36px !important;
            padding-left: 12px !important;
        }

        .select2-container .select2-selection--single .select2-selection__arrow {
            height: 34px;
            width: 34px;
            right: 3px;
        }

        .select2-container .select2-selection--single .select2-selection__arrow b {
            border-color: #999 transparent transparent transparent;
            border-width: 6px 6px 0 6px;
        }

        .select2-container--open .select2-selection--single .select2-selection__arrow b {
            border-color: transparent transparent #999 transparent !important;
            border-width: 0 6px 6px 6px !important;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #62a3ff;
        }

        .select2-results__option {
            padding: 6px 12px;
        }

        .select2-dropdown {
            border: 1px solid #e3e3e3 !important;
            padding-top: 5px;
            box-shadow: 0 2px 2px rgba(0, 0, 0, 0.15);
        }

        .select2-search input {
            border: 1px solid #e3e3e3 !important;
        }

        .select2-container .select2-selection--multiple {
            min-height: 38px !important;
            border: 1px solid #e3e3e3 !important;
        }

        .select2-container .select2-selection--multiple .select2-selection__rendered {
            padding: 2px 10px;
        }

        .select2-container .select2-selection--multiple .select2-search__field {
            margin-top: 7px;
            border: 0 !important;
        }

        .select2-container .select2-selection--multiple .select2-selection__choice {
            background-color: #f5f5f5;
            border: 1px solid #e3e3e3;
            border-radius: 1px;
            padding: 0 7px;
        }

    </style>
    <link href="{{url(config('theme.admin.plugins'))}}/select2/css/select2.min.css" rel="stylesheet"
          type="text/css"/>
@endsection

@section('content')
    <section class="section" id="login">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="title text-center">
                                @lang('keywords.Ledger') @lang('keywords.Balance'): {!! number_format($wallet,2) !!}
                            </h3>
                        </div>
                        <div class="col-md-6">
                            <h3 class="title text-center">
                                @lang('keywords.Available') @lang('keywords.Balance')
                                : {!! number_format($available_balance,2) !!}
                            </h3>
                        </div>
                    </div>
                    @if($wallet > 0)

                        <div class="dropdown" style="float:right;">
                            <button class="dropbtn btn btn-primary m-b-20">@lang('keywords.Use Credit')</button>
                            <div class="dropdown-content">
                                <a class="creditAddModalOpen" data-title="@lang('keywords.Cash') @lang('keywords.Payout') @lang('keywords.Request')"
                                   data-payment-type="1" href="#">Cash payout</a>
                                <a class="creditAddModalOpen" data-title="@lang('keywords.BankTransfer')"
                                   data-payment-type="2" href="#">Deposit on bank
                                    account</a>
                                <a data-toggle="modal" href="#merchantModal">Payment merchant</a>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="col-md-12">
                    <div class="card-box table-responsive">
                        <table id="datatable" class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th style="width: 10%;">@lang('keywords.PaymentType')</th>
                                <th style="width: 10%;">@lang('keywords.Amount')</th>
                                <th>@lang('keywords.Bank')</th>
                                <th>@lang('keywords.Bank')</th>
                                <th>@lang('keywords.TransactionCharge')</th>
                                <th style="width: 10%;">@lang('keywords.Info')</th>
                                <th style="width: 10%;">@lang('keywords.Notes')</th>
                                <th style="width: 10%;">@lang('keywords.Status')</th>
                                <th style="width: 10%;">@lang('keywords.Date')</th>
                                <th style="width: 10%;">@lang('keywords.Action')</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('common.delete_confirm',[
        'modalId'=>'deleteCredit',
        'action'=>route('client.credits.destroy','deleteId'),
        'item'=>'it',
        'callback'=>'showMsg'
        ])

    @include('common.credit.cash')
    @include('common.credit.merchant')
    @include('common.credit.create')
@endsection

@section('extra-js')
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/jquery.dataTables.min.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.bootstrap4.min.js"></script>

    <script src="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.responsive.min.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/responsive.bootstrap4.min.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/select2/js/select2.full.js"></script>
    {{--<script src="{{url(config('theme.common.js'))}}/loan.js"></script>--}}
@endsection

@section('custom-js')
    @hasanyrole('client')
    <script>
        setTimeout(function () {
            $('.apply_credit').attr('disabled', true);
        }, 1000);
        $(document).on('change', '#loan_model_terms_checkbox', function () {
            if ($('#loan_model_terms_checkbox:checked').val() == 1) {
                $('.apply_credit').removeAttr('disabled');
            } else {
                $('.apply_credit').attr('disabled', true);
            }
        });
    </script>
    @endhasanyrole
    <script type="text/javascript">
        var oTable = "";

        $(document).ready(function () {
            oTable = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{route('client.credits.data')}}",
                    "type": "POST"
                },
                order: [[0, 'desc']],
                "drawCallback": function (settings) {
                    InitTooltip();
                    $('[data-toggle="tooltip"]').tooltip();
                },
                columns: [
                    {data: 'updated_at', searchable: false, visible: false},
                    {data: 'payment_type', searchable: false, orderable: false},
                    {data: 'amount', name: 'amount'},
                    {data: 'bank_name', name: 'banks.name', visible: false},
                    {data: 'branch_name', name: 'branches.title', visible: false},
                    {data: 'transaction_charge', name: 'transaction_charge', visible: false},
                    {data: 'info', name: 'transaction_charge', searchable: false, orderable: false},
                    {data: 'notes', name: 'notes'},
                    {data: 'status', name: 'notes', orderable: false, searchable: false},
                    {data: 'created_at', name: 'created_at', searchable: false},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
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
                }
            });
        });
        $('#users_select,#payment_type_select,#bank_id,#branch_id').select2();
        $('#creditModal').on('hidden.bs.modal', function () {
            $('#creditModal').find('form')[0].reset();
            $('#creditModal').find('form').find('input[name="id"]').val('');
            $('#creditModal').find('input').removeAttr('disabled');
            $('#creditModal').find('button[type="submit"]').show();
            $('#users_select,#payment_type_select,#bank_id,#branch_id').val('').select2();
            $(".help-block").html("");
        })

        function setEdit(id, type) {
            if (type == undefined) {
                type = '';
            }
            var action = '{{route('client.credits.show','')}}/' + id
            $.ajax({
                type: 'GET',
                url: action,
                data: {},
                dataType: 'json',
                success: function (data) {
                    setFormValues('credit_form', data.inputs);
                    $('#walletDateTime').html('Date: ' + data.inputs.created_at.value);
                    payment_type_change()
                    $('#creditModal').modal('show');
                    $(".terms_condition").show();
                    payment_type_change();
                    if (type == 'view') {
                        $(".terms_condition").hide();
                        setTimeout(function () {
                            $('#creditModal').find('input,select,textarea').attr('disabled', 'disabled');
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
            var action = $(form).attr('action')
            $.ajax({
                type: 'POST',
                url: action,
                data: $(form).serialize(),
                dataType: 'json',
                success: function (data) {
                    $('#creditModal').modal('hide');
                    oTable.draw(true);
                    $(".help-block").html("");
                    location.reload();
                },
                error: function (jqXHR, exception) {
                    var Response = jqXHR.responseText;
                    ErrorBlock = $(form);
                    Response = $.parseJSON(Response);
                    DisplayErrorMessages(Response, ErrorBlock, 'input');
                }
            });
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
                        if ($('#bank_id').val() != "") {
                            str += '<option value="' + data['banks'][index]['id'] + '" data-transaction-type="' + data['banks'][index]['transaction_fee_type'] + '" data-transaction-amount="' + data['banks'][index]['transaction_fee'] + '"';
                            if (data['banks'][index]['id'] == $('#bank_id').val()) {
                                str += ' selected';
                            }
                            str += '>' + data['banks'][index]['name'] + "-" + data['banks'][index]['account_number'] + '</option>';
                        } else {
                            str += '<option value="' + data['banks'][index]['id'] + '" data-transaction-type="' + data['banks'][index]['transaction_fee_type'] + '" data-transaction-amount="' + data['banks'][index]['transaction_fee'] + '">' + data['banks'][index]['name'] + "-" + data['banks'][index]['account_number'] + '</option>';
                        }
                    }
                    $('#bank_id').html(str);
                }
            });
        });

        $(document).on('change', '#payment_type_select', function (e) {
            e.preventDefault();
            payment_type_change();
        });

        function payment_type_change() {
            if ($('[name="payment_type"]').val() == 1) {
                $('.bank_div').hide();
                $('.branch_div').show();
            } else if ($('[name="payment_type"]').val() == 2) {
                $('.bank_div').show();
                $('.branch_div').hide();
            } else {
                $('.bank_div').show();
                $('.branch_div').show();
            }
        }

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

        function showMsg() {
            console.log('hello');
        }

        $(document).on('click', '.creditAddModalOpen', function () {
            $('#walletDateTime').html('');
            $('#creditModal').find('.modal-title').html($(this).data('title'));
            $('#creditModal').find('[name="payment_type"]').val($(this).data('payment-type'));
            payment_type_change();
            $('#creditModal').modal('show');
        });
    </script>
    <script>
        $(document).ready(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endsection
