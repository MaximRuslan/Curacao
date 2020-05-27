@extends('admin1.layouts.master')
@section('page_name')
    Users
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
            <h4 class="page-title">Users</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            @if(session()->has('message'))
                <div class="alert alert-{!! session('type') !!}" role="alert">
                    {!! session('message') !!}
                </div>
            @endif
            <div class="card-box users-wizard">
                <div id="wizard-validation-form">
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
                                            EMPLOYMENT INFORMATION
                                        </a>
                                    </li>
                                    <li data-tab="3">
                                        <a href="#tab3" class="tab3 btn btn-default" data-toggle="tab">
                                            <span>3.</span>
                                            Bank/Web INFORMATION
                                        </a>
                                    </li>
                                    <li data-tab="4">
                                        <a href="#tab4" class="tab4 btn btn-default" data-toggle="tab">
                                            <span>4.</span>
                                            REFERENCE INFORMATION
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-12">
                                <div style="float:right">
                                    <input type='button' class='paginationButton btn button-next' name='next' value='Next'/>
                                    <input type='button' class='btn saveButtonSteps' name='Save And Close' value='Save And Close'/>
                                </div>
                                <div style="float:left">
                                    <input type='button' class='paginationButton btn button-previous' name='previous' value='Previous'/>
                                </div>
                            </div>
                        </div>
                        <div class="user-wizard-content">
                            <div class="tab-content">
                                <div class="tab-pane" id="tab1">
                                    <div class="usersAlert" style="display: none;"></div>
                                    <form id="usersInfoForm">
                                        <input type="hidden" name="id">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    {!!Form::label('role_id','Role *')!!}
                                                    {!!Form::select('role_id',$roles,old('role_id'),['class'=>'form-control select2single','placeholder'=>'Select Role','id'=>'role_id'])!!}
                                                    <span class="error" for="role_id"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-12"></div>
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
                                            <div class="col-md-6 jq__client">
                                                <div class="form-group">
                                                    {!!Form::label('lang','Language *')!!}
                                                    {!!Form::select('lang', $lang,old('lang'), ['class'=>'form-control select2single','placeholder'=>'Select Language','id'=>'users_lang'])!!}
                                                    <span class="error" for="lang"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <h2>Emails</h2>
                                                <div class="text-right">
                                                    <button class="addNewEmailPrimary btn btn-primary" type="button" data-toggle="tooltip" title="Add New Email">
                                                        <i class="fa fa-plus"></i>
                                                    </button>
                                                </div>
                                                <div class="form-group mt-2">
                                                    <table class="table table-bordered table-user">
                                                        <thead>
                                                        <tr>
                                                            <th width="60%;">Email</th>
                                                            <th width="10%;">Primary</th>
                                                            <th width="10%;">Verified</th>
                                                            <th width="20%;">Action</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody id="user_emails">
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <span class="error" for="primary"></span>
                                                <div class="has-error">
                                                    <p class="help-block" style="color:red;" id="email_error"></p>
                                                </div>
                                            </div>
                                            <div class="col-md-4 jq__client">
                                                <div class="form-group">
                                                    {!!Form::label('Payslip1 *','Payslip1 *',['data-toggle'=>'tooltip','title'=>'png,gif,jpg,jpeg,doc,docx,pdf'])!!}
                                                    {!! Form::file('payslip1',['class'=>'form-control','data-toggle'=>'tooltip','title'=>'png,gif,jpg,jpeg,doc,docx,pdf','accept'=>'image/x-png,image/gif,image/jpeg,.doc,.docx,.pdf']) !!}
                                                    <span class="error" for="payslip1"></span>
                                                    <div class="payslip1_image" style="display: none;">
                                                        <a href="" download
                                                           class="img-responsive btn btn-primary">
                                                            <i class="fa fa-paperclip"></i>
                                                        </a>
                                                        <button type="button"
                                                                class="btn btn-danger delete-btn removeImage"
                                                                data-index="payslip1">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 jq__client">
                                                <div class="form-group">
                                                    {!!Form::label('Payslip2 *','Payslip2 *',['data-toggle'=>'tooltip','title'=>'png,gif,jpg,jpeg,doc,docx,pdf'])!!}
                                                    {!! Form::file('payslip2',['class'=>'form-control','data-toggle'=>'tooltip','title'=>'png,gif,jpg,jpeg,doc,docx,pdf','accept'=>'image/x-png,image/gif,image/jpeg,.doc,.docx,.pdf']) !!}
                                                    <span class="error" for="payslip2"></span>
                                                    <div class="payslip2_image" style="display: none">
                                                        <a href="" download
                                                           class="img-responsive btn btn-primary">
                                                            <i class="fa fa-paperclip"></i>
                                                        </a>
                                                        <button type="button"
                                                                class="btn btn-danger delete-btn removeImage"
                                                                data-index="payslip2">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 jq__client">
                                                <div class="form-group">
                                                    {!!Form::label('address','Address *')!!}
                                                    {!!Form::textarea('address', old('address'), ['class'=>'form-control','placeholder'=>'Address','rows'=>3])!!}
                                                    <span class="error" for="address"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-4 jq__client">
                                                <div class="form-group">
                                                    {!!Form::label('Address Proof *','Address Proof *',['data-toggle'=>'tooltip','title'=>'png,gif,jpg,jpeg,doc,docx,pdf'])!!}
                                                    {!! Form::file('address_proof',['class'=>'form-control','data-toggle'=>'tooltip','title'=>'png,gif,jpg,jpeg,doc,docx,pdf','accept'=>'image/x-png,image/gif,image/jpeg,.doc,.docx,.pdf']) !!}
                                                    <span class="error" for="address_proof"></span>
                                                    <div class="address_proof_image" style="display: none">
                                                        <a href="" download
                                                           class="img-responsive btn btn-primary">
                                                            <i class="fa fa-paperclip"></i>
                                                        </a>
                                                        <button type="button"
                                                                class="btn btn-danger delete-btn removeImage"
                                                                data-index="address_proof">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </div>
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
                                                <div class="col-md-4 jq__not_client jq_branch">
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
                                                <div class="col-md-4 jq__not_client jq_branch">
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
                                                <div class="col-md-4 jq__not_client jq_branch">
                                                    <div class="form-group">
                                                        {!!Form::label('branch[]','Branch')!!} <span
                                                                class="jq__estrick"></span>
                                                        {!!Form::select('branch[]',[],'',['class'=>'form-control','id'=>'branch_id','multiple'])!!}
                                                        <span class="error" for="branch[]"></span>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="col-md-4 jq__client">
                                                <div class="form-group">
                                                    <label class="control-label">Sex</label><br>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="sex" value="1" checked>Male
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="sex" value="2">Female
                                                    </label>
                                                    <span class="error" for="sex"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-12"></div>
                                            <div class="col-md-4 jq__client">
                                                <div class="form-group">
                                                    {!!Form::label('dob','Date of Birth *')!!}
                                                    {!!Form::text('dob', '', ['class'=>'form-control old-date-picker','placeholder'=>'Date of Birth'])!!}
                                                    <span class="error" for="dob"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-4 jq__client">
                                                <div class="form-group">
                                                    <label class="control-label">Place of Birth *</label>
                                                    {!!Form::text('place_of_birth', '', ['class'=>'form-control','placeholder'=>'Place Of Birth'])!!}
                                                    <span class="error" for="place_of_birth"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-4 jq__client">
                                                <div class="form-group ">
                                                    {!!Form::label('civil_status','Civil Status *')!!}
                                                    {!!Form::select('civil_status', $civil_statues,'', ['class'=>'form-control select2single','placeholder'=>'Select civil status','id'=>'civil_status_select'])!!}
                                                    <span class="error" for="civil_status"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-12"></div>
                                            <div class="col-md-4 jq__client">
                                                <div class="form-group">
                                                    {!!Form::label('spouse_first_name','Spouse First Name(s) *')!!}
                                                    {!!Form::text('spouse_first_name','', ['class'=>'form-control','placeholder'=>'Spouse First Name(s)'])!!}
                                                    <span class="error" for="spouse_first_name"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-4 jq__client">
                                                <div class="form-group">
                                                    {!!Form::label('spouse_last_name','Spouse Last Name(s) *')!!}
                                                    {!!Form::text('spouse_last_name', '', ['class'=>'form-control','placeholder'=>'Spouse Last Name(s)'])!!}
                                                    <span class="error" for="spouse_last_name"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-12"></div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    {!!Form::label('id_number','ID Number *')!!}
                                                    {!!Form::text('id_number', old('id_number'), ['class'=>'form-control','placeholder'=>'ID Number'])!!}
                                                    <span class="error" for="id_number"></span>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label" id="exp_date">Exp. Date
                                                        *</label>
                                                    {!!Form::text('exp_date', '', ['class'=>'form-control date-picker','placeholder'=>'Exp. Date'])!!}
                                                    <span class="error" for="exp_date"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-12"></div>
                                            <div class="col-md-6 jq__client">
                                                <div class="form-group">
                                                    {!!Form::label('pp_number','Passport Number')!!}
                                                    {!!Form::text('pp_number', '', ['class'=>'form-control','placeholder'=>'Passport Number'])!!}
                                                    <span class="error" for="pp_number"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 jq__client">
                                                <div class="form-group">
                                                    {!!Form::label('pp_exp_date','Exp. Date')!!}
                                                    {!!Form::text('pp_exp_date', '', ['class'=>'form-control date-picker','placeholder'=>'Exp. Date'])!!}
                                                    <span class="error" for="pp_exp_date"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-12"></div>
                                            <div class="col-md-6 jq__not_client">
                                                <div class="form-group">
                                                    {!!Form::label('commission','Commission (%)')!!}
                                                    {!!Form::number('commission', '', ['class'=>'form-control','min'=>'0.01','max'=>'100','step'=>'0.01','placeholder'=>'Commission'])!!}
                                                    <span class="error" for="commission"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 status-group">
                                                <div class="form-group">
                                                    {!!Form::label('status','Status *')!!}
                                                    <select name="status" class="form-control select2single">
                                                        @foreach($statuses as $item)
                                                            <option value="{{$item->id}}"
                                                                    data-role="{!! $item->role !!}">
                                                                {{$item->title}}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <span class="error" for="status"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 jq__client">
                                                <div class="form-group">
                                                    {!!Form::label('Upload scan ID *','Upload scan ID *',['data-toggle'=>'tooltip','title'=>'png,gif,jpg,jpeg,doc,docx,pdf'])!!}
                                                    {!! Form::file('scan_id',['class'=>'form-control','data-toggle'=>'tooltip','title'=>'png,gif,jpg,jpeg,doc,docx,pdf','accept'=>'image/x-png,image/gif,image/jpeg,.doc,.docx,.pdf']) !!}
                                                    <span class="error" for="scan_id"></span>
                                                    <div class="scan_id_image" style="display: none;">
                                                        <a href="" download
                                                           class="img-responsive btn btn-primary">
                                                            <i class="fa fa-paperclip"></i>
                                                        </a>
                                                        <button type="button"
                                                                class="btn btn-danger delete-btn removeImage"
                                                                data-index="scan_id">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12"></div>
                                            <div class="col-md-12">
                                                <h2>Other Documents</h2>
                                                <div class="text-right">
                                                    <button type="button" class="addNewOtherDocument btn btn-primary">
                                                        <i class="fa fa-plus"></i>
                                                    </button>
                                                </div>
                                                <div class="form-group">
                                                    <table class="table table-bordered table-user">
                                                        <thead>
                                                        <tr>
                                                            <th width="40%;">File</th>
                                                            <th width="40%;">Name</th>
                                                            <th width="20%;">Action</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody id="other_document_table"></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-md-6 jq__client">
                                                        <h2>Cellphone <span style="color: red;">*</span></h2>
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
                                                    <p class="help-block" style="color:red;"
                                                       id="phone_error"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    {{--<div class="text-right mt-3">
                                        <button class="btn btn-primary saveButtonSteps" data-step="0">
                                            Save And Close
                                        </button>
                                    </div>--}}
                                </div>
                                <div class="tab-pane" id="tab2">
                                    <div class="text-center" id="main_message_step2" style="color:red;"></div>
                                    <div class="text-center">
                                        <label>{!! Form::radio('working_type','1',true) !!} Working</label>
                                        <label style="padding-left:10px;">
                                            {!! Form::radio('working_type','2',false) !!}Pension
                                        </label>
                                    </div>
                                    <div class="text-right mb-4">
                                        <button type="button" class="btn btn-primary addUserWorkInfo">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                    <table class="table table-bordered table-user">
                                        <thead>
                                        <tr>
                                            <th>Employer</th>
                                            <th>Position</th>
                                            <th>Employed Since</th>
                                            <th>Contract Expires</th>
                                            <th>action</th>
                                        </tr>
                                        </thead>
                                        <tbody class="tableWorkInfo"></tbody>
                                    </table>
                                    {{--<div class="text-right mt-3">
                                        <button class="btn btn-primary saveButtonSteps" data-step="1">
                                            Save And Close
                                        </button>
                                    </div>--}}
                                </div>
                                <div class="tab-pane" id="tab3">
                                    <form id="usersBankInfoForm">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group{{ $errors->has('how_much_loan') ? ' has-danger' : '' }}">
                                                    <label for="how_much_loan" class="control-label">@lang('keywords.how_much_loan')</label>
                                                    <input id="how_much_loan" autocomplete="off" type="number" class="form-control" min="0" name="how_much_loan"
                                                           value="{{ old('how_much_loan') }}"
                                                           autofocus>
                                                    @if ($errors->has('how_much_loan'))
                                                        <span class="help-block error">
                                                            <strong>{!!  $errors->first('how_much_loan')  !!}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group{{ $errors->has('repay_loan_2_weeks') ? ' has-danger' : '' }}">
                                                    <label for="repay_loan_2_weeks" class="control-label">@lang('keywords.repay_loan_2_weeks')</label>
                                                    {!! Form::select('repay_loan_2_weeks',$options,old('repay_loan_2_weeks'),['class'=>'form-control select2single','placeholder'=>\Illuminate\Support\Facades\Lang::get('keywords.select_options')]) !!}
                                                    @if ($errors->has('repay_loan_2_weeks'))
                                                        <span class="help-block error">
                                                            <strong>{!!  $errors->first('repay_loan_2_weeks')  !!}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group{{ $errors->has('have_bank_loan') ? ' has-danger' : '' }}">
                                                    <label for="have_bank_loan" class="control-label">@lang('keywords.have_bank_loan')</label>
                                                    {!! Form::select('have_bank_loan',$options,old('have_bank_loan'),['class'=>'form-control select2single','placeholder'=>\Illuminate\Support\Facades\Lang::get('keywords.select_options')]) !!}
                                                    @if ($errors->has('have_bank_loan'))
                                                        <span class="help-block error">
                                                            <strong>{!!  $errors->first('have_bank_loan')  !!}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group{{ $errors->has('have_bank_account') ? ' has-danger' : '' }}">
                                                    <label for="have_bank_account" class="control-label">@lang('keywords.have_bank_account')</label>
                                                    {!! Form::select('have_bank_account',$options,old('have_bank_account'),['class'=>'form-control select2single','placeholder'=>\Illuminate\Support\Facades\Lang::get('keywords.select_options')]) !!}
                                                    @if ($errors->has('have_bank_account'))
                                                        <span class="help-block error">
                                                            <strong>{!!  $errors->first('have_bank_account')  !!}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-right mt3 mb-4">
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
                                        <button class="btn btn-primary saveButtonSteps" data-step="2">
                                            Save And Close
                                        </button>
                                    </div>--}}
                                </div>
                                <div class="tab-pane" id="tab4">
                                    <form id="usersReferenceInfoForm" action="">
                                        <div class="row">
                                            <div class="col-md-6 jq__client">
                                                <div class="form-group" id="js--users-referred_by">

                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right mb-4">
                                            <button type="button" class="btn btn-primary addReferrenceButton"><i class="fa fa-plus"></i></button>
                                        </div>
                                        <table class="table table-bordered table-user">
                                            <thead>
                                            <tr>
                                                <th>First Name(s) *</th>
                                                <th>Last Name(s) *</th>
                                                <th>Relationship *</th>
                                                <th>Tel Number</th>
                                                <th>Cel Number *</th>
                                                <th>Address *</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody class="tableReferenceInfo"></tbody>
                                        </table>
                                        <input type="hidden" name="id" value="0">
                                    </form>
                                    {{--<div class="text-right mt-3">
                                        <button class="btn btn-primary saveButtonSteps" data-step="3">
                                            Save And Close
                                        </button>
                                    </div>--}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin1.popups.user_work_info_form')
@stop
@section('contentFooter')
    <script src="{!! asset(mix('resources/js/admin/usersCreate.js')) !!}"></script>
    <script>
        var type = '';
        var user_id = '';
        var has_admin = 0;
    </script>
    @if(auth()->user()->hasRole('super admin|admin'))
        <script>
            has_admin ={!! auth()->user()->hasRole('super admin|admin') !!};
        </script>
    @endif
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
        usersCreate.init();
    </script>
@stop