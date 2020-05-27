@extends('admin.layouts.app')
@section('page_name')
    Bank Reconciliation
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
        <div class="col-sm-12  m-b-20">
            <h4 class="page-title">Bank Reconciliation</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-3"></div>
                <div class="col-md-3"></div>
                <div class="col-md-3">
                    {!!Form::select('reconcile_type', ['1'=>'All transactions','2'=>'Only reconciled transactions','3'=>'Non-reconciled transactions'],1,['class'=>'form-control','id'=>'reconcile_type'])!!}
                </div>
            </div>
            <div class="card-box table-responsive mt-3">
                <table id="bank-table" class="table  table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th style="width: 5%;"></th>
                        <th>Date</th>
                        <th>Client full name</th>
                        <th>Client full name</th>
                        <th>Loan ID</th>
                        <th>Amount</th>
                        <th>Credit/Debit</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                </table>
                <div class="row">
                    <div class="col-md-6">
                        <div class="text-left">
                            <button class="btn btn-primary" id="select_all">Select All</button>
                            <button class="btn btn-danger" id="deselect_all">Deselect All</button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-right">
                            <button class="btn btn-primary reconcile_selected">Reconcile Selected</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.bank.reconcile_confirm')
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
        $('#reconcile_type').select2();
        $(document).ready(function () {
            oTable = $('#bank-table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 50,
                ajax: {
                    "url": adminSiteURL + 'bank-reconciliation-data',
                    data: function (d) {
                        d.reconcile_type = $('#reconcile_type').val();
                    }
                },
                columns: [
                    {data: 'reconcile_select', searchable: false, orderable: false},
                    {data: 'date', name: 'date'},
                    {data: 'fullname', name: 'users.firstname',searchable:false},
                    {data: 'fullname', name: 'users.lastname',searchable:false, orderable: false,visible:false},
                    {data: 'loan_id', name: 'loan_id', searchable: false},
                    {data: 'amount', name: 'amount'},
                    {data: 'type', name: 'loan_id', searchable: false, orderable: false},
                    {data: 'status', name: 'status', searchable: false, orderable: false},
                    {data: 'action', name: 'status', searchable: false, orderable: false},
                ],
                "drawCallback": function (settings) {
                    InitTooltip();
                },
                order: [[1, 'desc']]
            });
        });
        $(document).on('change', '#reconcile_type', function (e) {
            e.preventDefault();
            oTable.draw();
            if($(this).val()==2){
                $('#select_all').hide();
                $('#deselect_all').hide();
                $('.reconcile_selected').hide();
            }else{
                $('#select_all').show();
                $('#deselect_all').show();
                $('.reconcile_selected').show();
            }
        });
        $(document).on('click', '#select_all', function () {
            $('.reconcileCheckbox').prop('checked', true);
        });
        $(document).on('click', '#deselect_all', function () {
            $('.reconcileCheckbox').prop('checked', false);
        });

        $(document).on('click', '.reconcileBank', function (e) {
            e.preventDefault();
            $('#reconcile_form').find('[name="id"]').val([$(this).data('id')]);
            $('#reconcile_form').find('[name="type"]').val([$(this).data('type')]);
            $('#reconcileModal').modal('show');
        });

        $(document).on('click', '.reconcile_selected', function (e) {
            e.preventDefault();
            var values = [];
            var types = [];
            $('.reconcileCheckbox:checked').each(function (key, item) {
                values.push($(item).val());
                types.push($(item).data('type'));
            });
            $('#reconcile_form').find('[name="id"]').val(values);
            $('#reconcile_form').find('[name="type"]').val(types);
            $('#reconcileModal').modal('show');
        });

        $(document).on('submit', '#reconcile_form', function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'post',
                url: adminSiteURL + 'bank-reconcile',
                data: $('#reconcile_form').serialize(),
                success: function (data) {
                    oTable.draw(false);
                    $('#reconcileModal').modal('hide');
                }
            });
        });
    </script>
@endsection
