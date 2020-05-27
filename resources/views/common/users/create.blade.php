@extends('admin.layouts.app')
@section('page_name')
    @if(isset($edit_user))
        Users Edit
    @else
        Users Create
    @endif
@stop
@section('extra-styles')
    <link href="{{url(config('theme.admin.plugins').'/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet"
          type="text/css"/>
    <link href="{{url(config('theme.admin.plugins').'/datatables/buttons.bootstrap4.min.css')}}" rel="stylesheet"
          type="text/css"/>
    <link href="{{url(config('theme.admin.plugins').'/datatables/responsive.bootstrap4.min.css')}}" rel="stylesheet"
          type="text/css"/>
    <link href="{{url(config('theme.admin.plugins').'/jquery.steps/css/jquery.steps.css')}}" rel="stylesheet"
          type="text/css"/>
    <link href="{{url(config('theme.front.plugins'))}}/bootstrap-datepicker/css/bootstrap-datepicker.min.css"
          rel="stylesheet">
    <link href="{{url(config('theme.front.plugins'))}}/bootstrap-daterangepicker/daterangepicker.css"
          rel="stylesheet">
    <style>
        .table-user tbody td {
            border-color: black;
        }

        .table-user thead th {
            border-color: black;
            border-bottom: 1px solid black !important;
        }

        .wizard > .content > .body input[type="radio"] {
            border: none !important;
        }

        .country_code_label {
            background-color: ghostwhite;
        }

        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <h4 class="page-title">Users</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="card-box">
                <div id="wizard-validation-form" action="#">
                    <div>
                        <h3>PERSONAL INFORMATION</h3>
                        <section>
                            <form id="usersInfoForm" action="{!! url('ajax/users') !!}">
                                <input type="hidden" name="id">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Last Name(s) *</label>
                                            <input type="text" name="lastname" class="form-control"
                                                   placeholder="e.g Smith" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">First Name(s) *</label>
                                            <input type="text" name="firstname" class="form-control"
                                                   placeholder="e.g John" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Email Address *</label>
                                            <input type="email" name="email" class="form-control"
                                                   placeholder="e.g johnsmith@gmail.com" required>
                                            <span class="help-block" style="color:red;" id="email_error"></span>
                                        </div>
                                        @if(isset($edit_user) && ($edit_user->is_verified==0||$edit_user->is_verified==null))
                                            <div class="text-left">
                                                <a class="btn btn-default"
                                                   href="{!! url('resend/' . $edit_user->id . '/verification') !!}">
                                                    Resend Verification Mail
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!!Form::label('lang','Language *')!!}
                                            {!!Form::select('lang', $lang,'', ['class'=>'form-control','required','placeholder'=>'Select Language','id'=>'lang'])!!}
                                        </div>
                                    </div>
                                    <div class="col-md-12 jq__client">
                                        <h2>Secondary Emails</h2>
                                        <div class="text-right">
                                            <button class="addNewEmail btn btn-primary">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                        <div class="form-group">
                                            <table class="table table-bordered table-user">
                                                <thead>
                                                <tr>
                                                    <th>Email</th>
                                                    <th>Action</th>
                                                </tr>
                                                </thead>
                                                <tbody id="user_emails">

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Role *</label>
                                            <select name="role_id" class="form-control"
                                                    onchange="toggleStatus(this)">
                                                @foreach($roles as $role)
                                                    @if($role->id == '3')
                                                        <option selected value="{{$role->id}}">{{$role->name}}</option>
                                                    @else
                                                        <option value="{{$role->id}}">{{$role->name}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4 jq__client">
                                        <div class="form-group">
                                            <label class="control-label" data-toggle="tooltip"
                                                   title="png,gif,jpg,jpeg,doc,docx,pdf">Payslip1 *</label>
                                            <input type="file" id="payslip1" name="payslip1" data-toggle="tooltip"
                                                   title="png,gif,jpg,jpeg,doc,docx,pdf"
                                                   class="form-control"
                                                   accept="image/png,image/gif,image/jpeg,image/jpg,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf" required>
                                            <input type="hidden" name="removePayslip1">
                                            <div class="payslip1-holder" style="display: none;">
                                                <a href="" download class="img-responsive btn btn-primary">
                                                    <i class="fa fa-paperclip"></i>
                                                </a>
                                                <button type="button" onclick="removePay1(this)"
                                                        class="btn btn-danger delete-btn">
                                                    <i class="fa fa-trash"></i>
                                                    {{--Delete playsilp1--}}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 jq__client">
                                        <div class="form-group">
                                            <label class="control-label" data-toggle="tooltip"
                                                   title="png,gif,jpg,jpeg,doc,docx,pdf">Payslip2 *</label>
                                            <input type="file" id="payslip2" name="payslip2" data-toggle="tooltip"
                                                   title="png,gif,jpg,jpeg,doc,docx,pdf"
                                                   class="form-control"
                                                   accept="image/png,image/gif,image/jpeg,image/jpg,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf" required>
                                            <input type="hidden" name="removePayslip2">
                                            <div class="payslip2-holder" style="display: none">
                                                <a href="" download class="img-responsive btn btn-primary">
                                                    <i class="fa fa-paperclip"></i>
                                                </a>
                                                <button type="button" onclick="removePay2(this)"
                                                        class="btn btn-danger delete-btn">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 jq__client">
                                        <div class="form-group">
                                            {!!Form::label('address','Address *')!!}
                                            {!!Form::textarea('address', '', ['class'=>'form-control','placeholder'=>'Address','required','rows'=>3])!!}
                                        </div>
                                    </div>
                                    <div class="col-md-4 jq__client">
                                        <div class="form-group">
                                            <label class="control-label" data-toggle="tooltip"
                                                   title="png,gif,jpg,jpeg,doc,docx,pdf">Address Proof *</label>
                                            <input type="file" id="address_proof" name="address_proof"
                                                   data-toggle="tooltip" title="png,gif,jpg,jpeg,doc,docx,pdf"
                                                   class="form-control"
                                                   accept="image/png,image/gif,image/jpeg,image/jpg,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf" required>
                                            <input type="hidden" name="removeAddressProof">
                                            <div class="address-proof-holder" style="display: none">
                                                <a href="" download class="img-responsive btn btn-primary">
                                                    <i class="fa fa-paperclip"></i>
                                                </a>
                                                <button type="button" onclick="removeAddressProo(this)"
                                                        class="btn btn-danger delete-btn">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    @if(!auth()->user()->hasRole('super admin'))
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                {!!Form::label('country','Country *')!!}
                                                {!!Form::select('country', $countries,auth()->user()->country, ['class'=>'form-control','required','placeholder'=>'Select Country','disabled','id'=>'country_id'])!!}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                {!!Form::label('territory','District')!!}
                                                {!!Form::select('territory',$territories,auth()->user()->territory,['class'=>'form-control','required','placeholder'=>'District'])!!}
                                                @if($errors->has('territory'))
                                                    <p class="help-block">{!!$errors->first('territory')!!}</p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4 jq__not_client jq_branch">
                                            <div class="form-group">
                                                {!!Form::label('branch','Branch')!!}
                                                {!!Form::select('branch[]',$branches,auth()->user()->userBranches->pluck('id'),['class'=>'form-control','placeholder'=>'Branch','multiple'])!!}
                                                @if($errors->has('branch'))
                                                    <p class="help-block">{!!$errors->first('branch')!!}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @elseif(auth()->user()->hasRole('super admin') && session()->has('country'))
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                {!!Form::label('country','Country *')!!}
                                                {!!Form::select('country', $countries,session()->get('country'), ['class'=>'form-control','required','placeholder'=>'Select Country','disabled','id'=>'country_id'])!!}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                {!!Form::label('territory','District')!!}
                                                {!!Form::select('territory',$territories,'',['class'=>'form-control','placeholder'=>'District'])!!}
                                                @if($errors->has('territory'))
                                                    <p class="help-block">{!!$errors->first('territory')!!}</p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4 jq__not_client jq_branch">
                                            <div class="form-group">
                                                {!!Form::label('branch','Branch')!!}
                                                {!!Form::select('branch[]',$branches,'',['class'=>'form-control','placeholder'=>'Branch','multiple'])!!}
                                                @if($errors->has('branch'))
                                                    <p class="help-block">{!!$errors->first('branch')!!}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                {!!Form::label('country','Country *')!!}
                                                {!!Form::select('country', $countries,'', ['class'=>'form-control','required','placeholder'=>'Select Country','id'=>'country_id'])!!}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label">District</label>
                                                <select name="territory" class="form-control" id="territory_id"
                                                >
                                                    <option value=''>Select District</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 jq__not_client jq_branch">
                                            <div class="form-group">
                                                <label class="control-label">Branch <span class="jq__estrick"></label>
                                                <select name="branch[]" multiple class="form-control" id="branch_id">
                                                    <option value=''>Select Branch</option>
                                                </select>
                                            </div>
                                            <div class="error">
                                              <span class="help-block" for="branch">
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
                                        </div>
                                    </div>
                                    <div class="col-md-4 jq__client">
                                        <div class="form-group">
                                            {!!Form::label('dob','Date of Birth *')!!}
                                            {!!Form::text('dob', '', ['class'=>'form-control old-date-picker','placeholder'=>'Date of Birth','required'])!!}
                                        </div>
                                    </div>
                                    <div class="col-md-4 jq__client">
                                        <div class="form-group">
                                            <label class="control-label">Place of Birth *</label>
                                            {!!Form::text('place_of_birth', '', ['class'=>'form-control','placeholder'=>'Place Of Birth','required'])!!}
                                        </div>
                                    </div>

                                    <div class="col-md-4 jq__client">
                                        <div class="form-group ">
                                            {!!Form::label('civil_status','Civil Status *')!!}
                                            {!!Form::select('civil_status', ['1'=>'Single','2'=>'Married'],'', ['class'=>'form-control','required'])!!}
                                        </div>
                                    </div>
                                    <div class="col-md-4 jq__client">
                                        <div class="form-group">
                                            {!!Form::label('spouse_first_name','Spouse First Name(s) *')!!}
                                            {!!Form::text('spouse_first_name','', ['class'=>'form-control','placeholder'=>'Spouse First Name(s)'])!!}
                                        </div>
                                    </div>
                                    <div class="col-md-4 jq__client">
                                        <div class="form-group">
                                            {!!Form::label('spouse_last_name','Spouse Last Name(s) *')!!}
                                            {!!Form::text('spouse_last_name', '', ['class'=>'form-control','placeholder'=>'Spouse Last Name(s)'])!!}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">ID Number *</label>
                                            <input type="text" class="form-control" name="id_number"
                                                   placeholder="ID Number" required>
                                            <span class="help-block" style="color:red;" id="id_number_error"></span>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" id="exp_date">Exp. Date *</label>
                                            {!!Form::text('exp_date', '', ['class'=>'form-control date-picker','required','placeholder'=>'Exp. Date'])!!}
                                        </div>
                                    </div>
                                    <div class="col-md-6 jq__client">
                                        <div class="form-group">
                                            {!!Form::label('pp_number','Passport Number')!!}
                                            {!!Form::text('pp_number', '', ['class'=>'form-control','placeholder'=>'Passport Number'])!!}
                                        </div>
                                    </div>
                                    <div class="col-md-6 jq__client">
                                        <div class="form-group">
                                            {!!Form::label('pp_exp_date','Exp. Date')!!}
                                            {!!Form::text('pp_exp_date', '', ['class'=>'form-control date-picker','placeholder'=>'Exp. Date'])!!}
                                        </div>
                                    </div>
                                    <div class="col-md-6 status-group">
                                        <div class="form-group">
                                            <label class="control-label">Status *</label>
                                            <select name="status" class="form-control" required>
                                                @foreach($status as $item)
                                                    <option value="{{$item->id}}" data-role="{!! $item->role !!}">
                                                        {{$item->title}}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    {{--<div class="col-md-6 jq__client">
                                        <div class="form-group">
                                            <label class="control-label" data-toggle="tooltip" title="png,gif,jpg,jpeg">Profile
                                                Pic</label>
                                            <input type="file" id="profile_pic_user" name="profile_pic"
                                                   data-toggle="tooltip" title="png,gif,jpg,jpeg"
                                                   class="form-control" accept="image/png,image/gif,image/jpeg,image/jpg,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf">
                                            <input type="hidden" name="removeImage">
                                        </div>
                                    </div>--}}
                                    {{--<div class="col-md-6">--}}
                                    {{--<div class="form-group">--}}
                                    {{--<label for="control-label">--}}
                                    {{--<label class="control-label">Preferred Language</label>--}}
                                    {{--{!!Form::label('language','Preferred Language')!!}--}}
                                    {{--{!!Form::select('language',$languages,old('language'),['class'=>'form-control','placeholder'=>'Preferred Language'])!!}--}}
                                    {{--</label>--}}
                                    {{--</div>--}}
                                    {{--</div>--}}
                                    {{--<div class="profile-pic-holder col-md-6" style="display: none">
                                        <div class="form-group loan-image thumb-small">
                                            <span class="helper"></span>
                                            <img src="" class="img-responsive">
                                            <button type="button" onclick="removeProfilePic(this)"
                                                    class="btn btn-danger delete-btn">Delete image
                                            </button>
                                        </div>
                                    </div>--}}
                                    <div class="col-md-6 jq__client">
                                        <div class="form-group">
                                            <label class="control-label" data-toggle="tooltip"
                                                   title="png,gif,jpg,jpeg,doc,docx,pdf">Upload scan ID *</label>
                                            <input type="file" id="scan_id" name="scan_id" data-toggle="tooltip"
                                                   title="png,gif,jpg,jpeg,doc,docx,pdf"
                                                   class="form-control"
                                                   accept="image/png,image/gif,image/jpeg,image/jpg,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf" required>
                                            <input type="hidden" name="removeScanId">
                                            <div class="scan-id-holder" style="display: none">
                                                <a href="" download class="img-responsive btn btn-primary">
                                                    <i class="fa fa-paperclip"></i>
                                                </a>
                                                <button type="button" onclick="removeScanI(this)"
                                                        class="btn btn-danger delete-btn"><i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- <div class="col-md-6">
                                         <div class="form-group">
                                             <label class="control-label" data-toggle="tooltip"
                                                    title="png,gif,jpg,jpeg,doc,docx,pdf">Other Document</label>
                                             <input type="file" id="other_document" name="other_document"
                                                    data-toggle="tooltip" title="png,gif,jpg,jpeg,doc,docx,pdf"
                                                    class="form-control"
                                                    accept="image/png,image/gif,image/jpeg,image/jpg,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf">
                                             <input type="hidden" name="removeOtherDocument">
                                         </div>
                                     </div>
                                     <div class="other-document-holder col-md-6" style="display: none">
                                         <div class="form-group loan-image thumb-small">
                                             <span class="helper"></span>
                                             <img src="" class="img-responsive">
                                             <button type="button" onclick="removeOtherDocum(this)"
                                                     class="btn btn-danger delete-btn">Delete image
                                             </button>
                                         </div>
                                     </div>--}}
                                    <div class="col-md-12">
                                        <h2>Other Documents</h2>
                                        <div class="text-right">
                                            <button class="addNewOtherDocument btn btn-primary">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                        <div class="form-group">
                                            <table class="table table-bordered table-user">
                                                <thead>
                                                <tr>
                                                    <th>File</th>
                                                    <th>Name</th>
                                                    <th>Action</th>
                                                </tr>
                                                </thead>
                                                <tbody id="other_document_table"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="has-error">
                                            <p class="help-block" style="color:red;" id="phone_error"></p>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 jq__client">
                                                <h2>Cellphone</h2>
                                                <div class="text-right">
                                                    <button class="addNewCellphone btn btn-primary">
                                                        <i class="fa fa-plus"></i>
                                                    </button>
                                                </div>
                                                <div class="form-group">
                                                    <table class="table table-bordered table-user">
                                                        <thead>
                                                        <tr>
                                                            <th>Cellphone</th>
                                                            <th>Action</th>
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
                                                    <button class="addNewTelephone btn btn-primary">
                                                        <i class="fa fa-plus"></i>
                                                    </button>
                                                </div>
                                                <div class="form-group">
                                                    <table class="table table-bordered table-user">
                                                        <thead>
                                                        <tr>
                                                            <th>Telephone</th>
                                                            <th>Action</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody id="user_telephones">

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{--<div class="form-group clearfix">
                                        <label class="col-lg-12 control-label ">(*) Mandatory</label>
                                    </div>--}}
                                </div>
                            </form>
                            {{--<div class="text-right mt-3">
                                <button class="btn btn-primary saveButtonSteps" data-step="0">Save And Close</button>
                            </div>--}}
                        </section>
                        <h3>EMPLOYMENT INFORMATION</h3>
                        <section>
                            <div class="text-center" id="main_message_step2" style="color:red;"></div>
                            <div class="text-center">
                                <label>{!! Form::radio('working_type','1',true) !!} Working</label>
                                <label style="padding-left:10px;">{!! Form::radio('working_type','2',false) !!}
                                    Pension</label>
                            </div>
                            <div class="text-right mb-4">
                                <button class="btn btn-primary addUserWorkInfo">
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
                                <button class="btn btn-primary saveButtonSteps" data-step="1">Save And Close</button>
                            </div>--}}
                        </section>
                        <h3>BANK INFORMATION</h3>
                        <section>
                            <form id="usersBankInfoForm" action="">
                                <div class="text-right mb-4">
                                    <button class="btn btn-primary addUserBankInfo">
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
                        </section>
                        <h3>REFERENCE INFORMATION</h3>
                        <section>
                            <form id="usersReferenceInfoForm" action="">
                                {{-- <div class="text-right mb-4">
                                     <button class="btn btn-primary addUserReferenceInfo">
                                         <i class="fa fa-plus"></i>
                                     </button>
                                 </div>--}}
                                <table class="table table-bordered table-user">
                                    <thead>
                                    <tr>
                                        <th>First Name(s) *</th>
                                        <th>Last Name(s) *</th>
                                        <th>Relationship *</th>
                                        <th>Tel Number</th>
                                        <th>Cel Number *</th>
                                        <th>Address *</th>
                                        {{--<th>Action</th>--}}
                                    </tr>
                                    </thead>
                                    <tbody class="tableReferenceInfo"></tbody>
                                </table>
                            </form>
                            {{--<div class="text-right mt-3">
                                <button class="btn btn-primary saveButtonSteps" data-step="3">Save And Close</button>
                            </div>--}}
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('common.users.user_work_modal')
@endsection

