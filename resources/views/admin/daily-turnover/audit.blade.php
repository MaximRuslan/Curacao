@extends('admin.layouts.app')
@section('page_name')
    Audit
    @if(request('type')==1)
        Day Open
    @elseif(request('type')==2)
        Bank
    @elseif(request('type')==3)
        Vault
    @endif
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
            <h4 class="page-title">
                Audit
                @if(request('type')==1)
                    Day Open
                @elseif(request('type')==2)
                    Bank
                @elseif(request('type')==3)
                    Vault
                @endif
            </h4>
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
                        {!!Form::select('branch_id',$branches,old('branch_id'),['class'=>'form-control','placeholder'=>'All','id'=>'branch_id'])!!}
                    </div>
                </div>
            @endif
            <div class="card-box table-responsive mt-3">
                <table id="audit-table" class="table  table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Processor</th>
                        <th>Processor</th>
                        <th>Branch</th>
                        <th>Date</th>
                        <th>Completion Date</th>
                        <th>Verified By</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    @include('admin.daily-turnover.auditView')
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
        var payment_types ={!! json_encode($payment_types) !!};
        $(document).ready(function () {
            oTable = $('#audit-table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: '25',
                ajax: {
                    "url": "{{route('daily-turnovers.audit.data')}}",
                    "type": "POST",
                    data: function (d) {
                        d.branch_id = $('#branch_id').val();
                        d.type = "{!! request('type') !!}";
                    }
                },
                order: [],
                "drawCallback": function (settings) {
                    InitTooltip();
                },
                columns: [
                    {data: 'date', name: 'dayopens.date', visible: false},
                    {data: 'username', name: 'users.firstname', searchable: false},
                    {data: 'username', name: 'users.lastname', searchable: false, visible: false},
                    {data: 'branch_name', name: 'branches.title', searchable: false},
                    {data: 'date', name: 'dayopens.date', searchable: false},
                    {data: 'completion_date', name: 'dayopens.completion_date', searchable: false},
                    {data: 'verified_by_username', name: 'dayopens.completion_date', searchable: false},
                    {data: 'status', name: 'status', searchable: false, orderable: false},
                    {data: 'created_at', name: 'dayopens.created_at', searchable: false, orderable: false},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                order: [[0, 'desc']],
            });
        });
        $(document).on('change', '#branch_id', function (e) {
            e.preventDefault();
            oTable.draw();
        });
        $(document).on('click', '.viewAuditReport', function (e) {
            e.preventDefault();
            var date = $(this).data('date');
            var branch = $(this).data('branch');
            var user = $(this).data('user');
            $.ajax({
                dataType: 'json',
                method: 'get',
                url: adminSiteURL + 'daily-turnover/audit-report/' + date + '/' + user + '/' + branch,
                data: {
                    type: "{!! request('type') !!}",
                },
                success: function (data) {
                    if (data['end_date'] != '') {
                        $('#auditReportView').find('#date_title').text(data['date'] + ' To ' + data['end_date']);
                    } else {
                        $('#auditReportView').find('#date_title').text(data['date'] + ' To ' + data['date']);
                    }
                    $('#auditReportView').find('#total_day_in').text(data['total_in']);
                    $('#auditReportView').find('#total_day_out').text(data['total_out']);
                    $('#auditReportView').find('#total_dayopen_sum').text(data['dayopen_sum']);
                    $('#auditReportView').find('#total_next_dayopen_sum').text(data['next_dayopen_sum']);
                    $('#auditReportView').find('#total_diff').text(data['total_difference']);
                    $('#auditReportView').find('.approveTodayReport').data('date', date);
                    $('#auditReportView').find('.approveTodayReport').data('branch', branch);
                    $('#auditReportView').find('.approveTodayReport').data('user', user);
                    if (data['branch'] != undefined) {
                        $('#auditReportView').find('#branch_name').html(' - ' + data['branch']['title']);
                    }

                    var str = '';
                    for (var index in payment_types) {
                        if (data['dayopens'][index] == undefined) {
                            data['dayopens'][index] = '0.00';
                        }
                        if (data['next_date_dayopens'][index] == undefined) {
                            data['next_date_dayopens'][index] = '0.00';
                        }
                        str += '<tr>';

                        str += '<td>';
                        str += payment_types[index];
                        str += '</td>';

                        str += '<td>';
                        str += data['dayopens'][index];
                        str += '</td>';

                        str += '<td>';
                        str += data['in_amount'][index];
                        str += '</td>';

                        str += '<td>';
                        str += data['out_amount'][index];
                        str += '</td>';

                        str += '<td>';
                        str += data['next_date_dayopens'][index];
                        str += '</td>';

                        str += '<td>';
                        str += data['difference'][index];
                        str += '</td>';

                        str += '</tr>';
                    }

                    $('#auditReportView').find('#auditReportTbody').html(str);
                    if (data['approved'] == true || data['is_eligible'] != true) {
                        $('.approveTodayReport').hide();
                    } else {
                        $('.approveTodayReport').show();
                    }
                    $('#auditReportView').modal('show');
                }
            })
        });
        $(document).on('click', '.approveTodayReport', function (e) {
            e.preventDefault();
            if (confirm('Are you sure you want to approve this report?')) {
                $.ajax({
                    dataType: 'json',
                    method: 'post',
                    url: adminSiteURL + 'daily-turnover/audit-report/' + $('.approveTodayReport').data('date') + '/' + $('.approveTodayReport').data('user') + '/' + $('.approveTodayReport').data('branch') + '/approve',
                    data: {
                        type: "{!! request('type') !!}",
                    },
                    success: function (data) {
                        $('.approveTodayReport').hide();
                        oTable.draw();
                    }
                });
            }
        });
    </script>
@endsection
