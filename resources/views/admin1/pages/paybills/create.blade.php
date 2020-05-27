@extends('admin1.layouts.master')
@section('page_name')
    Pay Bills
    @if(isset($user))
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
            <h4 class="page-title">Pay Bills</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="card-box users-wizard">
                <div id="rootwizard">
                    <div class="navbar">
                        <div class="navbar-inner">
                            <ul class="nav nav-pills">
                                <li data-tab="1">
                                    <a href="#tab1" class="tab1 btn btn-default" data-toggle="tab">
                                        <span>1.</span>
                                        PERSONAL INFORMATION
                                    </a>
                                </li>
                                <li data-tab="2">
                                    <a href="#tab2" class="tab2 btn btn-default" data-toggle="tab">
                                        <span>2.</span>
                                        Bank INFORMATION
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div style="float:right">
                                <input type='button' class='paginationButton btn button-next' name='next' value='Next'/>
                                <input type='button' class='paginationButton btn saveButtonSteps' name='Save And Close' value='Save And Close'/>
                            </div>
                            <div style="float:left">
                                <input type='button' class='paginationButton btn button-previous' name='previous' value='Previous'/>
                            </div>
                        </div>
                    </div>
                    <div class="user-wizard-content">
                        <div class="tab-content">
                            <div class="tab-pane" id="tab1">
                                <form id="usersInfoForm">
                                    <input type="hidden" name="id">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                {!!Form::label('lastname','Last Name(s) *')!!}
                                                {!!Form::text('lastname', old('lastname'), ['class'=>'form-control','placeholder'=>'e.g Smith'])!!}
                                                <span class="error" for="lastname"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                {!!Form::label('firstname','First Name(s) *')!!}
                                                {!!Form::text('firstname', old('firstname'), ['class'=>'form-control','placeholder'=>'e.g John'])!!}
                                                <span class="error" for="firstname"></span>
                                            </div>
                                        </div>
                                        {{--<div class="col-md-6">
                                            <div class="form-group">
                                                {!!Form::label('email','Email Address *')!!}
                                                {!!Form::email('email', old('email'), ['class'=>'form-control','placeholder'=>'e.g johnsmith@gmail.com'])!!}
                                                <span class="error" for="email"></span>
                                            </div>
                                            @if(isset($user) && ($user->is_verified==0||$user->is_verified==null))
                                                <div class="text-left">
                                                    <a class="btn btn-default"
                                                       href="{!! url('resend/' . $user->id . '/verification') !!}">
                                                        Resend Verification Mail
                                                    </a>
                                                </div>
                                            @endif
                                        </div>--}}
                                        <div class="col-md-12">
                                            <h2>Secondary Emails</h2>
                                            <div class="text-right">
                                                <button type="button" class="addNewEmailPrimary btn btn-primary" data-toggle="tooltip"
                                                        title="Add New Seondary Email">
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
                                            <div class="has-error">
                                                <p class="help-block" style="color:red;" id="email_error"></p>
                                            </div>
                                            <span class="error" for="primary"></span>
                                        </div>
                                        {!! Form::hidden('role_id','4',['id'=>'role_id']) !!}
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                {!!Form::label('contact_person','Contact Person')!!}
                                                {!!Form::text('contact_person', '', ['class'=>'form-control','placeholder'=>'Contact Person'])!!}
                                                <span class="error" for="contact_person"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                {!!Form::label('address','Address *')!!}
                                                {!!Form::textarea('address', '', ['class'=>'form-control','placeholder'=>'Address','rows'=>3])!!}
                                                <span class="error" for="address"></span>
                                            </div>
                                        </div>
                                        @if(!auth()->user()->hasRole('super admin'))
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {!!Form::label('country','Country *')!!}
                                                    {!!Form::select('country', $countries,auth()->user()->country, ['class'=>'form-control select2single','placeholder'=>'Select Country','disabled','id'=>'country_id'])!!}
                                                    <span class="error" for="country"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {!!Form::label('territory','District')!!}
                                                    {!!Form::select('territory',$territories,auth()->user()->territory,['class'=>'form-control select2single','placeholder'=>'District'])!!}
                                                    <span class="error" for="district"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {!!Form::label('branch','Branch')!!}
                                                    {!!Form::select('branch[]',$branches,auth()->user()->userBranches->pluck('id'),['class'=>'form-control','multiple'])!!}
                                                    <span class="error" for="branch[]"></span>
                                                </div>
                                            </div>
                                        @elseif(auth()->user()->hasRole('super admin') && session()->has('country'))
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {!!Form::label('country','Country *')!!}
                                                    {!!Form::select('country', $countries,session()->get('country'), ['class'=>'form-control select2single','placeholder'=>'Select Country','disabled','id'=>'country_id'])!!}
                                                    <span class="error" for="country"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {!!Form::label('territory','District')!!}
                                                    {!!Form::select('territory',$territories,'',['class'=>'form-control select2single','placeholder'=>'District'])!!}
                                                    <span class="error" for="territory"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {!!Form::label('branch','Branch')!!}
                                                    {!!Form::select('branch[]',$branches,'',['class'=>'form-control','multiple','id'=>'branch_id'])!!}
                                                    <span class="error" for="branch[]"></span>
                                                </div>
                                            </div>
                                        @else
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {!!Form::label('country','Country *')!!}
                                                    {!!Form::select('country', $countries,'', ['class'=>'form-control select2single','placeholder'=>'Select Country','id'=>'country_id'])!!}
                                                    <span class="error" for="country"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {!!Form::label('territory','District')!!}
                                                    {!!Form::select('territory',[],'',['class'=>'form-control select2single','placeholder'=>'Select District','id'=>"territory_id"])!!}
                                                    <span class="error" for="territory"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {!!Form::label('branch[]','Branch')!!} <span
                                                            class="jq__estrick"></span>
                                                    {!!Form::select('branch[]',[],'',['class'=>'form-control','id'=>'branch_id','multiple'])!!}
                                                    <span class="error" for="branch[]"></span>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                {!!Form::label('transaction_type','Transaction fee type *')!!}
                                                {!!Form::select('transaction_type',['1'=>'percentage','2'=>'flat'],old('transaction_type'),['class'=>'form-control select2single','placeholder'=>'Transaction fee Type'])!!}
                                                <span class="error" for="transaction_type"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                {!!Form::label('transaction_fee','Transaction fee amount *')!!}
                                                {!!Form::number('transaction_fee', old('transaction_fee'), ['class'=>'form-control','placeholder'=>'Transaction fee','step'=>0.01])!!}
                                                <span class="error" for="transaction_fee"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                {!!Form::label('commission_type','Commission fee type *')!!}
                                                {!!Form::select('commission_type',['1'=>'percentage','2'=>'flat'],old('commission_type'),['class'=>'form-control select2single','placeholder'=>'Commission fee Type'])!!}
                                                <span class="error" for="commission_type"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                {!!Form::label('commission_fee','Commission fee amount *')!!}
                                                {!!Form::number('commission_fee', old('commission_fee'), ['class'=>'form-control','placeholder'=>'Commission fee','step'=>0.01])!!}
                                                <span class="error" for="commission_fee"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 status-group">
                                            <div class="form-group">
                                                {!!Form::label('status','Status *')!!}
                                                <select name="status" class="form-control select2single">
                                                    @foreach($statuses as $item)
                                                        <option value="{{$item->id}}" data-role="{!! $item->role !!}">
                                                            {{$item->title}}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <span class="error" for="status"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h2>Cellphone</h2>
                                                    <div class="text-right">
                                                        <button type="button" class="addNewCellphone btn btn-primary">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                    </div>
                                                    <div class="form-group">
                                                        <table class="table table-bordered table-user">
                                                            <thead>
                                                            <tr>
                                                                <th width="80%">Cellphone</th>
                                                                <th width="20%">Action</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody id="user_cellphones">

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
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
                                                                <th width="80%">Telephone</th>
                                                                <th width="20%">Action</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody id="user_telephones">

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="has-error">
                                                <p class="help-block" style="color:red;" id="phone_error"></p>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                {{--<div class="text-right mt-3">--}}
                                {{--<button class="btn btn-primary saveButtonSteps" data-step="0">Save And Close</button>--}}
                                {{--</div>--}}
                            </div>
                            <div class="tab-pane" id="tab2">
                                <form id="usersBankInfoForm">
                                    <div class="text-right mb-4">
                                        <button type="button" class="btn btn-primary addUserBankInfo">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                    <table class="table table-bordered table-user">
                                        <thead>
                                        <tr>
                                            <th>Bank Name *</th>
                                            <th>Bank Account Number *</th>
                                            <th>Name On Account *</th>
                                            <th>Address On Account *</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody class="tableBankInfo"></tbody>
                                    </table>
                                </form>
                                {{--<div class="text-right mt-3">
                                    <button class="btn btn-primary saveButtonSteps" data-step="2">Save And Close</button>
                                </div>--}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('contentFooter')
    <script src="{!! asset(mix('resources/js/admin/paybillsCreate.js')) !!}"></script>
    <script>
        var type = '';
        var user_id = '';
    </script>
    @if(isset($user))
        <script>
            type = 'edit';
            user_id = '{!! $user->id !!}';
        </script>
    @else
        <script>
            type = 'create';
            user_id = '';
        </script>
    @endif
    <script>
        window.type = type;
        window.user_id = user_id;
        paybillsCreate.init();
    </script>
@stop