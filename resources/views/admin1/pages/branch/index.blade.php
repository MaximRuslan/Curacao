@extends('admin1.layouts.master')
@section('page_name')
    Branch
@stop
@section('contentHeader')
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="btn-group pull-right m-b-20">
                <a class="btn btn-default waves-effect waves-light addBranch">Add</a>
            </div>
            <h4 class="page-title">Branches</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <table id="datatable" class="table  table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Country</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('admin1.popups.deleteConfirm',['modal_name'=>'Branch'])
    @include('admin1.popups.branch-create')
@stop
@section('contentFooter')
    <script src="{!! asset(mix('resources/js/admin/branch.js')) !!}"></script>
    <script>
        branch.init();
    </script>
@stop