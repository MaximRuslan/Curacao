@extends('admin1.layouts.master')
@section('page_name')
    Bank Reconciliation
@stop
@section('contentHeader')
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12  m-b-20">
            <h4 class="page-title">Bank Reconciliation</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-3"></div>
                <div class="col-md-3"></div>
                <div class="col-md-3">
                    {!!Form::select('reconcile_type', ['1'=>'All transactions','2'=>'Only reconciled transactions','3'=>'Non-reconciled transactions'],1,['class'=>'form-control','id'=>'reconcile_type'])!!}
                </div>
            </div>
            <div class="card-box table-responsive mt-3">
                <table id="datatable" class="table  table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th style="width: 5%;">{!! Form::checkbox('','1',false,['id'=>'select_all_checkbox']) !!}</th>
                        <th>Date</th>
                        <th>Client full name</th>
                        <th>Client full name</th>
                        <th>Loan ID</th>
                        <th>Amount</th>
                        <th>Credit/Debit</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                </table>
               {{-- <div class="row">
                    <div class="col-md-6">
                        --}}{{--<div class="text-left">--}}{{--
                            --}}{{--<button class="btn btn-primary" id="select_all">Select All</button>--}}{{--
                            --}}{{--<button class="btn btn-danger" id="deselect_all">Deselect All</button>--}}{{--
                        --}}{{--</div>--}}{{--
                    </div>
                    <div class="col-md-6">
                        <div class="text-right">
                            <button class="btn btn-primary reconcile_selected">Reconcile Selected</button>
                        </div>
                    </div>
                </div>--}}
            </div>
        </div>
    </div>
    @include('admin1.popups.reconcile_confirm')
@stop
@section('contentFooter')
    <script src="{!! asset(mix('resources/js/admin/bankReconcile.js')) !!}"></script>
    <script>
        bankReconcile.init();
    </script>
@stop