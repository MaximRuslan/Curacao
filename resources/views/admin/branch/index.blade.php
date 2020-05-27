@extends('admin.layouts.app')
@section('page_name')
    Branches
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
                <a data-toggle="modal" href="#branchModal"
                   class="btn btn-default waves-effect waves-light">Add</a>
            </div>
            <h4 class="page-title">Branch</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <table id="branch-table" class="table  table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Country</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('common.delete_confirm',[
        'modalId'=>'deleteBranch',
        'action'=>route('branch.destroy','deleteId'),
        'item'=>'branch',
        'callback'=>'showMsg'
        ])

    @include('admin.branch.create')
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
            oTable = $('#branch-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{route('branch-data')}}",
                    "type": "POST"
                },
                order: [],
                "drawCallback": function (settings) {
                    InitTooltip();
                },
                columns: [
                    {data: 'id', name: 'id', visible: false},
                    {data: 'title', name: 'title'},
                    // {data: 'title_es', name: 'title_es'},
                    // {data: 'title_nl', name: 'title_nl'},
                    {data: 'country', name: 'countries.name'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                order: [[0, 'desc']]
            });
            $('#branchModal').on('hidden.bs.modal', function () {
                $('#branchModal').find('form')[0].reset();
                $('#branchModal').find('form').find('input[name="id"]').val('');
                $('#branchModal').find('input').removeAttr('disabled');
                $('#branchModal').find('button[type="submit"]').show();
                $('.form-group').removeClass('has-error');
                $('.help-block').html('');
            })
        });

        function setEdit(id, type) {
            if (type == undefined) {
                type = '';
            }
            var action = '{{route('branch.show','')}}/' + id
            $.ajax({
                type: 'GET',
                url: action,
                data: {},
                dataType: 'json',
                success: function (data) {
                    setFormValues('branch_form', data.inputs);
                    $('#branchModal').modal('show');
                    if (type == 'view') {
                        setTimeout(function () {
                            $('#branchModal').find('input').attr('disabled', 'disabled');
                            $('#branchModal').find('button[type="submit"]').hide();
                        }, 100);
                    }
                },
                error: function (jqXHR, exception) {
                }
            });
        }

        function SaveBranch(form) {
            var action = $(form).attr('action')
            $.ajax({
                type: 'POST',
                url: action,
                data: $(form).serialize(),
                dataType: 'json',
                success: function (data) {
                    successMsg('Message');
                    $('#branchModal').modal('hide');
                    oTable.draw(true);
                    $('.form-group').removeClass('has-error');
                    $('.help-block').html('');
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
