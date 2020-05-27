@extends('admin1.layouts.master')
@section('page_name')
    SMS
@stop
@section('contentHeader')
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="btn-group pull-right m-b-20">
                <a href="#" class="btn btn-default waves-effect waves-light addMessage">Add</a>
            </div>
            <h4 class="page-title">SMS</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <table id="message-datatable" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>User</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    @include('admin1.popups.message-create')
@stop
@section('contentFooter')
    <script src="{!! asset(mix('resources/js/admin/messages.js')) !!}"></script>
    <script>
        messages.init();
    </script>
@stop