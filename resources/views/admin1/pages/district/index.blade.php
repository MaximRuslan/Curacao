@extends('admin1.layouts.master')
@section('page_name')
    District
@stop
@section('contentHeader')
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="btn-group pull-right m-b-20">
                <a class="btn btn-default waves-effect waves-light addDistrict">Add</a>
            </div>
            <h4 class="page-title">Districts</h4>
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

    @include('admin1.popups.deleteConfirm',['modal_name'=>'District'])
    @include('admin1.popups.district-create')
@stop
@section('contentFooter')
    <script src="{!! asset(mix('resources/js/admin/district.js')) !!}"></script>
    <script>
        district.init();
    </script>
@stop