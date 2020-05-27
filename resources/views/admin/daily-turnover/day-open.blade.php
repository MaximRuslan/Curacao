@extends('admin.layouts.app')
@section('page_name')
    @if(request('type')==1)
        Day Open
    @elseif(request('type')==2)
        Bank transfers
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
    <link href="{{url(config('theme.front.plugins'))}}/bootstrap-datepicker/css/bootstrap-datepicker.min.css"
          rel="stylesheet">
@endsection


@section('content')
    <div class="row">
        <div class="col-sm-12">
            @if(auth()->user()->hasRole('super admin|admin|processor|credit and processing'))
                <div class="btn-group pull-right m-b-20 dayopenRemove">
                    <button class="btn btn-default waves-effect waves-light addNewDayOpenButton">
                        Add
                    </button>
                </div>
            @endif
            @if(request('type')==1)
                <h4 class="page-title">Day Open</h4>
            @elseif(request('type')==2)
                <h4 class="page-title">Bank transfers</h4>
            @elseif(request('type')==3)
                <h4 class="page-title">Vault</h4>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            @if(auth()->user()->hasRole('super admin|admin'))
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
                <table id="day-open-table" class="table  table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>User</th>
                        <th>Branch</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Created</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    @include('admin.daily-turnover.day-open-create')
@endsection
@section('extra-js')
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/jquery.dataTables.min.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.responsive.min.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/responsive.bootstrap4.min.js"></script>
    <script src="{{url(config('theme.front.plugins'))}}/moment/moment.js"></script>
    <script src="{{url(config('theme.front.plugins'))}}/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
@endsection

