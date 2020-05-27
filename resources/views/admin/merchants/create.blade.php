@extends('admin.layouts.app')
@section('page_name')
    @if(isset($edit_user))
        Merchants Edit
    @else
        Merchants Create
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
                        <h3>PERSONAL INFO</h3>
                        <section>
                            <form id="usersInfoForm" action="{!! route('merchants.store') !!}">
                                <input type="hidden" name="id">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">First Name(s)</label>
                                            <input type="text" name="firstname" class="form-control"
                                                   placeholder="e.g John" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Last Name(s)</label>
                                            <input type="text" name="lastname" class="form-control"
                                                   placeholder="e.g Smith" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Email Address</label>
                                            <input type="email" name="email" class="form-control"
                                                   placeholder="e.g johnsmith@gmail.com" required>
                                            <span class="help-block" id="email_error"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
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
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!!Form::label('contact_person','Contact Person')!!}
                                            {!!Form::text('contact_person', '', ['class'=>'form-control','placeholder'=>'Contact Person'])!!}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!!Form::label('address','Address')!!}
                                            {!!Form::textarea('address', '', ['class'=>'form-control','placeholder'=>'Address','rows'=>3])!!}
                                        </div>
                                    </div>
                                    @if(!auth()->user()->hasRole('super admin'))
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                {!!Form::label('country','Country')!!}
                                                {!!Form::select('country', $countries,auth()->user()->country, ['class'=>'form-control','placeholder'=>'Select Country','disabled','id'=>'country_id'])!!}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                {!!Form::label('territory','District')!!}
                                                {!!Form::select('territory',$territories,auth()->user()->territory,['class'=>'form-control','placeholder'=>'District'])!!}
                                                @if($errors->has('territory'))
                                                    <p class="help-block">{!!$errors->first('territory')!!}</p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                {!!Form::label('branch','Branch')!!}
                                                {!!Form::select('branch',$branches,auth()->user()->branch,['class'=>'form-control','placeholder'=>'Branch'])!!}
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
                                                {!!Form::select('territory',$territories,'',['class'=>'form-control','required','placeholder'=>'District'])!!}
                                                @if($errors->has('territory'))
                                                    <p class="help-block">{!!$errors->first('territory')!!}</p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                {!!Form::label('branch','Branch')!!}
                                                {!!Form::select('branch',$branches,'',['class'=>'form-control','placeholder'=>'Branch'])!!}
                                                @if($errors->has('branch'))
                                                    <p class="help-block">{!!$errors->first('branch')!!}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                {!!Form::label('country','Country')!!}
                                                {!!Form::select('country', $countries,'', ['class'=>'form-control','placeholder'=>'Select Country','id'=>'country_id'])!!}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label">District</label>
                                                <select name="territory" class="form-control" id="territory_id">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label">Branch</label>
                                                <select name="branch" class="form-control" id="branch_id">
                                                    <option value=''>Select Branch</option>
                                                </select>
                                            </div>
                                            <div class="error">
                                              <span class="help-block" for="branch">
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!!Form::label('transaction_type','Transaction fee type')!!}
                                            {!!Form::select('transaction_type',['1'=>'percentage','2'=>'flat'],old('transaction_type'),['class'=>'form-control','placeholder'=>'Transaction fee Type','required'])!!}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!!Form::label('transaction_fee','Transaction fee amount')!!}
                                            {!!Form::number('transaction_fee', old('transaction_fee'), ['class'=>'form-control','placeholder'=>'Transaction fee','step'=>0.01,'required'])!!}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!!Form::label('commission_type','Commission fee type')!!}
                                            {!!Form::select('commission_type',['1'=>'percentage','2'=>'flat'],old('commission_type'),['class'=>'form-control','placeholder'=>'Commission fee Type','required'])!!}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!!Form::label('commission_fee','Commission fee amount')!!}
                                            {!!Form::number('commission_fee', old('commission_fee'), ['class'=>'form-control','placeholder'=>'Commission fee','step'=>0.01,'required'])!!}
                                        </div>
                                    </div>
                                    <div class="col-md-6 status-group">
                                        <div class="form-group">
                                            <label class="control-label">Status</label>
                                            <select name="status" class="form-control">
                                                @foreach($status as $item)
                                                    <option value="{{$item->id}}">{{$item->title}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="has-error">
                                            <p class="help-block" style="color:red;" id="phone_error"></p>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
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
                                                <h2>Telephone</h2>
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
                                    <div class="form-group clearfix">
                                        <label class="col-lg-12 control-label ">(*) Mandatory</label>
                                    </div>
                                </div>
                            </form>
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
                                        <th>Bank Name</th>
                                        <th>Bank Account Number</th>
                                        <th>Name On Account</th>
                                        <th>Address On Account</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody class="tableBankInfo"></tbody>
                                </table>
                            </form>
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
@endsection

@section('custom-js')
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
                format: 'mm/dd/yyyy'
            });
        }

        function wizardInit(type) {
            if (steps_init == 1) {
                steps.steps('destroy');
            }

            steps_init = 1;

            var enableAllSteps = false;
            if (type == 'edit') {
                enableAllSteps = true;
            }

            var process = false;

            steps = $("#wizard-validation-form").children("div").steps({
                headerTag: "h3",
                bodyTag: "section",
                transitionEffect: "slideLeft",
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
                                    $.ajax({
                                        dataType: 'json',
                                        method: 'post',
                                        url: $('#usersInfoForm').attr('action'),
                                        data: $('#usersInfoForm').serialize(),
                                        success: function (data) {
                                            if (data['status']) {
                                                process = true;
                                                $('#usersInfoForm').attr('action', siteURL + 'merchants/' + data['user_id']);
                                                $('#usersInfoForm').data('user-id', data['user_id']);
                                                $('#usersBankInfoForm').attr('action', ajaxURL + 'users/' + data['user_id'] + '/banks');
                                                $('#usersBankInfoForm').data('user-id', data['user_id']);
                                                steps.steps('next');
                                            } else {
                                                if (data['type'] == 'email') {
                                                    $('#email_error').text(data['message']);
                                                }
                                                if (data['type'] == 'phone') {
                                                    $('#phone_error').text(data['message']);
                                                }
                                            }
                                        },
                                        error: function (jqXHR, exception) {
                                            var Response = jqXHR.responseText;
                                            ErrorBlock = $($('#usersInfoForm'));
                                            Response = $.parseJSON(Response);
                                            DisplayErrorMessages(Response, ErrorBlock, 'input');
                                        }
                                    });
                                }
                            }
                            if (currentIndex == 1) {
                                if ($('#usersBankInfoForm').valid()) {
                                    $.ajax({
                                        dataType: 'json',
                                        method: 'post',
                                        url: $('#usersBankInfoForm').attr('action'),
                                        data: $('#usersBankInfoForm').serialize(),
                                        success: function (data) {
                                            if (data['status']) {
                                                process = true;
                                                steps.steps('next');
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
                                                        '         <input class="form-control" type="text" name="address_on_account[]"\n' +
                                                        '                value="' + work['address_on_account'] + '"\n' +
                                                        '                placeholder="Address On Account" required>\n' +
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
                        getUserBankInfo($('#usersBankInfoForm').data('user-id'));
                    }
                },
                onFinishing: function (event, currentIndex) {
                    if ($('#usersBankInfoForm').valid()) {
                        $.ajax({
                            dataType: 'json',
                            method: 'post',
                            url: $('#usersBankInfoForm').attr('action'),
                            data: $('#usersBankInfoForm').serialize(),
                            success: function (data) {
                                if (data['status']) {
                                    process = true;
                                    window.location = siteURL + 'admin/merchants';
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
                                            '         <input class="form-control" type="text" name="address_on_account[]"\n' +
                                            '                value="' + work['address_on_account'] + '"\n' +
                                            '                placeholder="Address On Account" required>\n' +
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
                },
                onFinished: function (event, currentIndex) {
                    window.location = siteURL + 'users';
                }
            });
        }

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
                        str = '';
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
                        '         <input class="form-control" type="text" name="address_on_account[]"\n' +
                        '                value=""\n' +
                        '                placeholder="Address On Account" required>\n' +
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
                            '        <input type="number"  onKeyDown="limitText(this,' + data['inputs']['phone_length']['value'] + ');" onKeyUp="limitText(this,' + data['inputs']['phone_length']['value'] + ');" required minlength="' + data['inputs']['phone_length']['value'] + '" maxlength="' + data['inputs']['phone_length']['value'] + '" name="telephone[]" class="form-control"></div>\n' +
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
                            '        <input type="number"  onKeyDown="limitText(this,' + data['inputs']['phone_length']['value'] + ');" onKeyUp="limitText(this,' + data['inputs']['phone_length']['value'] + ');" required minlength="' + data['inputs']['phone_length']['value'] + '" maxlength="' + data['inputs']['phone_length']['value'] + '" name="cellphone[]" class="form-control"></div>\n' +
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
                '        <input type="text" name="secondary_email[]" class="form-control">\n' +
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
                            '         <input class="form-control" type="text" name="address_on_account[]"\n' +
                            '                value="' + bank['address_on_account'] + '"\n' +
                            '                placeholder="Address On Account" required>\n' +
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

        function setEdit(id, type) {
            if (type == undefined) {
                type = '';
            }
            var action = '{{route('merchants.show','')}}/' + id
            $.ajax({
                type: 'GET',
                url: action,
                data: {},
                dataType: 'json',
                success: function (data) {
                    $('#usersInfoForm').attr('action', siteURL + 'admin/merchants/' + id);
                    wizardInit('edit');
                    setFormValues('usersInfoForm', data.inputs);
                    setTelephones(data.telephones, data['inputs']['country_code']['value'], data['inputs']['phone_length']['value']);
                    setCellphones(data.cellphones, data['inputs']['country_code']['value'], data['inputs']['phone_length']['value']);
                    setSecondaryEmails(data.emails);
                    datePickerInit();
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

        function showMsg() {
            console.log('hello');
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
                    '        <input type="text" name="secondary_email[]" class="form-control" value="' + data[index] + '">\n' +
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
@endsection
