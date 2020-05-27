@extends('admin1.layouts.viewLayout')
@section('page_name')
    @lang('keywords.Loan application details')
@stop
@section('contentHeader')
    <style>
        .red {
            color: red;
            background-color: #ffbdbd;
        }
    </style>
@stop
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
                                                <td>{!! $loan['id'] !!}</td>
                                            </tr>
                                            <tr>
                                                <td>@lang('keywords.Client')</td>
                                                <td>
                                                    <a target="_blank"
                                                       href="{!! url()->route('admin1.users.show',['user_id'=>$loan['client_id']]) !!}">
                                                        {!! ucwords(strtolower($loan['firstname']." ".$loan['lastname'])) !!}
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>@lang('keywords.Reason')</td>
                                                <td>
                                                    @if($loan['loan_reason_title'] != '')
                                                        {!! $loan['loan_reason_title'] !!}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>@lang('keywords.Salary')</td>
                                                <td>
                                                    @if(isset($loan['amounts'][0]))
                                                        {!! Helper::decimalShowing($loan['amounts'][0]['amount'],$user->country) !!}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>@lang('keywords.Salary Date')</td>
                                                <td>{!! $loan['deadline_date'] !!}</td>
                                            </tr>

                                            <tr>
                                                <td>@lang('keywords.Amount')</td>
                                                <td>{!! Helper::decimalShowing($loan['amount'],$user->country) !!}</td>
                                            </tr>
                                            <tr>
                                                <td>@lang('keywords.Suggested Amount')</td>
                                                <td>{!! Helper::decimalShowing($loan['max_amount'],$user->country) !!}</td>
                                            </tr>
                                            <tr>
                                                <td>@lang('keywords.Type')</td>
                                                <td>{!! $loan['loan_types_title'] !!}</td>
                                            </tr>

                                            <tr>
                                                <td>@lang('keywords.Status')</td>
                                                <td>{{$loan['loan_status_title']}}</td>
                                            </tr>
                                            @if($loan['loan_status'] == '11')
                                                <tr>
                                                    <td>@lang('keywords.Decline reason')</td>
                                                    <td>
                                                        @if(isset($loan['loan_decline_reasons_title']))
                                                            {!! $loan['loan_decline_reasons_title'] !!}
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>@lang('keywords.Decline Description')</td>
                                                    <td>
                                                        @if(isset($loan['decline_description']))
                                                            {!! $loan['decline_description'] !!}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif
                                            @if($loan['loan_status'] == '2')
                                                <tr>
                                                    <td>@lang('keywords.On Hold Reason')</td>
                                                    <td>
                                                        @if(isset($loan['loan_on_hold_reasons_title']))
                                                            {!! $loan['loan_on_hold_reasons_title'] !!}
                                                        @endif
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>@lang('keywords.On Hold Description')</td>
                                                    <td>
                                                        @if(isset($loan['decline_description']))
                                                            {!! $loan['decline_description'] !!}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif
                                            @if($loan['loan_status'] == '3')
                                                <tr>
                                                    <td>@lang('keywords.Approval Remarks')</td>
                                                    <td>
                                                        @if(isset($loan['decline_description']))
                                                            {!! $loan['decline_description'] !!}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td>@lang('keywords.Start Date')</td>
                                                <td>{!! $loan['start_date'] !!}</td>
                                            </tr>
                                            <tr>
                                                <td>@lang('keywords.End Date') / @lang('keywords.Completion Date')</td>
                                                <td>{!! $loan['end_date'] !!}</td>
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
                                                    <tr>
                                                        <td>{!! $value['key'] !!}</td>
                                                        <td>@if($value['value'] != '')
                                                                <a href="{!! $value['value'] !!}" class="income-image"
                                                                   target="_blank">
                                                                    <button class="btn btn-primary" type="button">
                                                                        <i class="fa fa-paperclip"></i>
                                                                    </button>
                                                                </a>
                                                            @endif
                                                        </td>
                                                        <td class="@if(isset($value['expires']) && $value['expires']!=null) red @endif">
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
                                                <td>{{Helper::decimalShowing($loan['salary'],$user->country)}}</td>
                                            </tr>
                                            <tr>
                                                <td>@lang('keywords.Total other loan')</td>
                                                <td>{{Helper::decimalShowing($loan['other_loan_deduction'],$user->country)}}</td>
                                            </tr>
                                            <tr>
                                                <td>@lang('keywords.Origination Fee')
                                                    ( {{ $loan['origination_amount'] }} %)
                                                </td>
                                                <td>{{Helper::decimalShowing($loan['origination_fee'],$user->country)}}</td>
                                            </tr>
                                            <tr>
                                                <td>@lang('keywords.Tax On Origination Fee')
                                                    ( {{ $loan['tax_percentage'] }} %)
                                                </td>
                                                <td>{{Helper::decimalShowing($loan['tax'],$user->country)}}</td>
                                            </tr>
                                            <tr>
                                                <td>@lang('keywords.Interest')
                                                    ( {{ $loan['interest'] }} %)
                                                </td>
                                                <td>{{Helper::decimalShowing($loan['interest_amount'],$user->country)}}</td>
                                            </tr>
                                            <tr>
                                                <td>@lang('keywords.Tax On Interest')</td>
                                                <td>{{ $tax_on_interest=Helper::decimalShowing(round(($loan['interest_amount'] * $loan['tax_percentage']) / 100,2),$user->country) }}</td>
                                            </tr>
                                            <tr>
                                                <td>@lang('keywords.Credit Amount')</td>
                                                <td>{{ Helper::decimalShowing(round($loan['amount'] - $loan['origination_fee'] - $loan['tax'] - $loan['interest_amount'] - $tax_on_interest, 2),$user->country) }}</td>
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
                                                            <span>{!! Helper::decimalShowing($last_history->total,$user->country) !!}</span>
                                                        @endif
                                                    </h5>
                                                </div>
                                                <div class="col-md-6 text-right">
                                                    <a class="btn btn-primary" target="_blank"
                                                       href="{!! url()->route('admin1.loans.calculation-history',$loan['id']) !!}">
                                                        <i class="fa fa-history"></i>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <h4>Notes</h4>
                                            </div>
                                            <div class="text-right">
                                                <a href="#allNotesModal" data-toggle="modal" class="btn btn-primary">
                                                    View All
                                                </a>
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
                                    <th>Loan Id</th>
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
                                    <th>Loan ID</th>
                                    <th>User</th>
                                    <th>User</th>
                                    <th>Type</th>
                                    <th>Payment Type</th>
                                    <th>Notes</th>
                                    <th>Amount</th>
                                    <th>Cashback Amount</th>
                                    <th>Payment Date</th>
                                    <th>Log Date</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin1.popups.loanNotesCreate')
    @include('admin1.popups.allNotesListing')
    @include('admin1.popups.deleteConfirm',['modal_name'=>'Note'])
@stop
@section('contentFooter')
    <script src="{!! asset(mix('resources/js/admin/loanView.js')) !!}"></script>
    <script>
        window.loan_id = "{!! $loan['id'] !!}";
        window.client_id = "{!! $loan['client_id'] !!}";
        window.admin = "{!! auth()->user()->hasRole('super admin|admin') !!}";
        loanView.init();
    </script>
@stop