@extends('admin1.layouts.master')
@section('page_name')
    NLBs
@stop
@section('contentHeader')
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12">
            @if(auth()->user()->hasRole('super admin|admin|processor'))
                <div class="btn-group pull-right m-b-20">
                    <a class="btn btn-default waves-effect waves-light addNlb">Add</a>
                </div>
            @endif
            <h4 class="page-title">NLBs</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            @if(auth()->user()->hasRole('super admin|admin'))
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
                        <th>ID</th>
                        <th>Date</th>
                        <th>Branch</th>
                        <th>User</th>
                        <th>User</th>
                        <th>Type</th>
                        <th>Reason</th>
                        <th>Amount</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('admin1.popups.deleteConfirm',['modal_name'=>'NlbTransaction'])
    @include('admin1.popups.nlb-create')
@stop
@section('contentFooter')
    <script src="{!! asset(mix('resources/js/admin/nlb.js')) !!}"></script>
    <script>
        window.admin = "{!! auth()->user()->hasRole('super admin|admin|auditor') !!}";
        nlb.init();
    </script>
@stop