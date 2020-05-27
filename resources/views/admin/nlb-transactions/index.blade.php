@extends('admin.layouts.app')
@section('page_name')
    NLB Transactions
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
@endsection


@section('content')
    <div class="row">
        <div class="col-sm-12">
            @if(auth()->user()->hasRole('processor'))
                <div class="btn-group pull-right m-b-20">
                    <a data-toggle="modal" href="#NLBTransactionsModel"
                       class="btn btn-default waves-effect waves-light">
                        Add
                    </a>
                </div>
            @endif
            <h4 class="page-title">NLB Transactions</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            @if(auth()->user()->hasRole('super admin'))
                <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-md-3"></div>
                    <div class="col-md-3"></div>
                    <div class="col-md-3">
                        {!!Form::label('branch_id','Branch')!!}
                        {!!Form::select('branch_id',$branches,old('branch_id'),['class'=>'form-control select2','placeholder'=>'All','id'=>'branch_id'])!!}
                    </div>
                </div>
            @endif
            <div class="card-box table-responsive">
                <table id="datatable" class="table  table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Branch</th>
                        <th>User</th>
                        <th>User</th>
                        <th>Type</th>
                        <th>Reason</th>
                        <th>Amount</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('common.delete_confirm',[
        'modalId'=>'deleteNlb',
        'action'=>route('nlb-transactions.destroy','deleteId'),
        'item'=>'it',
        'callback'=>'showMsg'
        ])

    @include('admin.nlb-transactions.create')
@endsection
@section('extra-js')
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/jquery.dataTables.min.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.bootstrap4.min.js"></script>

    <script src="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.responsive.min.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/responsive.bootstrap4.min.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/select2/js/select2.full.js"></script>
@endsection

@section('custom-js')
    <script type="text/javascript">
        var oTable = "";
        $('.select2').select2();
        $(document).ready(function () {
            oTable = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{route('nlb-transactions-data')}}",
                    "type": "POST",
                    "data": function (d) {
                        d.branch_id = $('#branch_id').val();
                    }
                },
                order: [],
                "drawCallback": function (settings) {
                    InitTooltip();
                },
                columns: [
                    {data: 'id', name: 'nlbs.id', visible: false},
                    {data: 'branch', name: 'branches.title'},
                    {data: 'user_name', name: 'users.firstname'},
                    {data: 'user_name', name: 'users.lastname', visible: false},
                    {data: 'type', name: 'nlbs.type'},
                    {data: 'reason_name', name: 'n_l_b_reasons.title'},
                    {data: 'amount', orderable: false, searchable: false},
                    {data: 'desc', name: 'nlb.desc'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                order: [[0, 'desc']]
            });
        });
        $('#NLBTransactionsModel').on('hidden.bs.modal', function () {
            $('#NLBTransactionsModel').find('form')[0].reset();
            $('#NLBTransactionsModel').find('form').find('input[name="id"]').val('');
            $('#NLBTransactionsModel').find('input,select').removeAttr('disabled');
            $('#NLBTransactionsModel').find('.select2').val('').select2();
            $('#NLBTransactionsModel').find('button[type="submit"]').show();
        });

        $(document).on('change blur keyup', '.amount_change', function (e) {
            e.preventDefault();
            totalCalculate();
        });

        $(document).on('change', '#branch_id', function (e) {
            e.preventDefault();
            oTable.draw();
        });

        function totalCalculate() {
            var sum = 0;
            $('.amount_change').each(function (key, item) {
                if ($(item).val() != '') {
                    sum += parseFloat($(item).val());
                }
            });
            $('#total_amount').val(sum);
        }

        function getReasons(type, value, view_type) {
            if (type != '') {
                $.ajax({
                    dataType: 'json',
                    method: 'get',
                    url: adminSiteURL + 'nlb-reasons/' + type + '/types',
                    success: function (data) {
                        var str = '';
                        for (var index in data['reasons']) {
                            if (value != undefined && value == index) {
                                str += '<option selected value="' + index + '">' + data['reasons'][index] + '</option>';
                            } else {
                                str += '<option value="' + index + '">' + data['reasons'][index] + '</option>';
                            }
                        }
                        $('#reason_selection').html(str);
                        if (view_type != undefined && view_type == 'view') {
                            setTimeout(function () {
                                $('#NLBTransactionsModel').find('#type').prop('disabled', true);
                                $('#NLBTransactionsModel').find('#reason_selection').prop('disabled', true);
                                $('#NLBTransactionsModel').find('textarea').attr('disabled', 'disabled');
                            }, 100);
                        }
                    }
                });
            } else {
                var str = '';
                $('#reason_selection').html(str);
                if (view_type != undefined && view_type == 'view') {
                    $('#NLBTransactionsModel').find('#type').prop('disabled', true);
                    $('#NLBTransactionsModel').find('#reason_selection').prop('disabled', true);
                    $('#NLBTransactionsModel').find('textarea').attr('disabled', 'disabled');
                }
            }
        }

        $(document).on('change', '#type', function (e) {
            e.preventDefault();
            getReasons($(this).val());
        });

        function setEdit(id, type) {
            if (type == undefined) {
                type = '';
            }
            var action = '{{route('nlb-transactions.show','')}}/' + id
            $.ajax({
                type: 'GET',
                url: action,
                data: {},
                dataType: 'json',
                success: function (data) {
                    setFormValues('transaction_form', data.inputs);
                    if (type == 'view') {
                        getReasons(data.inputs.type.value, data.reason, 'view');
                    } else {
                        getReasons(data.inputs.type.value, data.reason);
                    }
                    for (var index in data['amounts']) {
                        $('[name="amount[' + index + ']"]').val(data['amounts'][index]);
                    }
                    totalCalculate();
                    $('#NLBTransactionsModel').modal('show');
                    if (type == 'view') {
                        setTimeout(function () {
                            $('#NLBTransactionsModel').find('input').attr('disabled', 'disabled');
                            $('#NLBTransactionsModel').find('button[type="submit"]').hide();
                        }, 100);
                    }
                },
                error: function (jqXHR, exception) {
                }
            });
        }

        function SaveNLB(form) {
            var action = $(form).attr('action')
            $.ajax({
                type: 'POST',
                url: action,
                data: $(form).serialize(),
                dataType: 'json',
                success: function (data) {
                    successMsg('Message');
                    $('#NLBTransactionsModel').modal('hide');
                    oTable.draw(true);
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

        function showMsg() {
            console.log('hello');
        }
    </script>
@endsection
