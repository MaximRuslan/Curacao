@extends('admin1.layouts.viewLayout')
@section('page_name')
    Transaction History
@stop
@section('contentHeader')
@stop
@section('content')
    <div>
        <div class="modal-dialog modal-full">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title mt-0">Transaction History {!! $loan->id !!} - {!! $user->firstname.' '.$user->lastname !!} - {!! $country->name !!}</h4>
                    @if(!config('site.cron_auto_mode'))
                        <div class="text-right">
                            <button type="button" class="btn btn-primary" id="addNewHistory">Cron Job</button>
                        </div>
                    @endif
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Week</th>
                            <th>Date</th>
                            <th>Transaction</th>
                            <th>Amount For Trans.</th>
                            <th>Principal</th>
                            <th>Origination</th>
                            <th>Interest</th>
                            <th>Renewal</th>
                            <th>Tax</th>
                            <th>Debt Collection</th>
                            <th>Debt Collection Tax</th>
                            <th>Admin fees (incl tax)</th>
                            <th>Total Balance</th>
                            <th>Collector</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody class="loan_history_table"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @if(auth()->user()->hasRole('super admin|admin'))
        @include('admin1.popups.editTransaction')
    @endif
@stop
@section('contentFooter')
    <script src="{!! asset(mix('resources/js/admin/loanCalculation.js')) !!}"></script>
    <script>
        var admin = false;
    </script>
    @if(env('RECEIPT_ON')==true)
        <script>
            window.receipt_on = true;
        </script>
    @else
        <script>
            window.receipt_on = false;
        </script>
    @endif
    @if(auth()->user()->hasRole('super admin|admin'))
        <script>
            admin = true;
        </script>
    @endif
    <script>
        window.admin = admin;
        window.loan_id = '{!! $loan->id !!}';
        loanCalculation.init();
    </script>
@stop