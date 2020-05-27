@extends('admin.layouts.app')
@section('page_name')
    Messages
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
                <a data-toggle="modal" href="#messageModel" class="btn btn-default waves-effect waves-light">Add</a>
            </div>
            <h4 class="page-title">Messages</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <table id="messages-table" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>User</th>
                        <th>Title</th>
                        <th>Body</th>
                        <th>Type</th>
                        <th>Date</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    @include('admin.messages.create')
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
        var oTable = '';
        $('#country_select').select2();
        $('#user_select').select2({
            'placeholder': 'Select Users'
        });
        $('#messageModel').on('hidden.bs.modal', function () {
            $('#messageModel').find('form')[0].reset();
            $('#messageModel').find('form').find('input[name="id"]').val('');
            $('#messageModel').find('input').removeAttr('disabled');
            $('#messageModel').find('button[type="submit"]').show();
        });
        $(document).ready(function () {
            oTable = $('#messages-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{route('messages-data')}}",
                    "type": "POST"
                },
                order: [],
                "drawCallback": function (settings) {
                    InitTooltip();
                },
                columns: [
                    {data: 'id', name: 'firebase_notifications.id', visible: false},
                    {data: 'user_name', name: 'users.lastname'},
                    {data: 'user_name', name: 'users.firstname', visible: false},
                    {data: 'title', name: 'firebase_notifications.title'},
                    {data: 'body', name: 'firebase_notifications.body'},
                    {data: 'type', name: 'firebase_notifications.type'},
                    {data: 'created_at', name: 'firebase_notifications.created_at', searchable: false},
                ],
                order: [[0, 'desc']]
            });
        });

        $(document).on('change', '#country_select', function (e) {
            e.preventDefault();
            if ($(this).val() != '') {
                $.ajax({
                    dataType: 'json',
                    method: 'get',
                    url: adminSiteURL + 'countries/' + $(this).val() + '/users',
                    success: function (data) {
                        str = '';
                        for (var index in data['users']) {
                            str += '<option value="' + index + '">' + data['users'][index] + '</option>';
                        }
                        $('#user_select').html(str);
                    }
                });
            } else {
                str = '';
                $('#user_select').html(str);
            }
        });

        $(document).on('change', '#select_all_checkbox', function () {
            if ($('#select_all_checkbox:checked').val() == 1) {
                $('#user_select').val('').select2({
                    'placeholder': 'Select Users'
                });
                $('#user_select').prop('disabled', true).select2({
                    'placeholder': 'Select All'
                });
            } else {
                $('#user_select').prop('disabled', false).select2({
                    'placeholder': 'Select Users'
                });
            }
        });

        $(document).on('submit', '#messageModelForm', function (e) {
            e.preventDefault();
            console.log($('#messageModelForm').serialize());
            $.ajax({
                dataType: 'json',
                method: 'post',
                url: adminSiteURL + 'messages',
                data: $('#messageModelForm').serialize(),
                success: function (data) {
                    oTable.draw();
                    $('#messageModelForm')[0].reset();
                    $('#country_select').val('').select2();
                    $('#user_select').val('').select2({
                        'placeholder': 'Select Users'
                    });
                    $('#messageModel').modal('hide');
                }
            })
        });
    </script>
@endsection
