@extends('admin1.layouts.master')
@section('page_name')
    Push Notifications
@stop
@section('contentHeader')
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="btn-group pull-right m-b-20">
                <a href="#" class="btn btn-default waves-effect waves-light addMessage">Add</a>
            </div>
            <h4 class="page-title">Push Notifications</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <table id="datatable" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>User</th>
                        <th>Title</th>
                        <th>Body</th>
                        <th>Type</th>
                        <th>Date</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    @include('admin1.popups.push-notifications-create')
@stop
@section('contentFooter')
    <script src="{!! asset(mix('resources/js/admin/push_notifications.js')) !!}"></script>
    <script>
        push_notifications.init();
    </script>
@stop