@section('extra-js')
    <script src="{{url(config('theme.admin.plugins').'/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{url(config('theme.admin.plugins').'/datatables/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{url(config('theme.admin.plugins').'/datatables/dataTables.responsive.min.js')}}"></script>
    <script src="{{url(config('theme.admin.plugins').'/datatables/responsive.bootstrap4.min.js')}}"></script>
    <script src="{{url(config('theme.admin.plugins').'/jquery-validation/js/jquery.validate.min.js')}}"></script>
    <script src="{{url(config('theme.admin.plugins').'/jquery.steps/js/jquery.steps.min.js')}}"></script>
    <script src="{{url(config('theme.front.plugins'))}}/moment/moment.js"></script>
    <script src="{{url(config('theme.front.plugins'))}}/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
    <script src="{{url(config('theme.front.plugins'))}}/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script src="{!! asset('js/throttle.js') !!}"></script>
@endsection

@section('custom-js')
    @if(isset($relationships))
        <script>
            var relationships ={!! json_encode($relationships) !!};
        </script>
    @endif
    <script type="text/javascript">
        var steps_init = 0;
        var steps = '';

        function datePickerInit() {
            var startDate = new Date();
            startDate.setDate(startDate.getDate() + 1);

            $('.date-picker').datepicker({
                orientation: "bottom auto",
                clearBtn: true,
                startDate: startDate,
                autoclose: true,
                format: dateFormat
            });
            $('.old-date-picker').datepicker({
                orientation: "bottom auto",
                clearBtn: true,
                endDate: startDate,
                autoclose: true,
                format: dateFormat
            });
            $('.all-date-picker').datepicker({
                orientation: "bottom auto",
                clearBtn: true,
                autoclose: true,
                format: dateFormat
            });
        }

        var init_steps = false;

        function wizardInit(type) {
            if (steps_init == 1) {
                steps.steps('destroy');
            }

            steps_init = 1;

            var enableAllSteps = false;
            if (type == 'edit') {
                enableAllSteps = true;
                init_steps = false;
            }

            var process = false;

            /* $(document).on("click", "div.actions.clone_button>ul>li>a", $.debounce(1000,function (e) {

                 var $element = $(this);

                 // console.log($element);

                 if ($element.attr("href") == '#next' && !$element.parent().hasClass('disabled')) {
                     steps.steps('next');
                 }

                 if ($element.attr("href") == '#previous' && !$element.parent().hasClass('disabled')) {
                     steps.steps('previous');
                 }

             }));*/


            steps = $("#wizard-validation-form").children("div").steps({
                headerTag: "h3",
                bodyTag: "section",
                transitionEffect: "slideLeft",
                enableAllSteps: enableAllSteps,
                onInit: function () {
                    if (init_steps == false) {
                        $("div.actions").insertBefore("div.content.clearfix");
                        // $("div.actions").clone().addClass('clone_button').insertAfter("div.content.clearfix");
                        if (type == 'edit') {
                            var count = $("div.actions").length;
                            for (var i = 0; i < count - 2; i++) {
                                $("div.actions")[i].remove();
                            }
                        }
                        init_steps = true;
                    }
                },
                onStepChanging: function (event, currentIndex, newIndex) {
                    $('#phone_error').text('');
                    if (currentIndex > newIndex) {
                        return true;
                    } else {
                        if (process) {
                            process = false;
                            return true;
                        } else {
                            if (currentIndex == 0) {
                                if ($('#usersInfoForm').valid()) {
                                    console.log("Inside form");
                                    fullLoader.on({
                                        text: 'Loading !'
                                    });
                                    var custom_data = new FormData($('#usersInfoForm')[0]);
                                    $.ajax({
                                        dataType: 'json',
                                        method: 'post',
                                        url: $('#usersInfoForm').attr('action'),
                                        data: custom_data,
                                        processData: false,
                                        contentType: false,
                                        cache: false,
                                        crossDomain: true,
                                        success: function (data) {
                                            if (data['status']) {
                                                process = true;
                                                $('#usersInfoForm').attr('action', ajaxURL + 'users/' + data['user_id']);
                                                $('#usersInfoForm').data('user-id', data['user_id']);
                                                $('#usersWorkInfoForm').attr('action', ajaxURL + 'users/' + data['user_id'] + '/works');
                                                $('#usersWorkInfoForm').data('user-id', data['user_id']);
                                                $('#usersBankInfoForm').attr('action', ajaxURL + 'users/' + data['user_id'] + '/banks');
                                                $('#usersBankInfoForm').data('user-id', data['user_id']);
                                                $('#usersReferenceInfoForm').attr('action', ajaxURL + 'users/' + data['user_id'] + '/references');
                                                $('#usersReferenceInfoForm').data('user-id', data['user_id']);
                                                if (data['role_id'] == 3) {
                                                    if ($("#wizard-validation-form").data('process') == 'save') {
                                                        window.location = siteURL + 'users';
                                                    } else {
                                                        var counter = newIndex - currentIndex;
                                                        for (var i = 0; i < counter; i++) {
                                                            steps.steps('next');
                                                        }
                                                    }
                                                } else {
                                                    window.location = siteURL + 'users';
                                                }
                                            } else {
                                                if (data['type'] == 'email') {
                                                    $('#email_error').text(data['message']);
                                                }
                                                if (data['type'] == 'phone') {
                                                    $('#phone_error').text(data['message']);
                                                }
                                                if (data['type'] == 'id_number') {
                                                    $('#id_number_error').text(data['message']);
                                                }
                                            }
                                            fullLoader.off();
                                        },
                                        error: function (jqXHR, exception) {
                                            var Response = jqXHR.responseText;
                                            ErrorBlock = $($('#usersInfoForm'));
                                            Response = $.parseJSON(Response);
                                            DisplayErrorMessages(Response, ErrorBlock, 'input');
                                            fullLoader.off();
                                        }
                                    });
                                }
                            }
                            if (currentIndex == 1) {
                                fullLoader.on({
                                    text: 'Loading !'
                                });
                                if ($('[name="working_type"]:checked').val() == 1) {
                                    $.ajax({
                                        dataType: 'json',
                                        method: 'get',
                                        url: ajaxURL + 'users/' + $('#usersWorkInfoForm').data('user-id') + '/works',
                                        success: function (data) {
                                            if (data['works'] != undefined && data['works'].length > 0) {
                                                storeWorkingType(1);
                                                process = true;
                                                if ($("#wizard-validation-form").data('process') == 'save') {
                                                    window.location = siteURL + 'users';
                                                } else {
                                                    steps.steps('next');
                                                }
                                            } else {
                                                $('#main_message_step2').html('Employment information is required.');
                                            }
                                            fullLoader.off();
                                        }
                                    });
                                } else {
                                    $('#main_message_step2').html('');
                                    storeWorkingType(2);
                                    fullLoader.off();
                                    return true;
                                }
                            }
                            if (currentIndex == 2) {
                                fullLoader.on({
                                    text: 'Loading !'
                                });
                                if ($('#usersBankInfoForm').valid()) {
                                    $.ajax({
                                        dataType: 'json',
                                        method: 'post',
                                        url: $('#usersBankInfoForm').attr('action'),
                                        data: $('#usersBankInfoForm').serialize(),
                                        success: function (data) {
                                            fullLoader.off();
                                            if (data['status']) {
                                                process = true;
                                                if ($("#wizard-validation-form").data('process') == 'save') {
                                                    window.location = siteURL + 'users';
                                                } else {
                                                    steps.steps('next');
                                                }
                                            } else {
                                                str = '';
                                                var errors = data['errors'];
                                                $('.tableBankInfo').html(str);
                                                for (var index in data['inputs']['account_number']) {
                                                    var work = data['inputs'];
                                                    if (work['account_number'][index] == null) {
                                                        work['account_number'][index] = '';
                                                    }
                                                    if (work['bank_id'][index] == null) {
                                                        work['bank_id'][index] = '';
                                                    }
                                                    if (work['name_on_account'][index] == null) {
                                                        work['name_on_account'][index] = '';
                                                    }
                                                    if (work['address_on_account'][index] == null) {
                                                        work['address_on_account'][index] = '';
                                                    }
                                                    str += '<tr>\n' +
                                                        '     <td>\n';
                                                    str += '<select name="bank_id[]" class="form-control" required>';
                                                    for (var i in data['banks']) {
                                                        var selected = '';
                                                        if (i == work['bank_id'][index]) {
                                                            selected = 'selected';
                                                        }
                                                        str += '<option value="' + i + '" ' + selected + '>' + data['banks'][i] + '</option>';
                                                    }
                                                    str += '</select>';
                                                    if (errors['bank_id.' + index] != undefined) {
                                                        str += '          <label class="error">' + errors['bank_id.' + index] + '</label>';
                                                    }
                                                    str += '    </td>\n' +
                                                        '     <td>\n' +
                                                        '         <input class="form-control" type="text" name="account_number[]"\n' +
                                                        '                value="' + work['account_number'][index] + '"\n' +
                                                        '                placeholder="Account Number" required>\n';
                                                    if (errors['account_number.' + index] != undefined) {
                                                        str += '          <label class="error">' + errors['account_number.' + index] + '</label>';
                                                    }
                                                    str += '     </td>\n' +

                                                        '     <td>\n' +
                                                        '         <input class="form-control" type="text" name="name_on_account[]"\n' +
                                                        '                value="' + work['name_on_account'] + '"\n' +
                                                        '                placeholder="Name on Account" required>\n' +
                                                        '     </td>\n' +
                                                        '     <td>\n' +
                                                        '         <textarea class="form-control" type="text" name="address_on_account[]"\n' +
                                                        '                value=""\n' +
                                                        '                placeholder="Address On Account" required>' + work['address_on_account'] + '</textarea>\n' +
                                                        '     </td>\n' +
                                                        '     <td>\n' +
                                                        '         <button class="deleteWorkinfo btn btn-danger">\n' +
                                                        '             <i class="fa fa-trash"></i>\n' +
                                                        '         </button>\n' +
                                                        '     </td>\n' +
                                                        '  </tr>';
                                                }
                                                $('.tableBankInfo').append(str);
                                            }
                                        }
                                    });
                                }
                            }
                        }
                    }
                },
                onStepChanged: function (event, currentIndex, priorIndex) {
                    $('#wizard-validation-form').find('form')[0].reset();
                    $('#wizard-validation-form').find('form').find('input[name="id"]').val('');
                    $('#wizard-validation-form').find('input, select').removeAttr('disabled');
                    $('#wizard-validation-form').find('button[type="submit"]').show();
                    $('#wizard-validation-form').find('#deleteImage').show();
                    $('#wizard-validation-form').find('.profile-pic-holder').hide();
                    $('#wizard-validation-form').find('.profile-pic-holder').find('img').attr('src', '');
                    if (currentIndex == 0) {
                        setEdit($('#usersInfoForm').data('user-id'));
                    }
                    if (currentIndex == 1) {
                        getUserWorkInfo($('#usersWorkInfoForm').data('user-id'));
                    }
                    if (currentIndex == 2) {
                        getUserBankInfo($('#usersWorkInfoForm').data('user-id'));
                    }
                    if (currentIndex == 3) {
                        getUserReferenceInfo($('#usersWorkInfoForm').data('user-id'));
                    }
                },
                onFinishing: function (event, currentIndex) {
                    $.ajax({
                        dataType: 'json',
                        method: 'post',
                        url: $('#usersReferenceInfoForm').attr('action'),
                        data: $('#usersReferenceInfoForm').serialize(),
                        success: function (data) {
                            if (data['status']) {
                                window.location = siteURL + 'users';
                            } else {
                                str = '';
                                var errors = data['errors'];
                                $('.tableReferenceInfo').html(str);
                                for (var index in data['inputs']['first_name']) {
                                    var work = data['inputs'];
                                    if (work['first_name'][index] == null) {
                                        work['first_name'][index] = '';
                                    }
                                    if (work['last_name'][index] == null) {
                                        work['last_name'][index] = '';
                                    }
                                    if (work['telephone'][index] == null) {
                                        work['telephone'][index] = '';
                                    }
                                    if (work['relationship'][index] == null) {
                                        work['relationship'][index] = '';
                                    }
                                    if (work['cellphone'][index] == null) {
                                        work['cellphone'][index] = '';
                                    }
                                    if (work['address'][index] == null) {
                                        work['address'][index] = '';
                                    }

                                    str += '<tr>\n' +
                                        '     <td>\n' +
                                        '         <input class="form-control" type="text" name="first_name[]"\n' +
                                        '                value="' + work['first_name'][index] + '"\n' +
                                        '                placeholder="First Name" required>\n';
                                    if (errors['first_name.' + index] != undefined) {
                                        str += '          <label class="error">' + errors['first_name.' + index] + '</label>';
                                    }
                                    str += '     </td>\n';
                                    str += '     <td>\n' +
                                        '         <input class="form-control" type="text" name="last_name[]"\n' +
                                        '                value="' + work['last_name'][index] + '"\n' +
                                        '                placeholder="Last Name" required>\n';
                                    if (errors['last_name.' + index] != undefined) {
                                        str += '          <label class="error">' + errors['last_name.' + index] + '</label>';
                                    }
                                    str += '     </td>\n';
                                    str += '     <td>\n';
                                    str += '<select class="form-control" name="relationship[]" required>';
                                    str += '<option value="">Select Relationship</option>';
                                    for (var r_index in relationships) {
                                        if (r_index == work['relationship'][index]) {
                                            str += '<option selected value="' + r_index + '">' + relationships[r_index] + '</option>';
                                        } else {
                                            str += '<option value="' + r_index + '">' + relationships[r_index] + '</option>';
                                        }
                                    }
                                    str += '</select>';
                                    // str += '         <input class="form-control" type="text" name="relationship[]"\n' +
                                    //     '                value="' + work['relationship'][index] + '"\n' +
                                    //     '                placeholder="Relationship" required>\n';
                                    if (errors['relationship.' + index] != undefined) {
                                        str += '          <label class="error">' + errors['relationship.' + index] + '</label>';
                                    }
                                    str += '     </td>\n';
                                    str += '     <td>\n' +
                                        '         <div class="input-group">\n' +
                                        '            <span class="input-group-addon country_code_label">' + data['country_code'] + '</span>' +
                                        '         <input  onKeyDown="limitText(this,' + data['phone_length'] + ');" onKeyUp="limitText(this,' + data['phone_length'] + ');" minlength="' + data['phone_length'] + '" maxlength="' + data['phone_length'] + '" class="form-control" type="number" name="telephone[]"\n' +
                                        '                value="' + work['telephone'][index] + '"\n' +
                                        '                placeholder="Telephone" required></div>\n';
                                    if (errors['telephone.' + index] != undefined) {
                                        str += '          <label class="error">' + errors['telephone.' + index] + '</label>';
                                    }
                                    str += '     </td>\n';
                                    str += '     <td>\n' +
                                        '         <div class="input-group">\n' +
                                        '            <span class="input-group-addon country_code_label">' + data['country_code'] + '</span>' +
                                        '         <input  onKeyDown="limitText(this,' + data['phone_length'] + ');" onKeyUp="limitText(this,' + data['phone_length'] + ');" minlength="' + data['phone_length'] + '" maxlength="' + data['phone_length'] + '" class="form-control" type="number" name="cellphone[]"\n' +
                                        '                value="' + work['cellphone'][index] + '"\n' +
                                        '                placeholder="Cellphone" required></div>\n';
                                    if (errors['cellphone.' + index] != undefined) {
                                        str += '          <label class="error">' + errors['cellphone.' + index] + '</label>';
                                    }
                                    str += '     </td>\n';
                                    str += '     <td>\n' +
                                        '         <textarea class="form-control" type="text" name="address[]" ' +
                                        '                value=""\n' +
                                        '                placeholder="Address" required>' + work['address'][index] + '</textarea>\n';
                                    if (errors['address.' + index] != undefined) {
                                        str += '          <label class="error">' + errors['address.' + index] + '</label>';
                                    }
                                    str += '     </td>\n';
                                    str += '     <td>\n' +
                                        '         <button class="deleteWorkinfo btn btn-danger">\n' +
                                        '             <i class="fa fa-trash"></i>\n' +
                                        '         </button>\n' +
                                        '     </td>\n' +
                                        '  </tr>';
                                }
                                $('.tableReferenceInfo').append(str);
                            }
                        }
                    });
                },
                onFinished: function (event, currentIndex) {
                    window.location = siteURL + 'users';
                }
            });
            // var input = $('<li aria-hidden="false" aria-disabled="false"><a href="#next" role="menuitem">New Button</a></li>');
            //
            // input.appendTo($('ul[aria-label=Pagination]'));
        }

        $(document).on('click', '.saveButtonSteps', function (e) {
            e.preventDefault();
            if ($(this).data('step') == 3) {
                steps.steps('finish');
            } else {
                $("#wizard-validation-form").data('process', 'save');
                steps.steps('next');
            }
        });

        wizardInit('add');
        datePickerInit();

        function countryTerritory(country, territory_id) {
            if (territory_id == undefined) {
                territory_id = '';
            }
            if (country != '') {
                $.ajax({
                    dataType: 'json',
                    method: "get",
                    url: adminSiteURL + 'countries/' + country + '/territories',
                    success: function (data) {
                        str = '<option value="">Select District</option>';
                        for (var index in data['territories']) {
                            var territory = data['territories'][index];
                            if (territory_id == index) {
                                str += '<option value="' + index + '" selected>' + territory + '</option>';
                            } else {
                                str += '<option value="' + index + '">' + territory + '</option>';
                            }
                        }
                        $('#territory_id').html(str);
                    }
                });
            }
        }

        function countryBranch(country, territory_id) {
            if (territory_id == undefined) {
                territory_id = '';
            }
            if (country != '') {
                $.ajax({
                    dataType: 'json',
                    method: "get",
                    url: adminSiteURL + 'countries/' + country + '/branch',
                    success: function (data) {
                        str = '<option value="">Select Branch</option>';
                        for (var index in data['branches']) {
                            var branch = data['branches'][index];
                            if (territory_id == index) {
                                str += '<option value="' + index + '" selected>' + branch + '</option>';
                            } else {
                                str += '<option value="' + index + '">' + branch + '</option>';
                            }
                        }
                        $('#branch_id').html(str);
                    }
                });
            }
        }

        $(document).on('change', '#country_id', function (e) {
            e.preventDefault();
            countryTerritory($(this).val());
            countryBranch($(this).val());
        });

        $(document).on('click', '.addUserWorkInfo', function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'get',
                url: siteURL + 'users/' + $('#usersWorkInfoForm').data('user-id'),
                success: function (data) {
                    //  $('.country_code_label').text(data['inputs']['country_code']['value']);
                    //  $('#userWorkModal').find('.numeric').attr('minlength', data['inputs']['phone_length']['value']);
                    //  $('#userWorkModal').find('.numeric').attr('maxlength', data['inputs']['phone_length']['value']);
                    $('#usersWorkInfoForm')[0].reset();
                    $('#userWorkModal').modal('show');
                }
            });
