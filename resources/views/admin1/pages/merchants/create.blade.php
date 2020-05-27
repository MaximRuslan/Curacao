@extends('admin1.layouts.master')
@section('page_name')
    Merchants
    @if(isset($merchant))
        Edit
    @else
        Create
    @endif
@stop
@section('contentHeader')
    <style>
        .select2-container--default .select2-results__option[aria-disabled=true] {
            display: none;
        }

        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none !important;
            margin: 0 !important;
        }
    </style>
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <h4 class="page-title">@yield('page_name')</h4>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-sm-12">
            <div class="card-box users-wizard">
                <div class="usersAlert" style="display: none;"></div>
                @if(isset($merchant))
                    {!! Form::model($merchant, ['id'=>'merchantInfoForm']) !!}
                @else
                    {!! Form::open(['id'=>'merchantInfoForm']) !!}
                @endif
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('type','Type *') !!}
                            {!! Form::select('type',$types,old('type'),['class'=>'form-control select2Single','placeholder'=>'Select Type']) !!}
                            <span class="error" for="type"></span>
                        </div>
                    </div>
                    <div class="col-md-6 js--type js--type-2">
                        <div class="form-group">
                            {!! Form::label('merchant_id','Merchant *') !!}
                            {!! Form::select('merchant_id',$merchants, old('merchant_id'), ['class'=>'form-control select2Single','placeholder'=>'Select Merchant','id'=>'js--merchant-id']) !!}
                            <span class="error" for="merchant_id"></span>
                        </div>
                    </div>
                    <div class="col-md-6 js--type js--type-1">
                        <div class="form-group">
                            {!! Form::label('name','Company Name *') !!}
                            {!! Form::text('name', old('name'), ['class'=>'form-control','placeholder'=>'Name']) !!}
                            <span class="error" for="name"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!!Form::label('last_name','Last Name(s) *')!!}
                            {!!Form::text('last_name', old('last_name'), ['class'=>'form-control','placeholder'=>'Last Name'])!!}
                            <span class="error" for="last_name"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!!Form::label('first_name','First Name(s) *')!!}
                            {!!Form::text('first_name', old('first_name'), ['class'=>'form-control','placeholder'=>'First Name'])!!}
                            <span class="error" for="first_name"></span>
                        </div>
                    </div>
                    <div class="col-md-6  js--type js--type-2">
                        <div class="form-group">
                            {!!Form::label('email','Email(s) *')!!}
                            {!!Form::text('email', old('email'), ['class'=>'form-control','placeholder'=>'Email'])!!}
                            <span class="error" for="email"></span>
                        </div>
                    </div>
                    <div class="col-md-6  js--type js--type-2">
                        <div class="form-group">
                            {!!Form::label('branch','Branch(s) *')!!}
                            {!!Form::select('branch_id',[], old('branch_id'), ['class'=>'form-control select2Single','placeholder'=>'Select Branch'])!!}
                            <span class="error" for="branch_id"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!!Form::label('lang','Language *')!!}
                            {!!Form::select('lang', $lang,old('lang'), ['class'=>'form-control select2Single','placeholder'=>'Select Language','id'=>'users_lang'])!!}
                            <span class="error" for="lang"></span>
                        </div>
                    </div>
                    <div class="col-md-6  js--type js--type-1">
                        <div class="form-group">
                            {!!Form::label('tax_id','Tax ID *')!!}
                            {!!Form::text('tax_id', old('tax_id'), ['class'=>'form-control','placeholder'=>'Tax ID'])!!}
                            <span class="error" for="tax_id"></span>
                        </div>
                    </div>
                    <div class="col-md-12  js--type js--type-1">
                        <h2>Emails</h2>
                        <div class="text-right">
                            <button type="button" class="addNewEmailPrimary btn btn-primary" data-toggle="tooltip" title="Add New Seondary Email">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                        <div class="form-group mt-2">
                            <table class="table table-bordered table-user">
                                <thead>
                                <tr>
                                    <th width="60%;">Email</th>
                                    <th width="10%;">Primary</th>
                                    <th width="10%;">verified</th>
                                    <th width="20%;">Action</th>
                                </tr>
                                </thead>
                                <tbody id="user_emails">

                                </tbody>
                            </table>
                        </div>
                        <span class="error" for="secondary_email"></span>
                        <span class="error" for="primary"></span>
                    </div>
                    @if(auth()->user()->hasRole('super admin') && !session()->has('country'))
                        <div class="col-md-6 js--type js--type-1">
                            <div class="form-group">
                                {!!Form::label('country','Country *')!!}
                                {!!Form::select('country_id', $countries,old('country_id'), ['class'=>'form-control select2Single','placeholder'=>'Select Country','id'=>'country_id'])!!}
                                <span class="error" for="country_id"></span>
                            </div>
                        </div>
                    @elseif(auth()->user()->hasRole('super admin') && session()->has('country'))
                        {!! Form::hidden('country_id',session('country'),['id'=>'country_id']) !!}
                    @else
                        {!! Form::hidden('country_id',auth()->user()->country,['id'=>'country_id']) !!}
                    @endif
                    <div class="col-md-6 status-group js--type js--type-2" style="margin-top: 28px;">
                        <div class="form-group">
                            @if(isset($merchant) && $merchant->reconciliation==1)
                                <label><input type="checkbox" name="reconciliation" value="1" checked> Reconciliation Menu</label>
                            @else
                                <label><input type="checkbox" name="reconciliation" value="1"> Reconciliation Menu</label>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6 status-group">
                        <div class="form-group">
                            {!!Form::label('status','Status *')!!}
                            <select name="status" class="form-control select2Single">
                                @foreach($statuses as $item)
                                    <option value="{{$item->id}}" data-role="{!! $item->role !!}">
                                        {{$item->title}}
                                    </option>
                                @endforeach
                            </select>
                            <span class="error" for="status"></span>
                        </div>
                    </div>
                    <div class="col-md-12"></div>
                    <div class="col-md-6 js--type js--type-1">
                        <h2>Branch *</h2>
                        <div class="text-right">
                            <button type="button" class="addNewBranch btn btn-primary">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                        <div class="form-group">
                            <table class="table table-bordered table-user">
                                <thead>
                                <tr>
                                    <th width="80%">Branch</th>
                                    <th width="20%">Action</th>
                                </tr>
                                </thead>
                                <tbody id="js--branch-tbody">

                                </tbody>
                            </table>
                        </div>
                        <span class="error" for="branches"></span>
                    </div>
                    <div class="col-md-6 js--type js--type-1">
                        <h2>Telephone <span class="jq__not_client">*</span></h2>
                        <div class="text-right">
                            <button type="button" class="addNewTelephone btn btn-primary">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                        <div class="form-group">
                            <table class="table table-bordered table-user">
                                <thead>
                                <tr>
                                    <th width="60%">Telephone</th>
                                    <th width="20%">Primary</th>
                                    <th width="20%">Action</th>
                                </tr>
                                </thead>
                                <tbody id="user_telephones">

                                </tbody>
                            </table>
                        </div>
                        <span class="error" for="telephone"></span>
                    </div>
                    <div class="col-md-12 js--type js--type-1">
                        <h2>Commission</h2>
                        <div class="text-right">
                            <button type="button" class="addNewCommission btn btn-primary">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                        <div class="form-group">
                            <table class="table table-bordered table-user">
                                <thead>
                                <tr>
                                    <th width="30%">Min Amount</th>
                                    <th width="30%">Max Amount</th>
                                    <th width="30%">Commission (%)</th>
                                    <th width="10%">Action</th>
                                </tr>
                                </thead>
                                <tbody id="js--commission-tbody">

                                </tbody>
                            </table>
                        </div>
                        <span class="error" for="min_amount"></span>
                    </div>
                    @if(isset($merchant))
                        {!! Form::hidden('id',$merchant->id) !!}
                    @endif
                    <div class="col-md-12 text-left">
                        <button class="btn btn-primary">Save</button>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@stop
@section('contentFooter')
    @if(isset($merchant))
        <script>
            var branch_id = '{!! $merchant->branch_id !!}';
            var merchant_emails = {!! json_encode($emails) !!};
            var merchant_commissions = {!! json_encode($commissions) !!};
            var merchant_branches = {!! json_encode($branches) !!};
            var merchant_telephones = {!! json_encode($telephones) !!};
        </script>
    @else
        <script>
            var branch_id = '';
            var merchant_emails = [];
            var merchant_commissions = [];
            var merchant_branches = [];
            var merchant_telephones = [];
        </script>
    @endif
    <script src="{!! asset(mix('resources/js/admin/merchantsCreate.js')) !!}"></script>
    <script>
        merchantsCreate.init();
    </script>
@stop