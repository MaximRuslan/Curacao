@extends('layouts.app')

@section('extra-styles')
    <link href="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.bootstrap4.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{url(config('theme.admin.plugins'))}}/datatables/buttons.bootstrap4.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{url(config('theme.admin.plugins'))}}/datatables/responsive.bootstrap4.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{url(config('theme.admin.plugins'))}}/sweet-alert2/sweetalert2.css" rel="stylesheet">
    <style>
        .card-box {
            display: flex;
            align-items: center;
            height: 100%;
        }

        .card-box i {
            margin-right: 5px;
        }
    </style>
@endsection

@section('content')

    <section class="section" id="login">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    @if(auth()->user()->status != 2)
                        <div class="btn-group pull-right m-b-20">
                            <a href="{!! route('client.loans.create')!!}"
                               class="btn btn-primary m-b-20 pull-right">@lang('keywords.Apply for loan')</a>
                        </div>
                    @endif
                    <h3 class="title text-center">@lang('keywords.My Loans')</h3>
                </div>
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table id="my-applications" class="table table-bordered table-striped" style="width:100%">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>@lang('keywords.Loan reason')</th>
                                <th>@lang('keywords.Amount')</th>
                                <th>@lang('keywords.Loan type')</th>
                                <th>@lang('keywords.Status')</th>
                                <th>@lang('keywords.Decline reason')</th>
                                <th>@lang('keywords.Requested Date')</th>
                                <th>@lang('keywords.Start Date')</th>
                                <th>@lang('keywords.Completed Date')</th>
                                <th>@lang('keywords.Action')</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('common.delete_confirm',[
        'modalId'=>'deleteLoanApplication',
        'action'=>route('loan-applications.destroy','deleteId'),
        'callback'=>'showMsg'
    ])
    @include('common.applyLoanModal')
@endsection

@section('extra-js')
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/jquery.dataTables.min.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.bootstrap4.min.js"></script>

    <script src="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.responsive.min.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/responsive.bootstrap4.min.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/sweet-alert2/sweetalert2.js"></script>
    <script src="{{url(config('theme.common.js'))}}/loan.js"></script>
    {{--<script src="//{{ Request::getHost() }}:6001/socket.io/socket.io.js"></script>--}}
    {{--<script src="{!!asset('js/app.js')!!}"></script>--}}
@endsection

@section('custom-js')
    <script type="text/javascript">
        var oTable = "";
        $(document).ready(function () {

            oTable = $('#my-applications').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{route('client.loan.application')}}",
                    "type": "POST"
                },
                order: [[0, 'desc']],
                "drawCallback": function (settings) {
                    InitTooltip();
                    $('[data-toggle="tooltip"]').tooltip();
                },
                columns: [
                    {data: 'id', name: 'loan_applications.id', visible: false},
                    {data: 'reason_title', name: 'loan_reasons.title'},
                    {data: 'amount', name: 'loan_applications.amount'},
                    {data: 'loan_type_title', name: 'loan_types.title'},
                    {data: 'loan_status_title', name: 'loan_status.title'},
                    {data: 'loan_decline_reasons_title', name: 'loan_decline_reasons.title', searchable: false},
                    {data: 'created_at', name: 'created_at', searchable: false},
                    {data: 'start_date', name: 'start_date', searchable: false},
                    {data: 'end_date', name: 'end_date', searchable: false},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                "language": {
                    "lengthMenu": keywords.Show + " _MENU_ " + keywords.Entries,
                    "zeroRecords": keywords.NoMatchingRecordsFound,
                    "info": keywords.Showing + ('_END_' == 0 ? 0 : " _START_ ") + keywords.To + " _END_ " + keywords.Of + " _TOTAL_ " + keywords.Entries,
                    "infoEmpty": keywords.Showing + " 0 " + keywords.To + " 0 " + keywords.Of + " 0 " + keywords.Entries,
                    "infoFiltered": "(" + keywords.FilteredFrom + " _MAX_ " + keywords.TotalRecords + ")",
                    "search": keywords.Search,
                    "paginate": {
                        "previous": keywords.Previous,
                        "next": keywords.Next,
                    },
                }
            });
        });

        function SaveStatusApplication(form) {
            $('#loanApplicationModal').modal('hide');
            var id = $(form).find('input[name="id"]').val();
            var reason = $(form).find('#decline_reason').val();
            var description = $(form).find('#decline_description').val();
            action(id, 'Declined', reason, description);
            return false;
        }
    </script>
    <script>
        $(document).ready(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endsection