@section('custom-js')
    <script>
        var main_title = '';
    </script>
    @if(request('type')==1)
        <script>
            main_title = 'Day Open';
        </script>
    @elseif(request('type')==2)
        <script>
            main_title = 'Bank transfers';
        </script>
    @elseif(request('type')==3)
        <script>
            main_title = 'Vault';
        </script>
    @endif
    <script type="text/javascript">
        var oTable = "";
        var branch_name = '{!! session('branch_name') !!}';

        if (branch_name != '') {
            main_title += ' - ' + branch_name;

        }
        $(document).ready(function () {
            oTable = $('#day-open-table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: '25',
                ajax: {
                    "url": "{{route('daily-turnover.day-open.data')}}",
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
                    {data: 'total_amount', name: 'total_amount', searchable: false},
                    {data: 'created_at', name: 'dayopens.created_at', searchable: false},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                order: [[0, 'desc']],
            });
            $('.addNewDayOpenButton').click(function (e) {
                e.preventDefault();
                $.ajax({
                    dataType: 'json',
                    method: 'get',
                    url: siteURL + 'admin/daily-turnover/day-open/create',
                    success: function (data) {
                        $('.old-date-picker').datepicker({
                            orientation: "bottom auto",
                            clearBtn: true,
                            autoclose: true,
                            format: dateFormat
                        });
                        if (data['startDate'] != undefined) {
                            var startDate = new Date(data['startDate']);
                            startDate.setDate(startDate.getDate() + 1);
                            $('.old-date-picker').datepicker('setStartDate', startDate);
                        }
                        var endDate = new Date();
                        $('.old-date-picker').datepicker('setEndDate', endDate);
                        $('#dayOpenModal').find('.jq--title').html(main_title);
                        $('#dayOpenModal').modal('show');
                    }
                });
            });
            $('#dayOpenModal').on('hidden.bs.modal', function () {
                $('#dayOpenModal').find('form')[0].reset();
                $('#dayOpenModal').find('form').find('input[name="id"]').val('');
                $('#dayOpenModal').find('input').removeAttr('disabled');
                $('#dayOpenModal').find('select').removeAttr('disabled');
                $('#dayOpenModal').find('.error').html('');
                $('#dayOpenModal').find('button[type="submit"]').show();
            })
            $(document).on('change', '#branch_id', function (e) {
                e.preventDefault();
                oTable.draw();
            });
        });

        $(document).on('click', '.editDayOpenButton', function (e) {
            e.preventDefault();
            var branch = $(this).data('branch');
            $.ajax({
                dataType: 'json',
                method: 'get',
                url: adminSiteURL + 'daily-turnover/day-open/' + $(this).data('date') + '/' + $(this).data('user') + '/' + branch,
                success: function (data) {
                    var startDate = new Date();
                    $('.old-date-picker').datepicker({
                        orientation: "bottom auto",
                        clearBtn: true,
                        autoclose: true,
                        format: dateFormat
                    });
                    $('.old-date-picker').datepicker('setEndDate', startDate);
                    var date = new Date(data['dayopens'][0]['date']);
                    $('#dayopenForm').find('[name="date"]').datepicker('setDate', date);
                    $('#dayopenForm').find('[name="date"]').val(moment(date).format('DD/MM/YYYY'));
                    $('#dayopenForm').find('[name="old_date"]').val(moment(date).format('DD/MM/YYYY'));


                    $('#dayopenForm').find('[name="country_id"]').val(data['country']);
                    countryToggle(data['country'], branch)

                    for (var index in data['dayopens']) {
                        var dayopen = data['dayopens'][index];
                        $('#dayopenForm').find('[name="amount[' + dayopen['payment_type'] + ']"]').val(dayopen['amount']);
                    }
                    total_calculate();

                    $('#dayOpenModal').find('[name="branch"]').val(data['dayopens'][0]['branch_id']);
                    $('#dayOpenModal').find('[name="user"]').val(data['dayopens'][0]['user_id']);
                    $('#dayOpenModal').find('.jq--title').html('Day Open - ' + data['branch']);
                    $('#dayOpenModal').modal('show');
                }
            })
        });

        function SaveDayOpen(form) {
            var action = $(form).attr('action')
            $.ajax({
                type: 'POST',
                url: action,
                data: $(form).serialize(),
                dataType: 'json',
                success: function (data) {
                    if (!data['status']) {
                        $('#dayOpenModal').find('.date_error').html(data['message']);
                    } else {
                        successMsg('Message');
                        $('#dayOpenModal').modal('hide');
                        oTable.draw(true);
                    }
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

        $(document).on('click', '.viewDayopen', function (e) {
            e.preventDefault();
            var branch = $(this).data('branch');
            $.ajax({
                dataType: 'json',
                url: adminSiteURL + 'daily-turnover/day-open/' + $(this).data('date') + '/' + $(this).data('user') + '/' + branch,
                method: 'get',
                success: function (data) {
                    var startDate = new Date();
                    $('.old-date-picker').datepicker({
                        orientation: "bottom auto",
                        clearBtn: true,
                        autoclose: true,
                        format: dateFormat
                    });
                    $('.old-date-picker').datepicker('setEndDate', startDate);
                    var date = new Date(data['dayopens'][0]['date']);
                    $('#dayopenForm').find('[name="date"]').datepicker('setDate', date);
                    $('#dayopenForm').find('[name="date"]').val(moment(date).format('DD/MM/YYYY'));
                    $('#dayopenForm').find('[name="old_date"]').val(moment(date).format('DD/MM/YYYY'));

                    $('#dayopenForm').find('[name="country_id"]').val(data['country']);
                    countryToggle(data['country'], branch, 'view')

                    for (var index in data['dayopens']) {
                        var dayopen = data['dayopens'][index];
                        $('#dayopenForm').find('[name="amount[' + dayopen['payment_type'] + ']"]').val(dayopen['amount']);
                    }
                    total_calculate();
                    $('#dayopenForm').find('button[type="submit"]').hide();
                    $('#dayOpenModal').find('.jq--title').html('Day Open - ' + data['branch']);
                    $('#dayOpenModal').modal('show');
                }
            });
        });

        $(document).on('change blur keyup', '#dayopenForm input', function () {
            total_calculate();
        });

        function total_calculate() {
            var sum = 0;
            $('#dayopenForm input[name^="amount"]').each(function (key, item) {
                if ($(item).val() != '') {
                    sum = sum + parseFloat($(item).val());
                }
            });
            $('#dayopenForm input[name="total"]').val(sum);
        }

        function countryToggle(country, value, type) {
            if (country != '') {
                $.ajax({
                    dataType: 'json',
                    method: 'get',
                    url: adminSiteURL + 'countries/' + country + '/branch',
                    success: function (data) {
                        var str = '<option>Select Branch</option>';
                        for (var index in data['branches']) {
                            if (value != undefined && value == index) {
                                str += '<option value="' + index + '" selected>' + data['branches'][index] + '</option>';
                            } else {
                                str += '<option value="' + index + '">' + data['branches'][index] + '</option>';
                            }
                        }
                        $('#branch_selection').html(str);
                        if (type == 'view') {
                            setTimeout(function () {
                                $('#dayopenForm').find('input').prop('disabled', true);
                                $('#dayopenForm').find('select').prop('disabled', true);
                            }, 100);
                        }
                    }
                });
            } else {
                var str = '<option>Select Branch</option>';
                $('#branch_selection').html(str);
            }
        }


        $(document).on('change', '#country_selection', function (e) {
            e.preventDefault();
            countryToggle($(this).val())
        });
    </script>
@endsection
