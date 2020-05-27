@extends('admin1.layouts.master')
@section('page_name')
    Loan Reasons
@stop
@section('contentHeader')
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="btn-group pull-right m-b-20">
                <a class="btn btn-default waves-effect waves-light addReason">Add</a>
            </div>
            <h4 class="page-title">Loan reasons</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <table id="datatable" class="table  table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title ENG</th>
                        <th>Title ESP</th>
                        <th>Title PAP</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('admin1.popups.deleteConfirm',['modal_name'=>'LoanReason'])
    @include('admin1.popups.loan-reason-create')
@stop
@section('contentFooter')
    <script src="{!! asset(mix('resources/js/admin/loanReason.js')) !!}"></script>
    <script>
        loanReason.init();
    </script>
@stop