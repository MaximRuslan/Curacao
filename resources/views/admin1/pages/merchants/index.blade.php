@extends('admin1.layouts.master')
@section('page_name')
    Merchants
@stop
@section('contentHeader')
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="btn-group pull-right m-b-20">
                <a href="{!! url()->route('admin1.merchants1.create') !!}"
                   class="btn btn-default waves-effect waves-light">
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
                        <th>ID</th>
                        <th>Type</th>
                        <th>Last Name</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Telephone</th>
                        <th>Country</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    @include('admin1.popups.deleteConfirm',['modal_name'=>'Merchant'])
@stop
@section('contentFooter')
    <script src="{!! asset(mix('resources/js/admin/merchantsIndex.js')) !!}"></script>
    <script>
        merchantsIndex.init();
    </script>
@stop