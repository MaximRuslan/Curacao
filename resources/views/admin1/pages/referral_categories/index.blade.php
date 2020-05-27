@extends('admin1.layouts.master')
@section('page_name')
    Referral Categories
@stop
@section('contentHeader')
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="btn-group pull-right m-b-20">
                <a class="btn btn-default waves-effect waves-light addCategory">Add</a>
            </div>
            <h4 class="page-title">Referral Categories</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <table id="datatable" class="table  table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th style="width: 5%;">ID</th>
                        <th style="width: 15%;">Country</th>
                        <th style="width: 15%;">Title</th>
                        <th style="width: 10%;">Min Referrals</th>
                        <th style="width: 10%;">Max Referrals</th>
                        <th style="width: 10%;">Pay Per Loan start</th>
                        <th style="width: 10%;">Pay Per Loan PIF</th>
                        <th style="width: 10%;">Status</th>
                        <th style="width: 15%;">Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('admin1.popups.deleteConfirm',['modal_name'=>'ReferralCategory'])
    @include('admin1.popups.referral-category-create')
@stop
@section('contentFooter')
    <script src="{!! asset(mix('resources/js/admin/referral_category.js')) !!}"></script>
    <script>
        referral_category.init();
    </script>
@stop