@extends('admin1.layouts.master')
@section('page_name')
    @if(request('type')==2)
        Transfer To Bank -
    @elseif(request('type')==1)
        Cash Payouts -
    @endif
    @if(request('status')==1)
        Requests
    @elseif(request('status')==2)
        @if(request('type')==1)
            In Process
        @elseif(request('type')==2)
            Approved
        @endif
    @elseif(request('status')==3)
        Completed
    @elseif(request('status')==4)
        Rejected
    @endif
@stop
@section('contentHeader')
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12">
            @if(!auth()->user()->hasRole('super admin|processor'))
                <div class="btn-group pull-right m-b-20">
                    <a href="#nogo" class="btn btn-default waves-effect waves-light addCredit">Add</a>
                </div>
            @endif
            <h4 class="page-title">
                @if(request('type')==2)
                    Transfer To Bank -
                @elseif(request('type')==1)
                    Cash Payouts -
                @endif
                @if(request('status')==1)
                    Requests
                @elseif(request('status')==2)
                    @if(request('type')==1)
                        Approved
                    @elseif(request('type')==2)
                        In Process
                    @endif
                @elseif(request('status')==3)
                    Completed
                @elseif(request('status')==4)
                    Rejected
                @endif
            </h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            @if(auth()->user()->hasRole('super admin|credit and processing'))
                <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-md-3"></div>
                    <div class="col-md-3"></div>
                    <div class="col-md-3">
                        {!!Form::label('branch_id','Branch')!!}
                        {!!Form::select('branch_id',$branches,old('branch_id'),['class'=>'form-control select2','placeholder'=>'All','id'=>'branch_id'])!!}
                    </div>
                </div>
            @endif
            <div class="card-box table-responsive mt-3">
                <table id="datatable" class="table  table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th>Id</th>
                        <th style="width: 5%;">{!! Form::checkbox('','1',false,['id'=>'select_all_checkbox']) !!}</th>
                        <th>User</th>
                        <th>User</th>
                        <th>Payment Type</th>
                        <th>Amount</th>
                        <th>Bank</th>
                        <th>Account #</th>
                        <th>Transaction Charge</th>
                        <th>Branch</th>
                        <th>Notes</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    @include('admin1.popups.deleteConfirm',['modal_name'=>'Credit'])
    @include('admin1.popups.credit-create')
    @include('admin1.popups.rejectcredit')
    @include('admin1.popups.statusHistory')
    @if(request('type')==1 && (request('status')==2 || request('status')==3))
        @include('admin1.popups.creditWallet')
    @endif
@stop
@section('contentFooter')
    <script src="{!! asset(mix('resources/js/admin/credit.js')) !!}"></script>
    <script>
        window.status = '{!! request('status') !!}';
        window.type = '{!! request('type') !!}';
        credit.init();
    </script>
@stop