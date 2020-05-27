@extends('admin.layouts.app')
@section('page_name')
    Loan Reasons
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
                <a data-toggle="modal" href="#loanReasonsModal" class="btn btn-default waves-effect waves-light">Add</a>
            </div>
            <h4 class="page-title">Loan reasons</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <table id="reason-table" class="table  table-striped table-bordered" style="width:100%">
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
        'modalId'=>'deleteReason',
        'action'=>route('loan-reasons.destroy','deleteId'),
        'item'=>'it',
        'callback'=>'showMsg'
        ])

    @include('admin.loanreasons.create')
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
            oTable = $('#reason-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{route('loan-reasons-data')}}",
                    "type": "POST"
                },
                order: [],
                "drawCallback": function (settings) {
                    InitTooltip();
                },
                columns: [
                    {data: 'id', name: 'id', visible: false},
                    {data: 'title', name: 'title'},
                    {data: 'title_es', name: 'title_es'},
                    {data: 'title_nl', name: 'title_nl'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                order: [[0, 'desc']]
            });
            $('#loanReasonsModal').on('hidden.bs.modal', function () {
                $('#loanReasonsModal').find('form')[0].reset();
                $('#loanReasonsModal').find('form').find('input[name="id"]').val('');
                $('#loanReasonsModal').find('input').removeAttr('disabled');
                $('#loanReasonsModal').find('button[type="submit"]').show();
            })
        });

        function setEdit(id, type = '') {
            var action = '{{route('loan-reasons.show','')}}/' + id
            $.ajax({
                type: 'GET',
                url: action,
                data: {},
                dataType: 'json',
                success: function (data) {
                    setFormValues('loanReason_form', data.inputs);
                    $('#loanReasonsModal').modal('show');
                    if (type == 'view') {
                        setTimeout(function () {
                            $('#loanReasonsModal').find('input').attr('disabled', 'disabled');
                            $('#loanReasonsModal').find('button[type="submit"]').hide();
                        }, 100);
                    }
                },
                error: function (jqXHR, exception) {
                }
            });
        }

        function SaveLoanreason(form) {
            var action = $(form).attr('action')
            $.ajax({
                type: 'POST',
                url: action,
                data: $(form).serialize(),
                dataType: 'json',
                success: function (data) {
                    successMsg('Message');
                    $('#loanReasonsModal').modal('hide');
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
