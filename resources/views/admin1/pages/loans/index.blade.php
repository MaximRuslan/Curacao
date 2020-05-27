@extends('admin1.layouts.master')
@section('page_name')
    Loans
    @if(isset($status_name))
        ({!! $status_name !!})
    @endif
@stop
@section('contentHeader')
    <style>
        .web_registered {
            background: #e4a8a8;
        }

        #select2-client_id-results .select2-results__option[aria-disabled=true] {
            background: #fbbbbb;
        }
    </style>
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="btn-group pull-right m-b-20">
                @if(auth()->user()->hasRole('super admin|admin|processor'))
                    <a id="createNewLoan" href="" class="btn btn-default waves-effect waves-light">Add</a>
                @endif
                @if(request('status'))
                    <a href="{!! route('admin1.loans.excel') !!}?type={!! request('status') !!}" class="btn btn-primary waves-effect waves-light ml-1">
                        Export
                    </a>
                @else
                    <a href="{!! route('admin1.loans.excel') !!}" class="btn btn-primary waves-effect waves-light ml-1">
                        Export
                    </a>
                @endif
            </div>
            <h4 class="page-title">
                Loans
                @if(isset($status_name))
                    ({!! $status_name !!})
                @endif
            </h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <table id="datatable" class="table table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th><input type="checkbox" id="js--assign-checkbox" value="1"></th>
                        <th>ID</th>
                        <th style="width: 10%;">Client</th>
                        <th>Client</th>
                        <th style="width: 10%;">Collector</th>
                        <th>Collector</th>
                        <th>Follow up date</th>
                        <th>Original Due Date</th>
                        <th style="width: 5%;">Id Client</th>
                        <th style="width: 5%;">Loan ID</th>
                        <th style="width: 5%;">Type</th>
                        <th style="width: 5%;">Amount</th>
                        <th style="width: 10%;">Requested Date</th>
                        <th style="width: 5%;">Start Date</th>
                        <th style="width: 10%;">Outstanding Balance</th>
                        <th style="width: 10%;">Last Payment Date</th>
                        <th style="width: 8%;">Completed Date</th>
                        <th style="width: 7%;">Status</th>
                        <th style="width: 7%;">Deleted By</th>
                        <th style="width: 7%;">Deleted By</th>
                        <th style="width: 20%;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Action&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    @include('admin1.popups.loan_application_modal')
    @include('admin1.popups.loanChangeStatus')
    @include('admin1.popups.assignLoan')
    @include('admin1.popups.loan_transactions')
    @include('admin1.popups.loan_status_history')
    @include('admin1.popups.deleteConfirm',['modal_name'=>'Loan'])
@stop
@section('contentFooter')
    <script>
        window.clients_has_active_loans ={!! $clients_has_active_loans->values() !!};
        window.status = "{!! request('status') !!}";
        window.assign = false;
        window.my_client = false;
        statuses ={!! json_encode($statuses) !!};
        employees = {!! json_encode($employees) !!};
        collector = false;
        admin = false;
    </script>
    @if(auth()->user()->hasRole('debt collector'))
        <script>
            collector = true;
        </script>
    @endif
    @if(auth()->user()->hasAnyRole(['super admin','admin']))
        <script>
            admin = true;
        </script>
    @endif
    @if(isset($assign))
        <script>
            window.assign = {!! $assign !!};
        </script>
    @endif
    @if(isset($my_client))
        <script>
            window.my_client = {!! $my_client !!};
        </script>
    @endif
    <script src="{!! asset(mix('resources/js/admin/loans.js')) !!}"></script>
    <script>
        loans.init();
    </script>
@stop