@extends('admin1.layouts.master')
@section('page_name')
    Country
@stop
@section('contentHeader')
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="btn-group pull-right m-b-20">
                <a class="btn btn-default waves-effect waves-light addCountry">Add</a>
            </div>
            <h4 class="page-title">Countries</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <table id="datatable" class="table  table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Phone Code</th>
                        <th>Phone Length</th>
                        <th>Valuta</th>
                        <th>Tax</th>
                        <th>Tax %</th>
                        <th>Time Zone</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('admin1.popups.deleteConfirm',['modal_name'=>'Country'])
    @include('admin1.popups.country-create')
@stop
@section('contentFooter')
    <script src="{!! asset('plugins/tinymce.min.js') !!}"></script>
    <script src="{!! asset(mix('resources/js/admin/country.js')) !!}"></script>
    <script>
        country.init();
    </script>
@stop