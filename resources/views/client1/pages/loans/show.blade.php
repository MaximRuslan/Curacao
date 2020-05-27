@extends('client1.layouts.master')

@section('pageTitle')
    @lang('keywords.Loan application details')
@stop

@section('contentHeader')

@stop
@section('body_class')
    fixed-content
@stop

@section('content')
    <div class="froggy-listing">
        <div class="froggy-listing__header"
             style="background-image: url({!! asset('resources/img/client/froggy-cover.jpg') !!});">
            <div class="container">
                <h3>
                    @lang('keywords.Loan application details')
                </h3>
            </div>
        </div>
        <div class="froggy-listing__content">
            <div class="container">
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
                                            <td>{{number_format($loan['amounts'][0]['amount'],2) }}</td>
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
                                        @if($loan['loan_status'] == '11')
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
                                                                    <button class="btn action-button" type="button">
                                                                        <i class="material-icons">attachment</i>
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
                        <table id="datatable" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>@lang('keywords.Type')</th>
                                <th>Type</th>
                                <th>Type</th>
                                <th>@lang('keywords.PaymentType')</th>
                                <th>@lang('keywords.Notes')</th>
                                <th>@lang('keywords.Amount')</th>
                                <th>@lang('keywords.Cashback Amount')</th>
                                <th>@lang('keywords.Date of payment')</th>
                                <th>@lang('keywords.log_date')</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('contentFooter')
    <script>
        var loan_id = '{!! $loan['id'] !!}';
    </script>
    <script src="{!! asset(mix('resources/js/client/loansShow.js')) !!}"></script>
    <script>
        loansShow.init();
    </script>
@stop