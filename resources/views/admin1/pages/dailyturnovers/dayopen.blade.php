@extends('admin1.layouts.master')
@section('page_name')
    @if(request('type')==1)
        Day Open
    @elseif(request('type')==2)
        Bank transfers
    @elseif(request('type')==3)
        Vault
    @endif
@stop
@section('contentHeader')
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12">
            @if(auth()->user()->hasRole('super admin|admin|processor|credit and processing'))
                <div class="btn-group pull-right m-b-20 dayopenRemove">
                    <button class="btn btn-default waves-effect waves-light addNewDayOpenButton">
                        Add
                    </button>
                </div>
            @endif
            @if(request('type')==1)
                <h4 class="page-title">Day Open</h4>
            @elseif(request('type')==2)
                <h4 class="page-title">Bank transfers</h4>
            @elseif(request('type')==3)
                <h4 class="page-title">Vault</h4>
            @endif
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
                        {!!Form::select('branch_id',$branches,old('branch_id'),['class'=>'form-control','placeholder'=>'All','id'=>'branch_id'])!!}
                    </div>
                </div>
            @endif
            <div class="card-box table-responsive mt-3">
                <table id="datatable" class="table  table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>User</th>
                        <th>Branch</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Created</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    @include('admin1.popups.day-open-create')
@stop
@section('contentFooter')
    <script src="{!! asset(mix('resources/js/admin/dayopen.js')) !!}"></script>
    <script>
        window.branch_name = '{!! session('branch_name') !!}';
        window.type = "{!! request('type') !!}";
        dayopen.init();
    </script>
@stop