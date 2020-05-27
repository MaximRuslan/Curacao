@extends('admin1.layouts.master')
@section('page_name')
    @if(request('type')==1)
        Emails
    @else
        Push
    @endif
@stop
@section('contentHeader')
@stop
@section('content')
    <div class="row m-b-20">
        <div class="col-sm-12">
            <h4 class="page-title">
                @if(request('type')==1)
                    Emails
                @else
                    Push
                @endif
            </h4>
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
                        <th>Receivers</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('admin1.popups.template-create')
@stop
@section('contentFooter')
    <script>
        var template_type = '{!! request('type') !!}';
    </script>
    <script src="{!! asset(mix('resources/js/admin/template.js')) !!}"></script>
    <script>
        template.init();
    </script>
@stop