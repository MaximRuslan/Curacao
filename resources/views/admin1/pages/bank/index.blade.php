@extends('admin1.layouts.master')
@section('page_name')
    Banks
@stop
@section('contentHeader')
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="btn-group pull-right m-b-20">
                <a class="btn btn-default waves-effect waves-light addBank">Add</a>
            </div>
            <h4 class="page-title">Bank</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <table id="datatable" class="table  table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th width="10%">ID</th>
                        <th width="10%">Name</th>
                        <th width="15%">Contact Person</th>
                        <th width="15%">Email</th>
                        <th width="10%">Phone</th>
                        <th width="10%">Country</th>
                        <th width="10%">Transaction Fee</th>
                        <th width="10%">Tax On Transaction Fee</th>
                        <th width="10%">Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('admin1.popups.deleteConfirm',['modal_name'=>'Bank'])
    @include('admin1.popups.bank-create')
@stop
@section('contentFooter')
    <script src="{!! asset(mix('resources/js/admin/bank.js')) !!}"></script>
    <script>
        bank.init();
    </script>
@stop