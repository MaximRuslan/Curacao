@extends('admin1.layouts.master')
@section('page_name')
    @if(isset($type) && $type=='web')
        WEB Registrations
    @else
        Users
    @endif
@stop
@section('contentHeader')
    <style>
        .dataTables_filter {
            display: inline-flex;
            float: right;
        }

        .statusSelect2 {
            text-align: left;
            width: 161px;
            margin-right: 10px;
        }
    </style>
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12">
            @if(auth()->user()->hasRole('super admin|admin|processor'))
                <div class="btn-group pull-right m-b-20">
                    <a href="{!! url()->route('admin1.users.create') !!}"
                       class="btn btn-default waves-effect waves-light">
                        Add
                    </a>
                    @if(auth()->user()->hasRole('super admin|admin'))
                        @if(!isset($type) || $type!='web')
                            <a href="{!! route('admin1.users.excel') !!}" class="btn btn-primary waves-effect waves-light ml-1">
                                Export
                            </a>
                        @endif
                    @endif
                </div>
            @endif
            <h4 class="page-title">
                @if(isset($type) && $type=='web')
                    WEB Registrations
                @else
                    Users
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
                        <th>ID</th>
                        <th>Last Name</th>
                        <th>Name</th>
                        <th>Collector</th>
                        <th>ID</th>
                        <th>Country</th>
                        <th>Status</th>
                        <th>Role</th>
                        <th>Is Verified</th>
                        <th>Registration Date</th>
                        <th>Wallet</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    @include('admin1.popups.userWalletAdd')
    @include('admin1.popups.userPdf')
    @include('admin1.popups.deleteConfirm',['modal_name'=>'User'])
@stop
@section('contentFooter')
    @if(isset($type) && $type=='web')
        <script>
            window.type = "{!! $type !!}";
        </script>
    @endif
    <script src="{!! asset(mix('resources/js/admin/usersIndex.js')) !!}"></script>
    <script>
        usersIndex.init();
    </script>
@stop