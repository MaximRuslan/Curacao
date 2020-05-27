@extends('admin.layouts.loanviewlayout')
@section('page_name')
    Loans Info
@stop
@if(!auth()->user()->hasRole('client'))
@section('extra-styles')
    <link href="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.bootstrap4.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{url(config('theme.admin.plugins'))}}/datatables/buttons.bootstrap4.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{url(config('theme.admin.plugins'))}}/datatables/responsive.bootstrap4.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{url(config('theme.front.plugins'))}}/bootstrap-datepicker/css/bootstrap-datepicker.min.css"
          rel="stylesheet">
    <link href="{{url(config('theme.front.plugins'))}}/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
    <link href="{{url(config('theme.admin.plugins'))}}/sweet-alert2/sweetalert2.css" rel="stylesheet">
    <style>
        .red {
            background: #ffc9c9;
        }
    </style>
@endsection
@endif

@section('content')
    <div>
        <div class="modal-dialog modal-full">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title mt-0">@lang('keywords.Loan application details')</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="portlet">
                                <div class="portlet-heading bg-custom">
                                    <h3 class="portlet-title">
                                        @lang('keywords.Details')
                                    </h3>
                                    <div class="clearfix"></div>
                                </div>
                                <div id="bg-primary" class="panel-collapse collapse show">
                                    <div class="portlet-body  loan-view-table">
                                        <table class="table table-bordered table-sm loanViewModal">
                                            <tbody>
                                            <tr>
                                                <td>@lang('keywords.Loan ID')</td>
                                                <td>
                                                    {{$loan['id']}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>@lang('keywords.Client')</td>
                                                <td>
                                                    @if(auth()->user()->hasRole('client'))
                                                        {{$loan['user']['firstname'].' '.$loan['user']['lastname'] }}
                                                    @else
                                                        <a target="_blank"
                                                           href="{!! url()->route('users.edit',['user_id'=>$loan['user']['id']]) !!}">
                                                            {{$loan['user']['firstname'].' '.$loan['user']['lastname'] }}
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>@lang('keywords.Reason')</td>
                                                <td>
                                                    @if($loan['reason'] != '')
                                                        {{ $loan['reason']['title'] }}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>@lang('keywords.Salary')</td>
                                                <td>{{number_format($loan['salary'],2) }}</td>
                                            </tr>
                                            <tr>
                                                <td>@lang('keywords.Salary Date')</td>
                                                <td>{{$loan['deadline_date']}}</td>
                                            </tr>

                                            <tr>
                                                <td>@lang('keywords.Amount')</td>
                                                <td>{{number_format($loan['amount'],2)}}</td>
                                            </tr>
                                            <tr>
                                                <td>@lang('keywords.Suggested Amount')</td>
                                                <td>{{number_format($loan['max_amount'],2)}}</td>
                                            </tr>
                                            <tr>
                                                <td>@lang('keywords.Type')</td>
                                                <td>{{$loan['type']['title']}}</td>
                                            </tr>
                                            {{--<tr>
                                                <td>Duration</td>
                                                <td>{{$loan->loan_duration}}</td>
                                            </tr>
                                            <tr>
                                                <td>Interest</td>
                                                <td>{{$loan->loan_interest}}</td>
                                            </tr>--}}
                                            <tr>
                                                <td>@lang('keywords.Status')</td>
                                                <td>{{$loan['status']['title']}}</td>
                                            </tr>
                                            @if($loan['loan_status'] == '11)
                                                <tr>
                                                    <td>@lang('keywords.Decline reason')</td>
                                                    <td>
                                                        @if(isset($loan['decline_reason']))
                                                            {{$loan['decline_reason']['title']}}
                                                        @endif
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>@lang('keywords.Decline Description')</td>
                                                    <td>
                                                        @if(isset($loan['decline_description']))
                                                            {{$loan['decline_description']}}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif
                                            @if($loan['loan_status'] == '2')
                                                <tr>
                                                    <td>@lang('keywords.On Hold Reason')</td>
                                                    <td>
                                                        @if(isset($loan['onHoldReason']))
                                                            {{$loan['onHoldReason']['title']}}
                                                        @endif
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>@lang('keywords.On Hold Description')</td>
                                                    <td>
                                                        @if(isset($loan['decline_description']))
                                                            {{$loan['decline_description']}}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td>@lang('keywords.Start Date')</td>
                                                <td>{{$loan['start_date']}}</td>
                                            </tr>
                                            <tr>
                                                <td>@lang('keywords.End Date') / @lang('keywords.Completion Date')</td>
                                                <td>{{$loan['end_date']}}</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                        @if($loan['uploaded'] > 0)
                                            <div class="portlet-heading bg-custom">
                                                <h3 class="portlet-title">
                                                    @lang('keywords.LoanDocuments')
                                                </h3>
                                                <div class="clearfix"></div>
                                            </div>
                                            <table class="table table-bordered table-sm loanViewModal">
                                                <tbody>
                                                @foreach($loan['amounts'] as $amount)
                                                    @if($amount['type'] == 1)
                                                        <tr>
                                                            <td>
                                                                @if($amount['type'] == 1)
                                                                    @lang('keywords.'.$amount['title'] )
                                                                @else
                                                                    {!! $amount['title']  !!}
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if($amount['documents'] != '')
                                                                    <a class="example-image-link view-loan-images"
                                                                       href="{{asset('storage/loan_applications/'.$loan['id'].'/'.$amount['documents']['file_name'])}}"
                                                                       target="_blank">
                                                                        <button class="btn btn-primary" type="button"><i
                                                                                    class="fa fa-paperclip"></i>
                                                                        </button>
                                                                    </a>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                                </tbody>
                                            </table>
                                        @endif
                                        @if($loan['otherLoan'] > 0)
                                            <div class="portlet-heading bg-custom">
                                                <h3 class="portlet-title">
                                                    @lang('keywords.OtherLoans')
                                                </h3>
                                                <div class="clearfix"></div>
                                            </div>
                                            <table class="table table-bordered table-sm loanViewModal">
                                                <tbody>
                                                @foreach($loan['amounts'] as $k => $value)
                                                    @if($value['type'] == 2)
                                                        <tr>
                                                            <td>{!! $value['title'] !!}</td>
                                                            <td>{!! number_format($value['amount'],2) !!}</td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                                </tbody>
                                            </table>
                                        @endif
                                        @if(count($loan['user_documents']) > 0  && !auth()->user()->hasRole('client'))
                                            <div class="portlet-heading bg-custom">
                                                <h3 class="portlet-title">
                                                    @lang('keywords.ClientDocuments')
                                                </h3>
                                                <div class="clearfix"></div>
                                            </div>
                                            <table class="table table-bordered table-sm loanViewModal">
                                                <tbody>
                                                @foreach($loan['user_documents'] as $k => $value)
                                                    <tr class="@if(isset($value['expires']) && $value['expires']!=null) red @endif">
                                                        <td>   @lang('keywords.'.$value['key'] ) </td>
                                                        <td>@if($value['value'] != '')
                                                                <a href="{!! $value['value'] !!}" class="income-image"
                                                                   target="_blank">
                                                                    <button class="btn btn-primary" type="button">
                                                                        <i class="fa fa-paperclip"></i>
                                                                    </button>
                                                                </a>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if(isset($value['expires']) && $value['expires']!=null)
                                                                {!! $value['expires'] !!}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        @endif

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 pull-right">
                            <div class="portlet">
                                <div class="portlet-heading bg-custom">
                                    <h3 class="portlet-title">
                                        @lang('keywords.LoanCalculation')
                                    </h3>
                                    <div class="clearfix"></div>
                                </div>
                                <div id="bg-primary" class="panel-collapse collapse show">
                                    <div class="portlet-body  loan-view-table">
                                        <table class="table table-bordered table-sm loanViewModal">
                                            <tbody>
                                            <tr>
                                                <td>@lang('keywords.Total Salary')</td>
                                                <td>{{number_format($loan['salary'],2)}}</td>
                                            </tr>
                                            <tr>
                                                <td>@lang('keywords.Total other loan')</td>
                                                <td>{{number_format($loan['other_loan_deduction'],2)}}</td>
                                            </tr>
                                            <tr>
                                                <td>@lang('keywords.Origination Fee')
                                                    ( {{ number_format($loan['origination_amount'],2) }} %)
                                                </td>
                                                <td>{{number_format($loan['origination_fee'],2)}}</td>
                                            </tr>
                                            <tr>
                                                <td>@lang('keywords.Tax On Origination Fee')
                                                    ( {{ number_format($loan['tax_percentage'],2) }} %)
                                                </td>
                                                <td>{{number_format($loan['tax'],2)}}</td>
                                            </tr>
                                            <tr>
                                                <td>@lang('keywords.Interest')
                                                    ( {{ number_format($loan['interest'],2) }} %)
                                                </td>
                                                <td>{{number_format($loan['interest_amount'],2)}}</td>
                                            </tr>
                                            <tr>
                                                <td>@lang('keywords.Tax On Interest')</td>
                                                <td>{{ $tax_on_interest=number_format(round(($loan['interest_amount'] * $loan['tax_percentage']) / 100,2),2) }}</td>
                                            </tr>
                                            <tr>
                                                <td>@lang('keywords.Credit Amount')</td>
                                                <td>{{ number_format(round($loan['amount'] - $loan['origination_fee'] - $loan['tax'] - $loan['interest_amount'] - $tax_on_interest, 2),2) }}</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @if(auth()->user()->hasRole('super admin|admin|processor|debt collector'))
                                <div class="portlet">
                                    <div class="portlet-heading bg-custom">
                                        <h3 class="portlet-title">
                                            Collections
                                        </h3>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div id="bg-primary" class="panel-collapse collapse show">
                                        <div class="portlet-body  loan-view-table">
                                            <table class="table table-bordered table-sm">
                                                <tbody>
                                                <tr>
                                                    <td colspan="2">
                                                        <b>Mobile Phone:</b> {!! implode(',',$numbers->toArray()) !!}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">
                                                        <b>Employer Name:</b>
                                                        @if(isset($user_work))
                                                            {!! $user_work->employer !!}
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <b>Workphone:</b>
                                                        @if(isset($user_work))
                                                            {!! $user_work->telephone_code !!}{!! $user_work->telephone !!}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <b>Ext:</b>
                                                        @if(isset($user_work))
                                                            {!! $user_work->extension !!}
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <b>Manager:</b>
                                                        @if(isset($user_work))
                                                            {!! $user_work->supervisor_name !!}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <b>Phone:</b>
                                                        @if(isset($user_work))
                                                            {!! $user_work->supervisor_telephone_code !!}{!! $user_work->supervisor_telephone !!}
                                                        @endif
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h5>
                                                        <b>Open Balance:</b>
                                                        @if(isset($last_history) && $last_history!=null)
                                                            <span>{!! $last_history->total !!}</span>
                                                        @endif
                                                    </h5>
                                                </div>
                                                <div class="col-md-6 text-right">
                                                    <a class="btn btn-primary" target="_blank"
                                                       href="{!! url()->route('loan-applications.calculation-history',$loan['id']) !!}">
                                                        <i class="fa fa-history"></i>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <h4>Notes</h4>
                                            </div>
                                            <div class="text-right">
                                                <button class="btn btn-primary addNewFollowup">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </div>
                                            <table class="table table-bordered mt-2">
                                                <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Follow Up</th>
                                                    <th>Processor</th>
                                                    <th>Details</th>
                                                    @if(auth()->user()->hasRole('super admin|admin'))
                                                        <th>Action</th>
                                                    @endif
                                                </tr>
                                                </thead>
                                                <tbody id="notes_tbody"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    @if(!auth()->user()->hasRole('client'))
                        <div class="row">
                            <div class="col-md-12">
                                <div class="portlet">
                                    <div class="portlet-heading bg-custom">
                                        <h3 class="portlet-title">
                                            @lang('keywords.LoanHistory')
                                        </h3>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                                <table id="loan-history-table" class="table table-striped table-bordered"
                                       style="width:100%">
                                    <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Id number</th>
                                        <th>Reason</th>
                                        <th>Amount</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Requested Date</th>
                                        <th>Start Date</th>
                                        <th>Completed Date</th>
                                        {{--<th>Action</th>--}}
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-12">
                            <div class="portlet">
                                <div class="portlet-heading bg-custom">
                                    <h3 class="portlet-title">
                                        @lang('keywords.LoanTransactions')
                                    </h3>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                            <table id="loan-transaction-table" class="table table-striped table-bordered"
                                   style="width:100%">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Type</th>
                                    <th>Payment Type</th>
                                    <th>Notes</th>
                                    <th>Amount</th>
                                    <th>Cashback Amount</th>
                                    <th>Payment Date</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('common.loanapplication.followups-create')
    @include('common.delete_confirm',[
       'modalId'=>'deleteLoanNotes',
       'action'=>route('loan-notes.destroy','deleteId'),
       'item'=>'it',
       'callback'=>'showMsg'
       ])

@endsection


@section('extra-js')
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/jquery.dataTables.min.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.bootstrap4.min.js"></script>

    <script src="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.responsive.min.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/responsive.bootstrap4.min.js"></script>
    <script src="{{url(config('theme.front.plugins'))}}/moment/moment.js"></script>
    <script src="{{url(config('theme.front.plugins'))}}/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
    <script src="{{url(config('theme.front.plugins'))}}/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/sweet-alert2/sweetalert2.js"></script>
    <script src="{{url(config('theme.common.js'))}}/loan.js"></script>
@endsection

@section('custom-js')
    <script type="text/javascript">
        var admin = "{!! auth()->user()->hasRole('super admin|admin') !!}";
        $(document).ready(function () {
            showTransaction('{!! $loan['id'] !!}');
            getNotes('{!! $loan['id'] !!}');
            oTable = $('#loan-history-table').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: {
                    "url": "{{route('loan-application-data')}}",
                    "type": "POST",
                    data: function (d) {
                        d.user_id = "{!! $loan['client_id'] !!}";
                        d.not_id = "{!! $loan['id'] !!}";
                    }
                },
                order: [],
                "drawCallback": function (settings) {
                    InitTooltip();
                },
                columns: [
                    {data: 'id', name: 'loan_applications.id', visible: false},
                    {data: 'user_id_number', name: 'users.id', visible: false},
                    {data: 'reason_title', name: 'loan_reasons.title'},
                    {data: 'amount', name: 'loan_applications.amount'},
                    {data: 'loan_type_title', name: 'loan_types.title'},
                    {data: 'loan_status_title', name: 'loan_status.title'},
                    {data: 'created_at', name: 'created_at', searchable: false},
                    {data: 'start_date', name: 'start_date', searchable: false},
                    {data: 'end_date', name: 'end_date', searchable: false},
                    // {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                order: [[0, 'desc']],
                pageLength: '50'
            });
        });
        var startDate = new Date();
        $('.old-date-picker').datepicker({
            orientation: "bottom auto",
            clearBtn: true,
            endDate: startDate,
            autoclose: true,
            format: dateFormat
        });

        function getNotes(loan_id) {
            $.ajax({
                dataType: 'json',
                method: 'get',
                url: ajaxURL + 'loan-applications/' + loan_id + '/notes',
                success: function (data) {
                    var str = '';
                    for (var index in data['notes']) {
                        var note = data['notes'][index];
                        str += '<tr>' +
                            '<td>' + note['date'] + '</td>' +
                            '<td>' + note['follow_up'] + '</td>' +
                            '<td>' + note['user_name'] + '</td>' +
                            '<td>' + note['details'] + '</td>';
                        if (admin == '1') {
                            str += '<td>' +
                                '<button class="btn btn-primary editNote" title="Edit" data-id="' + note['id'] + '"><i class="fa fa-pencil"></i></button>' +
                                '<button class="btn btn-danger deleteNote" title="Delete" data-modal-id="deleteLoanNotes" onclick="DeleteConfirm(this)" data-id="' + note['id'] + '"><i class="fa fa-trash"></i></button>' +
                                '</td>';
                        }
                        str += '</tr>';
                    }
                    $('#notes_tbody').html(str);
                }
            })
        }

        $(document).on('click', '.addNewFollowup', function (e) {
            e.preventDefault();
            $('#addNewFollowupModal').find('#add_new_note_form')[0].reset();
            $('#addNewFollowupModal').find('[name="id"]').val('');
            $('#addNewFollowupModal').modal('show');
        });
        $(document).on('click', '.editNote', function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'get',
                url: ajaxURL + 'loan-applications/{!! $loan['id'] !!}/notes/' + $(this).data('id'),
                success: function (data) {
                    setFormValues('add_new_note_form', data.inputs);
                    $('#addNewFollowupModal').modal('show');
                    // if (type == 'view') {
                    //     setTimeout(function () {
                    //         $('#bankModal').find('input').attr('disabled', 'disabled');
                    //         $('#bankModal').find('button[type="submit"]').hide();
                    //     }, 100);
                    // }
                }
            })
        });
        $(document).on('submit', '#add_new_note_form', function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'post',
                url: ajaxURL + 'loan-applications/{!! $loan['id'] !!}/notes',
                data: $(this).serialize(),
                success: function (data) {
                    getNotes('{!! $loan['id'] !!}');
                    $('#addNewFollowupModal').modal('hide');
                },
                error: function (jqXHR) {
                    var Response = jqXHR.responseText;
                    Response = $.parseJSON(Response);
                    DisplayErrorMessages(Response, $('#add_new_note_form'), 'input');
                }
            });
        });
    </script>
@endsection
