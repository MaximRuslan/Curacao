@extends('admin1.layouts.master')
@section('page_name')
    Wallet
@stop
@section('contentHeader')
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="btn-group pull-right m-b-20">
                <a id="addPayment" href="" class="btn btn-default waves-effect waves-light">Add</a>
            </div>
            <h4 class="page-title">
                Wallet
            </h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {!!Form::label('start_date','Start Date',['style'=>"font-weight:600"])!!}
                            {!!Form::text('start_date', '', ['class'=>'form-control all-date-picker','id'=>'start_date_below','placeholder'=>'Start Date'])!!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!!Form::label('end_date','End Date',['style'=>"font-weight:600"])!!}
                            {!!Form::text('end_date', '', ['class'=>'form-control all-date-picker','id'=>'end_date_below','placeholder'=>'End Date'])!!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!!Form::label('user_id','User',['style'=>"font-weight:600"])!!}
                            {!!Form::select('user_id', $users, old('user_id'), ['class'=>'form-control','id'=>'js--user','placeholder'=>'All'])!!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <button class="btn btn-default js--search" style="margin-top: 1.7rem;">Filter</button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <table id="wallet-datatable" class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Date/time</th>
                            <th>Loan #</th>
                            <th>Client</th>
                            <th>Payment Amount</th>
                            <th>Amount</th>
                            <th>Commission (%)</th>
                            <th>Employee</th>
                            <th>Created By</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('admin1.popups.wallet-create')
@stop
@section('contentFooter')
    <script src="{!! asset(mix('resources/js/admin/wallet.js')) !!}"></script>
    <script>
        wallet.init();
    </script>
@stop