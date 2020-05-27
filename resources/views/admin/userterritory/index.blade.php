@extends('admin.layouts.app')
@section('page_name')
    District
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
                <a data-toggle="modal" href="#userTerritoryModal"
                   class="btn btn-default waves-effect waves-light">Add</a>
            </div>
            <h4 class="page-title">District</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <table id="territory-table" class="table  table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        {{--<th>Title ESP</th>--}}
                        {{--<th>Title NED</th>--}}
                        <th>Country</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('common.delete_confirm',[
        'modalId'=>'deleteTerritory',
        'action'=>route('user-territory.destroy','deleteId'),
        'item'=>'it',
        'callback'=>'showMsg'
        ])

    @include('admin.userterritory.create')
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
            oTable = $('#territory-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{route('userterritory-data')}}",
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
                order:[[0,'desc']]
            });
            $('#userTerritoryModal').on('hidden.bs.modal', function () {
                $('#userTerritoryModal').find('form')[0].reset();
                $('#userTerritoryModal').find('form').find('input[name="id"]').val('');
                $('#userTerritoryModal').find('input').removeAttr('disabled');
                $('#userTerritoryModal').find('button[type="submit"]').show();
            })
        });

        function setEdit(id, type) {
            if (type == undefined) {
                type = '';
            }
            var action = '{{route('user-territory.show','')}}/' + id
            $.ajax({
                type: 'GET',
                url: action,
                data: {},
                dataType: 'json',
                success: function (data) {
                    setFormValues('userTerritory_form', data.inputs);
                    $('#userTerritoryModal').modal('show');
                    if (type == 'view') {
                        setTimeout(function () {
                            $('#userTerritoryModal').find('input').attr('disabled', 'disabled');
                            $('#userTerritoryModal').find('button[type="submit"]').hide();
                        }, 100);
                    }
                },
                error: function (jqXHR, exception) {
                }
            });
        }

        function SaveUserterritory(form) {
            var action = $(form).attr('action')
            $.ajax({
                type: 'POST',
                url: action,
                data: $(form).serialize(),
                dataType: 'json',
                success: function (data) {
                    successMsg('Message');
                    $('#userTerritoryModal').modal('hide');
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
