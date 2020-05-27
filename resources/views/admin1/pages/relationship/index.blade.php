@extends('admin1.layouts.master')
@section('page_name')
    Relationship
@stop
@section('contentHeader')
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="btn-group pull-right m-b-20">
                <a href="#" class="btn btn-default waves-effect waves-light addRelation">Add</a>
            </div>
            <h4 class="page-title">Relationship</h4>
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
                        <th>Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('admin1.popups.deleteConfirm',['modal_name'=>'Relationship'])
    @include('admin1.popups.relationship-create')
@stop
@section('contentFooter')
    <script src="{!! asset(mix('resources/js/admin/relationship.js')) !!}"></script>
    <script>
        relationship.init();
    </script>
@stop