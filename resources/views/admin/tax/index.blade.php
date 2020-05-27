@extends('admin.layouts.app')

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
                <a data-toggle="modal" href="#taxModal" class="btn btn-default waves-effect waves-light">Add</a>
            </div>
            <h4 class="page-title">Tax</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <table id="tax-table" class="table  table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('common.delete_confirm',
    ['modalId'=>'deleteTax','action'=>route('tax.destroy','deleteId'),'item'=>'it','callback'=>'showMsg'])

    @include('admin.tax.create')
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
            oTable = $('#tax-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{route('tax-data')}}",
                    "type": "POST"
                },
                order: [],
                "drawCallback": function (settings) {
                    InitTooltip();
                },
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
            });
            $('#taxModal').on('hidden.bs.modal', function () {
                $('#taxModal').find('form')[0].reset();
                $('#taxModal').find('form').find('input[name="id"]').val('');
                $('#taxModal').find('input').removeAttr('disabled');
                $('#taxModal').find('button[type="submit"]').show();
            })
        });

        function setEdit(id, type) {
            if (type == undefined) {
                type = '';
            }
            var action = adminSiteURL + 'tax/' + id + '/edit';
            $.ajax({
                dataType: 'json',
                type: 'GET',
                url: action,
                data: {},
                success: function (data) {
                    setFormValues('tax_form', data.inputs);
                    $('#taxModal').modal('show');
                    if (type == 'view') {
                        setTimeout(function () {
                            $('#taxModal').find('input').attr('disabled', 'disabled');
                            $('#taxModal').find('button[type="submit"]').hide();
                        }, 100);
                    }
                }
            });
        }

        function SaveTax(form) {
            var action = $(form).attr('action');
            $.ajax({
                type: 'POST',
                url: action,
                data: $(form).serialize(),
                dataType: 'json',
                success: function (data) {
                    successMsg(data['message']);
                    $('#taxModal').modal('hide');
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
