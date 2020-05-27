@extends('admin.layouts.app')
@section('page_name')
    NLB Reasons
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
            <div class="btn-group pull-right m-b-20">
                <a data-toggle="modal" href="#NLBReasonsModel" class="btn btn-default waves-effect waves-light">Add</a>
            </div>
            <h4 class="page-title">NLB Reasons</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <table id="datatable" class="table  table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Type</th>
                        <th>Title</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('common.delete_confirm',[
        'modalId'=>'deleteNLBReason',
        'action'=>route('nlb-reasons.destroy','deleteId'),
        'item'=>'it',
        'callback'=>'showMsg'
        ])

    @include('admin.nlb-reasons.create')
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
                    "url": "{{route('nlb-reasons-data')}}",
                    "type": "POST"
                },
                order: [],
                "drawCallback": function (settings) {
                    InitTooltip();
                },
                columns: [
                    {data: 'id', name: 'id', visible: false},
                    {data: 'type', name: 'type'},
                    {data: 'title', name: 'name'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                order: [[0, 'desc']]
            });
            $('#NLBReasonsModel').on('hidden.bs.modal', function () {
                $('#NLBReasonsModel').find('form')[0].reset();
                $('#NLBReasonsModel').find('form').find('input[name="id"]').val('');
                $('#NLBReasonsModel').find('input,select').removeAttr('disabled');
                $('#NLBReasonsModel').find('.select2').val('').select2();
                $('#NLBReasonsModel').find('button[type="submit"]').show();
            })
        });

        function setEdit(id, type) {
            if (type == undefined) {
                type = '';
            }
            var action = '{{route('nlb-reasons.show','')}}/' + id
            $.ajax({
                type: 'GET',
                url: action,
                data: {},
                dataType: 'json',
                success: function (data) {
                    setFormValues('reason_form', data.inputs);
                    $('#NLBReasonsModel').modal('show');
                    if (type == 'view') {
                        setTimeout(function () {
                            $('#NLBReasonsModel').find('input').attr('disabled', 'disabled');
                            $('#NLBReasonsModel').find('#type').prop('disabled', true);
                            $('#NLBReasonsModel').find('button[type="submit"]').hide();
                        }, 100);
                    }
                },
                error: function (jqXHR, exception) {
                }
            });
        }

        function SaveNLBReason(form) {
            var action = $(form).attr('action')
            $.ajax({
                type: 'POST',
                url: action,
                data: $(form).serialize(),
                dataType: 'json',
                success: function (data) {
                    successMsg('Message');
                    $('#NLBReasonsModel').modal('hide');
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