//            str = '<tr>\n' +
//                '     <td>\n' +
//                '         <input class="form-control" type="text" name="company_name[]"\n' +
//                '                value=""\n' +
//                '                placeholder="Company Name" required>\n' +
//                '     </td>\n' +
//                '     <td>\n' +
//                '         <input class="form-control" type="text" name="function[]"\n' +
//                '                value=""\n' +
//                '                placeholder="Function In Company" required>\n' +
//                '     </td>\n' +
//                '     <td>\n' +
//                '         <input class="form-control" type="text" name="telephone[]"\n' +
//                '                value=""\n' +
//                '                placeholder="Telephone" required>\n' +
//                '     </td>\n' +
//                '     <td>\n' +
//                '         <input class="form-control" type="text" name="mail[]" value=""\n' +
//                '                placeholder="Email" required>\n' +
//                '     </td>\n' +
//                '     <td>\n' +
//                '         <button class="deleteWorkinfo btn btn-danger">\n' +
//                '             <i class="fa fa-trash"></i>\n' +
//                '         </button>\n' +
//                '     </td>\n' +
//                '  </tr>';
//            $('.tableWorkInfo').append(str);
        });
        $(document).on('click', '.editUserWorkInfo', function (e) {
            $.ajax({
                dataType: 'json',
                method: 'get',
                url: ajaxURL + 'users/' + $('#usersWorkInfoForm').data('user-id') + '/works/' + $(this).data('id') + '/edit',
                success: function (data) {
                    $('#usersWorkInfoForm')[0].reset();
                    //        $('.country_code_label').text(data['work']['country_code']['value']);
                    setFormValues('usersWorkInfoForm', data.work);
                    $('#userWorkModal').modal('show');
                    $('#userWorkModal').find('input, select').removeAttr('disabled');
                    $('#userWorkModal').find('button[type="submit"]').show();
                }
            });
        });

        ``

        $(document).on('click', '.userWorkInfoSubmit', function (e) {
            if ($('#usersWorkInfoForm').valid()) {
                $.ajax({
                    dataType: 'json',
                    method: 'post',
                    url: ajaxURL + 'users/' + $('#usersWorkInfoForm').data('user-id') + '/works',
                    data: $('#usersWorkInfoForm').serialize(),
                    success: function (data) {
                        getUserWorkInfo($('#usersWorkInfoForm').data('user-id'));
                        $('#userWorkModal').modal('hide');
                    }
                });
            }
        });

        /*$(document).on('submit', '#usersWorkInfoForm', function (e) {
            e.preventDefault();
            if ($('#usersWorkInfoForm').valid()) {

            }
        });*/

        $(document).on('click', '.deleteUserWorkinfo', function (e) {
            e.preventDefault();
            if (confirm('Are you sure you want to delete this work info?')) {
                $.ajax({
                    dataType: 'json',
                    method: 'delete',
                    url: ajaxURL + 'users/' + $('#usersWorkInfoForm').data('user-id') + '/works/' + $(this).data('id'),
                    success: function (data) {
                        getUserWorkInfo($('#usersWorkInfoForm').data('user-id'));
                    }
                });
            }
        });

        $(document).on('click', '.deleteWorkinfo', function (e) {
            e.preventDefault();
            $(this).parent().parent().remove();
        });

        $(document).on('click', '.addUserBankInfo', function (e) {
            e.preventDefault();
            $.ajax({
                datatype: 'json',
                method: 'get',
                url: ajaxURL + "users/banks",
                data: {
                    user_id: $('#usersBankInfoForm').data('user-id'),
                },
                success: function (data) {
                    str = '<tr>\n' +
                        '     <td>\n';
                    str += '<select name="bank_id[]" class="form-control" required>' +
                        '<option value="">Select Bank</option>';
                    for (var i in data['banks']) {
                        str += '<option value="' + i + '">' + data['banks'][i] + '</option>';
                    }
                    str += '</select>';
                    str += '     </td>\n' +
                        '     <td>\n' +
                        '         <input class="form-control" type="text" name="account_number[]"\n' +
                        '                value=""\n' +
                        '                placeholder="Account Number" required>\n' +
                        '     </td>\n' +
                        '     <td>\n' +
                        '         <input class="form-control" type="text" name="name_on_account[]"\n' +
                        '                value=""\n' +
                        '                placeholder="Name on Account" required>\n' +
                        '     </td>\n' +
                        '     <td>\n' +
                        '         <textarea class="form-control" type="text" name="address_on_account[]"\n' +
                        '                value=""\n' +
                        '                placeholder="Address On Account" required></textarea>\n' +
                        '     </td>\n' +
                        '     <td>\n' +
                        '         <button class="deleteWorkinfo btn btn-danger">\n' +
                        '             <i class="fa fa-trash"></i>\n' +
                        '         </button>\n' +
                        '     </td>\n' +
                        '  </tr>';
                    $('.tableBankInfo').append(str);
                }
            });
        });

        $(document).on('click', '.addUserReferenceInfo', function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'get',
                url: siteURL + 'users/' + $('#usersBankInfoForm').data('user-id'),
                success: function (data) {
                    str = '<tr>\n' +
                        '     <td>\n' +
                        '         <input class="form-control" type="text" name="first_name[]" value="" placeholder="First Name" required>' +
                        '     </td>\n' +
                        '     <td>\n' +
                        '         <input class="form-control" type="text" name="last_name[]" value="" placeholder="Last Name" required>' +
                        '     </td>\n' +
                        '     <td>\n';
                    str += '<select class="form-control" name="relationship[]" required>';
                    str += '<option value="">Select Relationship</option>';
                    for (var index in relationships) {
                        str += '<option value="' + index + '">' + relationships[index] + '</option>';
                    }
                    str += '</select>';
                    // str += '         <input class="form-control" type="text" name="relationship[]" value="" placeholder="Relationship" required>';
                    str += '     </td>\n' +
                        '     <td>\n' +
                        '         <div class="input-group">\n' +
                        '            <span class="input-group-addon country_code_label">' + data['inputs']['country_code']['value'] + '</span>' +
                        '         <input onKeyDown="limitText(this,' + data['phone_length'] + ');" onKeyUp="limitText(this,' + data['phone_length'] + ');" minlength="' + data['inputs']['phone_length']['value'] + '" maxlength="' + data['inputs']['phone_length']['value'] + '" class="form-control" type="number" name="telephone[]" value="" placeholder="Telephone" required>' +
                        '     </div></td>\n' +
                        '     <td>\n' +
                        '         <div class="input-group">\n' +
                        '            <span class="input-group-addon country_code_label">' + data['inputs']['country_code']['value'] + '</span>' +
                        '         <input onKeyDown="limitText(this,' + data['phone_length'] + ');" onKeyUp="limitText(this,' + data['phone_length'] + ');" minlength="' + data['inputs']['phone_length']['value'] + '" maxlength="' + data['inputs']['phone_length']['value'] + '" class="form-control" type="number" name="cellphone[]" value="" placeholder="Cellphone" required>\n' +
                        '     </div></td>\n' +
                        '     <td>\n' +
                        '         <textarea class="form-control" type="text" name="address[]" value="" placeholder="Address" required></textarea>\n' +
                        '     </td>\n' +
                        '     <td>\n' +
                        '         <button class="deleteWorkinfo btn btn-danger">\n' +
                        '             <i class="fa fa-trash"></i>\n' +
                        '         </button>\n' +
                        '     </td>\n' +
                        '  </tr>';
                    $('.tableReferenceInfo').append(str);
                }
            });

        });

        function limitText(limitField, limitNum) {
            if (limitField.value.length > limitNum) {
                limitField.value = limitField.value.substring(0, limitNum);
            }
        }

        $(document).on('click', '.addNewTelephone', function (e) {
            e.preventDefault();
            if ($('#country_id').val() != '') {
                $.ajax({
                    dataType: 'json',
                    method: 'get',
                    url: adminSiteURL + 'countries/' + $('#country_id').val(),
                    success: function (data) {
                        str = '<tr>\n' +
                            '    <td>\n' +
                            '         <div class="input-group">\n' +
                            '            <span class="input-group-addon country_code_label">' + data['inputs']['country_code']['value'] + '</span>' +
                            '        <input type="number" required minlength="' + data['inputs']['phone_length']['value'] + '" maxlength="' + data['inputs']['phone_length']['value'] + '" name="telephone[]" class="form-control"' +
                            '            onKeyDown="limitText(this,' + data['inputs']['phone_length']['value'] + ');" onKeyUp="limitText(this,' + data['inputs']['phone_length']['value'] + ');">' +
                            '</div>\n' +
                            '    </td>\n' +
                            '    <td>\n' +
                            '        <button class="delete btn btn-danger deleteWorkinfo">\n' +
                            '            <i class="fa fa-trash"></i>\n' +
                            '        </button>\n' +
                            '    </td>\n' +
                            '</tr>';

                        $('#user_telephones').append(str);

                    }
                });
            } else {
                alert('Please select country first.');
            }
        });
        $(document).on('click', '.addNewCellphone', function (e) {
            e.preventDefault();
            if ($('#country_id').val() != '') {
                $.ajax({
                    dataType: 'json',
                    method: 'get',
                    url: adminSiteURL + 'countries/' + $('#country_id').val(),
                    success: function (data) {
                        str = '<tr>\n' +
                            '    <td>\n' +
                            '         <div class="input-group">\n' +
                            '            <span class="input-group-addon country_code_label">' + data['inputs']['country_code']['value'] + '</span>' +
                            '        <input type="number" required minlength="' + data['inputs']['phone_length']['value'] + '" maxlength="' + data['inputs']['phone_length']['value'] + '" name="cellphone[]" class="form-control"' +
                            '               onKeyDown="limitText(this,' + data['inputs']['phone_length']['value'] + ');" onKeyUp="limitText(this,' + data['inputs']['phone_length']['value'] + ');">' +
                            '       </div>\n' +
                            '    </td>\n' +
                            '    <td>\n' +
                            '        <button class="delete btn btn-danger deleteWorkinfo">\n' +
                            '            <i class="fa fa-trash"></i>\n' +
                            '        </button>\n' +
                            '    </td>\n' +
                            '</tr>';

                        $('#user_cellphones').append(str);
                    }
                });
            } else {
                alert('Please select country first.');
            }
        });
        $(document).on('click', '.addNewEmail', function (e) {
            e.preventDefault();
            str = '<tr>\n' +
                '    <td>\n' +
                '        <input type="email" required name="secondary_email[]" class="form-control">\n' +
                '    </td>\n' +
                '    <td>\n' +
                '        <button class="delete btn btn-danger deleteWorkinfo">\n' +
                '            <i class="fa fa-trash"></i>\n' +
                '        </button>\n' +
                '    </td>\n' +
                '</tr>';

            $('#user_emails').append(str);
        });

        function getUserBankInfo(user_id) {
            $.ajax({
                dataType: 'json',
                method: 'get',
                url: ajaxURL + 'users/' + user_id + '/banks',
                success: function (data) {
                    str = '';
                    $('.tableBankInfo').html(str);
                    for (var index in data['banks']) {
                        var bank = data['banks'][index];

                        str += '<tr>\n' +
                            '     <td>\n';
                        str += '<select name="bank_id[]" class="form-control" required>' +
                            '<option value="">Select Bank</option>';
                        for (var i in data['banks_data']) {
                            var selected = '';
                            if (i == bank['bank_id']) {
                                selected = 'selected';
                            }
                            str += '<option value="' + i + '" ' + selected + '>' + data['banks_data'][i] + '</option>';
                        }
                        str += '</select>';
                        str += '     <td>\n' +
                            '         <input class="form-control" type="text" name="account_number[]"\n' +
                            '                value="' + bank['account_number'] + '"\n' +
                            '                placeholder="Account Number" required>\n' +
                            '     </td>\n' +
                            '     <td>\n' +
                            '         <input class="form-control" type="text" name="name_on_account[]"\n' +
                            '                value="' + bank['name_on_account'] + '"\n' +
                            '                placeholder="Name on Account" required>\n' +
                            '     </td>\n' +
                            '     <td>\n' +
                            '         <textarea class="form-control" type="text" name="address_on_account[]"\n' +
                            '                value=""\n' +
                            '                placeholder="Address On Account" required>' + bank['address_on_account'] + '</textarea>\n' +
                            '     </td>\n' +
                            '     <td>\n' +
                            '         <button class="deleteWorkinfo btn btn-danger">\n' +
                            '             <i class="fa fa-trash"></i>\n' +
                            '         </button>\n' +
                            '     </td>\n' +
                            '  </tr>';
                    }
                    $('.tableBankInfo').append(str);
                }
            });
        }

        function getUserWorkInfo(user_id) {
            $.ajax({
                dataType: 'json',
                method: 'get',
                url: ajaxURL + 'users/' + user_id + '/works',
                success: function (data) {
                    str = '';
                    $('.tableWorkInfo').html(str);
                    $('[name="working_type"]').removeAttr('checked');
                    if (data['working_type'] != null && data['working_type'] != '') {
                        $('[name="working_type"][value="' + data['working_type'] + '"]').attr('checked', true);
                    } else {
                        $('[name="working_type"][value="1"]').attr('checked', true);
                    }
                    for (var index in data['works']) {
                        var work = data['works'][index];

                        str += '<tr>\n' +
                            '     <td>' + work['employer'] + '</td>' +
                            '     <td>' + work['position'] + '</td>' +
                            '     <td>' + work['employed_since'] + '</td>' +
                            '     <td>' + work['contract_expires'] + '</td>' +
                            '     <td>\n' +
                            '         <button class="btn btn-primary editUserWorkInfo" data-id="' + work['id'] + '">' +
                            '             <i class="fa fa-pencil"></i>' +
                            '         </button>' +
                            '         <button class="deleteUserWorkinfo btn btn-danger"  data-id="' + work['id'] + '">' +
                            '             <i class="fa fa-trash"></i>\n' +
                            '         </button>\n' +
                            '         <button class="viewWorkInfo btn btn-default"  data-id="' + work['id'] + '">' +
                            '             <i class="fa fa-eye"></i>\n' +
                            '         </button>\n' +
                            '     </td>\n' +
                            '  </tr>';
                    }
                    $('.tableWorkInfo').append(str);
                }
            });
        }

        function getUserReferenceInfo(user_id) {
            $.ajax({
                dataType: 'json',
                method: 'get',
                url: ajaxURL + 'users/' + user_id + '/references',
                success: function (data) {
                    str = '';
                    $('.tableReferenceInfo').html(str);
                    var loop = 3;
                    if (data['references'].length > 3) {
                        loop = data['references'].length;
                    }
                    for (var i = 0; i < loop; i++) {
                        var reference = data['references'][i];

                        if (reference == undefined) {
                            reference = {
                                first_name: '',
                                last_name: '',
                                relationship: '',
                                telephone: '',
                                cellphone: '',
                                address: ''
                            };
                        }

                        str += '<tr>\n' +
                            '     <td>\n' +
                            '         <input class="form-control" type="text" name="first_name[]" value="' + reference['first_name'] + '" placeholder="First Name" required>' +
                            '     </td>\n' +
                            '     <td>\n' +
                            '         <input class="form-control" type="text" name="last_name[]" value="' + reference['last_name'] + '" placeholder="Last Name" required>' +
                            '     </td>\n' +
                            '     <td>\n';
                        str += '<select class="form-control" name="relationship[]" required>';
                        str += '<option value="">Select Relationship</option>';
                        for (var index in relationships) {
                            if (index == reference['relationship']) {
                                str += '<option selected value="' + index + '">' + relationships[index] + '</option>';
                            } else {
                                str += '<option value="' + index + '">' + relationships[index] + '</option>';
                            }
                        }
                        str += '</select>';
                        // str += '         <input class="form-control" type="text" name="relationship[]" value="' + reference['relationship'] + '" placeholder="Relationship" required>';
                        str += '     </td>\n' +
                            '     <td>\n' +
                            '         <div class="input-group">\n' +
                            '            <span class="input-group-addon country_code_label">' + data['country_code'] + '</span>' +
                            '         <input onKeyDown="limitText(this,' + data['phone_length'] + ');" onKeyUp="limitText(this,' + data['phone_length'] + ');" minlength="' + data['phone_length'] + '" maxlength="' + data['phone_length'] + '" class="form-control" type="number" name="telephone[]" value="' + reference['telephone'] + '" placeholder="Telephone" required>' +
                            '     </div></td>\n' +
                            '     <td>\n' +
                            '         <div class="input-group">\n' +
                            '            <span class="input-group-addon country_code_label">' + data['country_code'] + '</span>' +
                            '         <input onKeyDown="limitText(this,' + data['phone_length'] + ');" onKeyUp="limitText(this,' + data['phone_length'] + ');" minlength="' + data['phone_length'] + '" maxlength="' + data['phone_length'] + '" class="form-control" type="number" name="cellphone[]" value="' + reference['cellphone'] + '" placeholder="Cellphone" required>\n' +
                            '     </div></td>\n' +
                            '     <td>\n' +
                            '         <textarea class="form-control" type="text" name="address[]" value="" placeholder="Address" required>' + reference['address'] + '</textarea>\n' +
                            '     </td>\n' +
                            // '     <td>\n' +
                            // '         <button class="deleteWorkinfo btn btn-danger">\n' +
                            // '             <i class="fa fa-trash"></i>\n' +
                            // '         </button>\n' +
                            // '     </td>\n' +
                            '  </tr>';
                    }
                    $('.tableReferenceInfo').append(str);
                }
            })
        }

        function setEdit(id, type) {
            if (type == undefined) {
                type = '';
            }
            var action = '{{route('users.show','')}}/' + id
            $.ajax({
                type: 'GET',
                url: action,
                data: {},
                dataType: 'json',
                success: function (data) {
                    $('#usersInfoForm').attr('action', ajaxURL + 'users/' + id);
                    wizardInit('edit');
                    setFormValues('usersInfoForm', data.inputs);
                    setTelephones(data.telephones, data['inputs']['country_code']['value'], data['inputs']['phone_length']['value']);
                    setCellphones(data.cellphones, data['inputs']['country_code']['value'], data['inputs']['phone_length']['value']);
                    setOtherDocuments(data.other_documents);
                    setSecondaryEmails(data.emails);
                    if (data.inputs.profile_pic.value != '') {
                        $('.profile-pic-holder').show();
                        $('.profile-pic-holder').find('a').attr('href', data.inputs.profile_pic.value);
                    } else {
                        $('.profile-pic-holder').hide();
                        $('.profile-pic-holder').find('a').attr('href', '');
                    }
                    if (data.inputs.scan_id.value != '') {
                        $('.scan-id-holder').show();
                        $('.scan-id-holder').find('a').attr('href', data.inputs.scan_id.value);
                        // var lastItem = data.inputs.scan_id.value.split('/').pop();
                        // $('.scan-id-holder').find('a').text(lastItem);
                    } else {
                        $('.scan-id-holder').hide();
                        $('.scan-id-holder').find('a').attr('href', '');
                    }
                    if (data.inputs.address_proof.value != '') {
                        $('.address-proof-holder').show();
                        $('.address-proof-holder').find('a').attr('href', data.inputs.address_proof.value);
                        // var lastItem = data.inputs.address_proof.value.split('/').pop();
                        // $('.address-proof-holder').find('a').text(lastItem);
                    } else {
                        $('.address-proof-holder').hide();
                        $('.address-proof-holder').find('a').attr('href', '');
                    }

                    if (data.inputs.payslip1.value != '') {
                        $('.payslip1-holder').show();
                        $('.payslip1-holder').find('a').attr('href', data.inputs.payslip1.value);
                        // var lastItem = data.inputs.payslip1.value.split('/').pop();
                        // $('.payslip1-holder').find('a').text(lastItem);
                    } else {
                        $('.payslip1-holder').hide();
                        $('.payslip1-holder').find('a').attr('href', '');
                    }
                    if (data.inputs.payslip2.value != '') {
                        $('.payslip2-holder').show();
                        $('.payslip2-holder').find('a').attr('href', data.inputs.payslip2.value);
                        // var lastItem = data.inputs.payslip2.value.split('/').pop();
                        // $('.payslip2-holder').find('a').text(lastItem);
                    } else {
                        $('.payslip2-holder').hide();
                        $('.payslip2-holder').find('a').attr('href', '');
                    }
                    /*  if (data.inputs.other_document.value != '') {
                          $('.other-document-holder').show();
                          $('.other-document-holder').find('img').attr('src', data.inputs.other_document.value);
                      } else {
                          $('.other-document-holder').hide();
                          $('.other-document-holder').find('img').attr('src', '');
                      }*/
                    datePickerInit();
                    toggleStatus('[name="role_id"]');
                    $('[data-toggle="tooltip"]').tooltip();
                    if (data.inputs.scan_id.value != '') {
                        $('[name="scan_id"]').removeAttr('required');
                    }
                    if (data.inputs.address_proof.value != '') {
                        $('[name="address_proof"]').removeAttr('required');
                    }
                    if (data.inputs.payslip1.value != '') {
                        $('[name="payslip1"]').removeAttr('required');
                    }
                    if (data.inputs.payslip2.value != '') {
                        $('[name="payslip2"]').removeAttr('required');
                    }
                },
                error: function (jqXHR, exception) {
                }
            });
        }

        function SaveUser(form) {

            var options = {
                target: '',
                url: $(form).attr('action'),
                type: 'POST',
                success: function (res) {
                    successMsg('Message');
                },
                error: function (jqXHR, exception) {
                    var Response = jqXHR.responseText;
                    ErrorBlock = $(form);
                    Response = $.parseJSON(Response);
                    DisplayErrorMessages(Response, ErrorBlock, 'input');
                }
            }
            $(form).ajaxSubmit(options);
            return false;
        }

        function removeUserPic(element) {
            var form = $(element).parents('form');
            $(form).find('input[name="removeImage"]').val('true');
            var options = {
                target: '',
                url: $(form).attr('action'),
                type: 'POST',
                success: function (res) {
                    successMsg('Profile picture removed');
                    $(form).find('input[name="removeImage"]').val('');
                    $(element).parents('.col-md-12').hide();
                },
                error: function (jqXHR, exception) {
                    var Response = jqXHR.responseText;
                    ErrorBlock = $(form);
                    Response = $.parseJSON(Response);
                    DisplayErrorMessages(Response, ErrorBlock, 'input');
                }
            }
            $(form).ajaxSubmit(options);
            return false;
        }

        function showMsg() {
            console.log('hello');
        }

        toggleStatus('[name="role_id"]');

        function toggleStatus(element) {
            var role = $(element).val();
            $('.status-group').show();
            if (role == 3) {
                $('#steps-uid-0-t-1').show();
                $('#steps-uid-0-t-2').show();
                $('#steps-uid-0-t-3').show();
                $('a[href$="next"]').text('Next');
                $('.jq__client').show();
                $('.jq__not_client').hide();
                $('[name="status"]').attr('required', true);
                $('[name="place_of_birth"]').attr('required', true);
                $('[name="dob"]').attr('required', true);
                $('[name="scan_id"]').attr('required', true);
                $('[name="address_proof"]').attr('required', true);
                $('[name="payslip1"]').attr('required', true);
                $('[name="payslip2"]').attr('required', true);
                $('[name="branch"]').removeAttr('required');
                $('.jq__estrick').html('');
                $('[name="status"] option[data-role="1"]').show();
            } else {
                $('#steps-uid-0-t-1').hide();
                $('#steps-uid-0-t-2').hide();
                $('#steps-uid-0-t-3').hide();
                $('.jq__client').hide();
                $('.jq__not_client').show();
                $('a[href$="next"]').text('Save And Close');
                $('[name="status"]').removeAttr('required');
                $('[name="place_of_birth"]').removeAttr('required');
                $('[name="dob"]').removeAttr('required');
                $('[name="payslip1"]').removeAttr('required');
                $('[name="payslip2"]').removeAttr('required');
                $('[name="scan_id"]').removeAttr('required');
                $('[name="address_proof"]').removeAttr('required');
                $('.jq__estrick').html('*');
                if (role != 1) {
                    $('[name="status"] option[data-role="1"]').hide();
                    $('.jq_branch').show();
                    $('[name="branch"]').attr('required', true);
                } else if (role == 9) {
                    $('.status-group').hide();
                    $('.jq_branch').hide();
                    $('[name="branch"]').removeAttr('required');
                } else {
                    $('.status-group').hide();
                    $('.jq_branch').hide();
                    $('[name="branch"]').removeAttr('required');
                }
            }
        }

        function setTelephones(data, country_code, phonelength) {
            str = '';
            for (var index in data) {
                str += '<tr>' +
                    '    <td>\n' +
                    '         <div class="input-group">\n' +
                    '            <span class="input-group-addon country_code_label">' + country_code + '</span>' +
                    '        <input type="number" onKeyDown="limitText(this,' + phonelength + ');" onKeyUp="limitText(this,' + phonelength + ');" required minlength="' + phonelength + '" maxlength="' + phonelength + '" name="telephone[]" class="form-control" value="' + data[index] + '">' +
                    '       </div>\n' +
                    '    </td>\n' +
                    '    <td>\n' +
                    '        <button class="delete btn btn-danger deleteWorkinfo">\n' +
                    '            <i class="fa fa-trash"></i>\n' +
                    '        </button>\n' +
                    '    </td>\n' +
                    '</tr>';

            }
            $('#user_telephones').html(str);
        }

        function setCellphones(data, country_code, phonelength) {
            str = '';
            for (var index in data) {
                str += '<tr>\n' +
                    '    <td>\n' +
                    '         <div class="input-group">\n' +
                    '            <span class="input-group-addon country_code_label">' + country_code + '</span>' +
                    '        <input type="number" onKeyDown="limitText(this,' + phonelength + ');" onKeyUp="limitText(this,' + phonelength + ');" required minlength="' + phonelength + '" maxlength="' + phonelength + '" name="cellphone[]" class="form-control" value="' + data[index] + '">' +
                    '           </div>' +
                    '    </td>\n' +
                    '    <td>\n' +
                    '        <button class="delete btn btn-danger deleteWorkinfo">\n' +
                    '            <i class="fa fa-trash"></i>\n' +
                    '        </button>\n' +
                    '    </td>\n' +
                    '</tr>';
            }
            $('#user_cellphones').html(str);
        }

        function setSecondaryEmails(data) {
            str = '';
            for (var index in data) {
                str += '<tr>\n' +
                    '    <td>\n' +
                    '        <input type="email" name="secondary_email[]" class="form-control" value="' + data[index] + '">\n' +
                    '    </td>\n' +
                    '    <td>\n' +
                    '        <button class="delete btn btn-danger deleteWorkinfo">\n' +
                    '            <i class="fa fa-trash"></i>\n' +
                    '        </button>\n' +
                    '    </td>\n' +
                    '</tr>';
            }
            $('#user_emails').html(str);
        }

        function toggleCivilStatus() {
            if ($('[name="civil_status"]').val() == 1) {
                $('[name="spouse_first_name"]').removeAttr('required');
                $('[name="spouse_last_name"]').removeAttr('required');
                $('[name="spouse_first_name"]').parent().hide();
                $('[name="spouse_last_name"]').parent().hide();
            } else if ($('[name="civil_status"]').val() == 2) {
                $('[name="spouse_first_name"]').attr('required', true);
                $('[name="spouse_last_name"]').attr('required', true);
                $('[name="spouse_first_name"]').parent().show();
                $('[name="spouse_last_name"]').parent().show();
            }
        }

        $(document).on('click change', '[name="civil_status"]', function (e) {
            e.preventDefault();
            toggleCivilStatus();
        });
        toggleCivilStatus();

        function storeWorkingType(type) {
            if (type == 1 || type == 2) {
                $.ajax({
                    dataType: 'json',
                    method: 'post',
                    url: ajaxURL + 'users/' + $('#usersWorkInfoForm').data('user-id') + '/working-type',
                    data: {
                        type: type
                    },
                    success: function (data) {
                        console.log('sajdjbasjd');
                    }
                });
            }
        }

        var counter_doc = 0;
        $(document).on('click', '.addNewOtherDocument', function (e) {
            e.preventDefault();
            var str = '<tr>' +
                '   <td>' +
                '       <input type="file" name="other_document[]" class="form-control">' +
                '   </td>' +
                '   <td><input type="text" name="other_document_name[]" class="form-control"></td>' +
                '   <td><button class="btn btn-danger deleteOtherDocument" data-id=""><i class="fa fa-trash"></i></button></td>' +
                '</tr>';
            $('#other_document_table').append(str);
        });
        $(document).on('click', '.deleteOtherDocument', function (e) {
            e.preventDefault();
            if ($(this).data('id') != '') {
                var _this = this;
                $.ajax({
                    dataType: 'json',
                    method: 'delete',
                    url: ajaxURL + 'documents/' + $(this).data('id'),
                    success: function (data) {
                        if (data['status']) {
                            $(_this).parent().parent().remove();
                        }
                    }
                });
            } else {
                $(this).parent().parent().remove();
            }
        });

        function setOtherDocuments(data) {
            var str = '';
            for (var index in data) {
                var file = data[index];
                // var lastItem = file['document'].split('/').pop();
                str += '<tr>' +
                    '   <td>' +
                    '       <a href="' + file['document'] + '" download target="_blank" class="btn btn-primary"><i class="fa fa-paperclip"></i></a>' +
                    '       <input type="hidden" name="other_document_id[]" value="' + file['id'] + '">' +
                    '   </td>' +
                    '   <td><input type="text" name="other_old_document_name[]" class="form-control" value="' + file['name'] + '"></td>' +
                    '   <td><button class="btn btn-danger deleteOtherDocument" data-id="' + file['id'] + '"><i class="fa fa-trash"></i></button></td>' +
                    '</tr>';
            }
            $('#other_document_table').html(str);
        }
    </script>
    @if(request('user_id'))
        <script>
            var user_id = "{!! request('user_id') !!}";
            setEdit(user_id, 'view');
        </script>
    @endif
    @if(isset($edit_user))
        <script>
            setEdit("{!! $edit_user->id !!}");
        </script>
    @endif
    <script>
        $(document).ready(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endsection
