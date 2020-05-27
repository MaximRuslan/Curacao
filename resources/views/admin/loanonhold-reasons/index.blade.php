@extends('admin.layouts.app')
@section('page_name')
    Loan onhold Reasons
@stop
@section('extra-styles')

    <link href="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.bootstrap4.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{url(config('theme.admin.plugins'))}}/datatables/buttons.bootstrap4.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{url(config('theme.admin.plugins'))}}/datatables/responsive.bootstrap4.min.css" rel="stylesheet"
          type="text/css"/>
@endsection


@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="btn-group pull-right m-b-20">
                <a data-toggle="modal" href="#Loanonhold_reason_Modal" class="btn btn-default waves-effect waves-light">Add</a>
            </div>
            <h4 class="page-title">Loan on hold reasons</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <table id="loan-decline-table" class="table  table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title ENG</th>
                        <th>Title ESP</th>
                        <th>Title PAP</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('common.delete_confirm',[
        'modalId'=>'deleteLoanOnHoldReason',
        'action'=>route('loan-onhold-reasons.destroy','deleteId'),
        'item'=>'it',
        'callback'=>'showMsg'
        ])

    @include('admin.loanonhold-reasons.create')
@endsection
@section('extra-js')
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/jquery.dataTables.min.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.bootstrap4.min.js"></script>

    <script src="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.responsive.min.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/responsive.bootstrap4.min.js"></script>
@endsection

@section('custom-js')
    <script type="text/javascript">
        var oTable = "";
        $(document).ready(function () {
            oTable = $('#loan-decline-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{route('loan-onhold-data')}}",
                    "type": "POST"
                },
                order: [],
                "drawCallback": function (settings) {
                    InitTooltip();
                },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'title', name: 'title'},
                    {data: 'title_es', name: 'title_es'},
                    {data: 'title_nl', name: 'title_nl'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                order: [[0, 'desc']]
            });
            $('#Loanonhold_reason_Modal').on('hidden.bs.modal', function () {
                $('#Loanonhold_reason_Modal').find('form')[0].reset();
                $('#Loanonhold_reason_Modal').find('form').find('input[name="id"]').val('');
                $('#Loanonhold_reason_Modal').find('input').removeAttr('disabled');
                $('#Loanonhold_reason_Modal').find('button[type="submit"]').show();
            })
        });

        function setEdit(id, type = '') {
            var action = '{{route('loan-onhold-reasons.show','')}}/' + id
            $.ajax({
                type: 'GET',
                url: action,
                data: {},
                dataType: 'json',
                success: function (data) {
                    setFormValues('loan_onhold_reasons_form', data.inputs);
                    $('#Loanonhold_reason_Modal').modal('show');
                    if (type == 'view') {
                        setTimeout(function () {
                            $('#Loanonhold_reason_Modal').find('input').attr('disabled', 'disabled');
                            $('#Loanonhold_reason_Modal').find('button[type="submit"]').hide();
                        }, 100);
                    }
                },
                error: function (jqXHR, exception) {
                }
            });
        }

        function SaveLoanDeclineReason(form) {
            var action = $(form).attr('action')
            $.ajax({
                type: 'POST',
                url: action,
                data: $(form).serialize(),
                dataType: 'json',
                success: function (data) {
                    successMsg('Message');
                    $('#Loanonhold_reason_Modal').modal('hide');
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
