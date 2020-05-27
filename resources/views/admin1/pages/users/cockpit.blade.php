@extends('admin1.layouts.master')
@section('page_name')
    Cockpit
@stop
@section('contentHeader')
    <style>
        .hide-row {
            display: none;
        }
    </style>
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <h4 class="page-title">
                Assigned Debts
            </h4>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-12">
            <div class="card-box table-responsive">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {!!Form::label('start_date','Start Date',['style'=>"font-weight:600"])!!}
                            {!!Form::text('start_date', \App\Library\Helper::databaseToFrontEditDate(date('Y-m-d',strtotime('first day of January'))), ['class'=>'form-control all-date-picker','id'=>'start_date_below','placeholder'=>'Start Date'])!!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!!Form::label('end_date','End Date',['style'=>"font-weight:600"])!!}
                            {!!Form::text('end_date', \App\Library\Helper::date_to_sheet_timezone(date('Y-m-d H:i:s')), ['class'=>'form-control all-date-picker','id'=>'end_date_below','placeholder'=>'End Date'])!!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!!Form::label('user_id','User',['style'=>"font-weight:600"])!!}
                            {!!Form::select('user_id', $users, old('user_id'), ['class'=>'form-control','id'=>'js--user','placeholder'=>'All'])!!}
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <button class="btn btn-default js--search" style="margin-top: 1.7rem;">Filter</button>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <button class="btn btn-default js--export" style="margin-top: 1.7rem;">Export</button>
                            <a href="#nogo" id="exportPDFLink"></a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        *Excl. tax
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row" id="js--cockpit-data">
    </div>
@stop
@section('contentFooter')
    <script src="{!! asset(mix('resources/js/admin/cockpit.js')) !!}"></script>
@stop