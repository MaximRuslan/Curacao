@extends('admin1.layouts.master')
@section('page_name')
    Audit
    @if(request('type')==1)
        Day Open
    @elseif(request('type')==2)
        Bank
    @elseif(request('type')==3)
        Vault
    @endif
@stop
@section('contentHeader')
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <h4 class="page-title">
                Audit
                @if(request('type')==1)
                    Day Open
                @elseif(request('type')==2)
                    Bank
                @elseif(request('type')==3)
                    Vault
                @endif
            </h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            @if(auth()->user()->hasRole('super admin'))
                <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-md-3"></div>
                    <div class="col-md-3"></div>
                    <div class="col-md-3">
                        {!!Form::label('branch_id','Branch')!!}
                        {!!Form::select('branch_id',$branches,old('branch_id'),['class'=>'form-control','placeholder'=>'All','id'=>'branch_id'])!!}
                    </div>
                </div>
            @endif
            <div class="card-box table-responsive mt-3">
                <table id="datatable" class="table  table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Processor</th>
                        <th>Processor</th>
                        <th>Branch</th>
                        <th>Date</th>
                        <th>Completion Date</th>
                        <th>Verified By</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    @include('admin1.popups.audit_view')
@stop
@section('contentFooter')
    <script src="{!! asset(mix('resources/js/admin/audit.js')) !!}"></script>
    <script>
        window.branch_name = '{!! session('branch_name') !!}';
        window.type = "{!! request('type') !!}";
        window.payment_types ={!! json_encode($payment_types) !!};
        audit.init();
    </script>
@stop