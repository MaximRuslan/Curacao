@extends('admin1.layouts.master')
@section('page_name')
    Reconciliations
@stop
@section('contentHeader')
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12 m-b-20">
            <div class="btn-group pull-right">
                <a href="#nogo" class="btn btn-default waves-effect waves-light js--reconciliation-add-button">
                    Add
                </a>
            </div>
            <h4 class="page-title">@yield('page_name')</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <table id="datatable" class="table table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th>Id</th>
                        <th>Transaction Id</th>
                        <th>Merchant</th>
                        <th>Branch</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>OTP</th>
                        <th>Created By</th>
                        <th>Created By</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    @include('admin1.popups.reconciliation-create')
    @include('admin1.popups.deleteConfirm',['modal_name'=>'Reconciliation'])
    @include('admin1.popups.reconciliation-history')
@stop
@section('contentFooter')
    <script src="{!! asset(mix('resources/js/admin/merchantsReconciliation.js')) !!}"></script>
    <script>
        merchantsReconciliation.init();
    </script>
@stop