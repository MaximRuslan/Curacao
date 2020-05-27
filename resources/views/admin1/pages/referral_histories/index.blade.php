@extends('admin1.layouts.master')
@section('page_name')
    Referral History
@stop
@section('contentHeader')
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12  m-b-20">
            <h4 class="page-title">Referral History</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <div class="col-md-12 mb-5 row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">Start Date *</label>
                            <input type="text" class="all-date-picker form-control" name="start_date">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">End Date</label>
                            <input type="text" class="all-date-picker form-control" name="end_date">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">Loan Status</label>
                            <select name="loan_status" class="select2Single form-control" id="loan_status">
                                <option value="0">All</option>
                                <option value="1">Start</option>
                                <option value="2">PIF</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 mt-4">
                        <button class="btn btn-primary" id="js--filter-data">Filter</button>
                        <button class="btn btn-primary" id="js--filter-excel">Excel</button>
                        <a href="#nogo" id="exportPDFLink"></a>
                    </div>
                </div>
                <table id="datatable" class="table  table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Bonus Payout</th>
                        <th>Client-ID</th>
                        <th>Client-ID</th>
                        <th>Client-ID</th>
                        <th>Status</th>
                        <th>Referred Client</th>
                        <th>Referred Client</th>
                        <th>Referred Client</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

@stop
@section('contentFooter')
    <script src="{!! asset(mix('resources/js/admin/referralHistory.js')) !!}"></script>
    <script>
        referralHistory.init();
    </script>
@stop