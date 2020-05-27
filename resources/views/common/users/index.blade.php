@extends('admin.layouts.app')
@section('page_name')
    Users
@stop
@section('extra-styles')

    <link href="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.bootstrap4.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{url(config('theme.admin.plugins'))}}/datatables/buttons.bootstrap4.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{url(config('theme.admin.plugins'))}}/datatables/responsive.bootstrap4.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{url(config('theme.admin.plugins'))}}/jquery.steps/css/jquery.steps.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{url(config('theme.front.plugins'))}}/bootstrap-datepicker/css/bootstrap-datepicker.min.css"
          rel="stylesheet">
    <link href="{{url(config('theme.front.plugins'))}}/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
    <style>
        .table-user tbody td {
            border-color: black;
        }

        .table-user thead th {
            border-color: black;
            border-bottom: 1px solid black !important;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            @if(auth()->user()->hasRole('super admin|admin|processor'))
                <div class="btn-group pull-right m-b-20">
                    <a href="{!! url()->route('users.create') !!}" class="btn btn-default waves-effect waves-light">
                        Add
                    </a>
                </div>
            @endif
            <h4 class="page-title">Users</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <table id="user-table" class="table  table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Last Name</th>
                        <th>Name</th>
                        <th>ID</th>
                        <th>Country</th>
                        <th>Status</th>
                        <th>Role</th>
                        <th>Is Verified</th>
                        <th>Wallet</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    @include('common.delete_confirm',[
        'modalId'=>'deleteUser',
        'action'=>route('users.destroy','deleteId'),
        'item'=>'it',
        'callback'=>'showMsg'
        ])
    @include('common.users.user_wallet_add')
@endsection

@section('extra-js')
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/jquery.dataTables.min.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.responsive.min.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/responsive.bootstrap4.min.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/jquery-validation/js/jquery.validate.min.js"></script>
    <script src="{{url(config('theme.front.plugins'))}}/moment/moment.js"></script>
    <script src="{{url(config('theme.front.plugins'))}}/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
    <script src="{{url(config('theme.front.plugins'))}}/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/jquery.steps/js/jquery.steps.min.js"></script>
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
            oTable = $('#user-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{route('users-data')}}",
                    "type": "POST",
                    data: function (d) {
                        d.user_id = "{!! request('user_id') !!}";
                    }
                },
                order: [],
                "drawCallback": function (settings) {
                    InitTooltip();
                },
                columns: [
                    {data: 'id', name: 'id', visible: false},
                    {data: 'lastname', name: 'users.lastname', visible: false},
                    {data: 'username', name: 'users.firstname'},
                    {data: 'id_number', name: 'users.id_number'},
                    {data: 'country_name', name: 'countries.name'},
                    {data: 'status_name', name: 'user_status.title'},
                    {data: 'role.name', name: 'role.name', 'orderable': false,},
                    {data: 'is_verified', name: 'users.is_verified', 'orderable': false, searchable: false},
                    {data: 'wallet', name: 'wallet', 'orderable': false, searchable: false},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                order: [[0, 'desc']],
                pageLength: '50'
            });

            $('#userModal').on('hidden.bs.modal', function () {
                $('#userModal').find('form')[0].reset();
                $('#userModal').find('form').find('input[name="id"]').val('');
                $('#userModal').find('input, select').removeAttr('disabled');
                $('#userModal').find('button[type="submit"]').show();
                $('#userModal').find('#deleteImage').show();
                $('#userModal').find('.profile-pic-holder').hide();
                $('#userModal').find('.profile-pic-holder').find('img').attr('src', '');
            })
        });

        var wTable = '';

        function walletTableReinit() {
            if (wTable == '') {
                wTable = $('#wallet-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        "url": ajaxURL + 'wallets-data',
                        "type": "POST",
                        data: function (d) {
                            d.user_id = $('#userWalletForm').data('user-id');
                        }
                    },
                    order: [],
                    "drawCallback": function (settings) {
                        InitTooltip();
                    },
                    columns: [
                        {data: 'type', name: 'type'},
                        {data: 'amount', name: 'amount'},
                        {data: 'notes', name: 'notes'},
                        {data: 'transaction_payment_date', name: 'transaction_payment_date', searchable: false},
                        {data: 'created_at', name: 'created_at', 'searchable': false},
                    ],
                    pageLength: 25,
                });
            } else {
                wTable.draw();
            }
        }

        function showMsg() {
            console.log('hello');
        }

        function toggleStatus(element) {
            var role = $(element).val();
            if (role == 3) {
                $('.status-group').show();
            } else {
                $('.status-group').hide();
            }
        }

        $(document).on('click', '.AddAmount', function (e) {
            e.preventDefault();
            $('#userWalletForm').data('user-id', $(this).data('id'));
            $('#userWalletForm')[0].reset();
            walletTableReinit();
            $('#userWallerModal').find('#client_name').html($(this).data('name'));
            $('#userWallerModal').find('#client_id').html($(this).data('client_id'));
            if ($(this).data('balance') == '' || $(this).data('balance') == null) {
                $('#userWallerModal').find('#balance').html('0.00');
            } else {
                $('#userWallerModal').find('#balance').html($(this).data('balance'));
            }
            $('#userWallerModal').modal('show');
        });
        $(document).on('submit', '#userWalletForm', function (e) {
            e.preventDefault();
            var form = '#userWalletForm';
            if ($('#userWalletForm').find('[name="amount"]').val() != '') {
                $.ajax({
                    dataType: 'json',
                    method: 'post',
                    data: $('#userWalletForm').serialize(),
                    url: ajaxURL + 'users/' + $('#userWalletForm').data('user-id') + '/wallet',
                    success: function (data) {
                        wTable.draw(false);
                        oTable.draw(false);
                        var user_id = $('#userWalletForm').data('user-id');
                        $('#userWalletForm')[0].reset();
                        $('#userWalletForm').data('user-id', user_id);
                    },
                    error: function (jqXHR, exception) {
                        var Response = jqXHR.responseText;
                        ErrorBlock = $(form);
                        Response = $.parseJSON(Response);
                        DisplayErrorMessages(Response, ErrorBlock, 'input');
                    }
                });
            }
        });

        $(document).on('change blur keyup keydown', '#userWalletForm input', function (e) {
            totalWalletCalculate();
        });

        function totalWalletCalculate() {
            var total_received = 0;
            $('#userWalletForm').find('[name^="amount"]').each(function (key, item) {
                if ($(item).val() != '') {
                    total_received += parseFloat($(item).val());
                }
            });
            $('#userWalletForm').find("[name='transaction_total[received]']").val(total_received);
            var total_cashback = 0;
            $('#userWalletForm').find('[name^="cashback_amount"]').each(function (key, item) {
                if ($(item).val() != '') {
                    total_cashback += parseFloat($(item).val());
                }
            });
            $('#userWalletForm').find('[name="transaction_total[cash_back]"]').val(total_cashback);
            $('#userWalletForm').find('[name="transaction_total[payment]"]').val(total_received - total_cashback);
        }
    </script>
    @if(request('user_id'))
        <script>
            var user_id = "{!! request('user_id') !!}";
            setEdit(user_id, 'view');
        </script>
    @endif
@endsection
