@extends('admin1.layouts.master')
@section('page_name')
    Users Profile
@stop
@section('contentHeader')
    <style>
        .red {
            color: red !important;
            background-color: #ffbdbd !important;
        }
    </style>
@stop
@section('content')
    <div class="container-fluid">

        <!-- Page-Title -->
        <div class="row">
            <div class="col-sm-12">

                <h4 class="page-title">Profile</h4>
                <ol class="breadcrumb">

                </ol>

            </div>
        </div>


        <div class="row">
            <div class="col-md-4 col-lg-3">
                <div class="profile-detail card-box">
                    <div>
                        @if($user->profile_pic!=null && $user->profile_pic!='')
                            <img src="{{asset('uploads/'.$user->profile_pic)}}" class="rounded-circle"
                                 alt="profile-image">
                        @else
                            <i class=" fa fa-user-circle fa-3x " style="font-size: 7em;"></i>
                        @endif

                        <ul class="list-inline status-list m-t-20">
                            <li class="list-inline-item">
                                <h3 class="text-primary m-b-5">Name</h3>
                                <p class="text-muted">{!! ucwords(strtolower($user->firstname)) !!}</p>
                            </li>
                            <li class="list-inline-item">
                                <h3 class="text-success m-b-5">Role</h3>
                                <p class="text-muted">{!! $user->role->name !!}</p>
                            </li>
                            <li class="list-inline-item">
                                <h3 class="text-success m-b-5">ID</h3>
                                <p class="text-muted">{!! $user->id_number !!}</p>
                            </li>
                            <li class="list-inline-item">
                                <h3 class="text-success m-b-5">Status</h3>
                                <p class="text-muted">{!! $user->status !!}</p>
                            </li>
                        </ul>

                        <hr>

                        <div class="text-left">
                            <p class="text-muted font-13">
                                <strong>Full Name :</strong>
                                <span class="m-l-15">{!! ucwords(strtolower($user->firstname." ".$user->lastname)) !!}</span>
                            </p>

                            <p class="text-muted font-13">
                                <strong>Cellphone :</strong>
                                <span class="m-l-15">{!! $user->cellphones !!}</span></p>

                            <p class="text-muted font-13">
                                <strong>Email :</strong>
                                <span class="m-l-15">{!! $user->email !!}</span>
                            </p>

                            <p class="text-muted font-13">
                                <strong>Country :</strong>
                                <span class="m-l-15">{!! $user->country !!}</span>
                            </p>

                        </div>
                    </div>

                </div>
                @if($user->role_id==3 && $user->country_referral==1)
                    <div class="profile-detail card-box">
                        <div class="text-left">
                            <table class="table" style="border: none">
                                @if(auth()->user()->role_id==1 || auth()->user()->role_id==5)
                                    <tr>
                                        <td style="width: 60%; border: none"><h4>Referral</h4></td>
                                        <td style="text-align: right; border: none; padding-top: 20px;">
                                            <input data-size="small" id="js--referral-status" type="checkbox" @if($user->referral_status==1) checked @endif data-plugin="switchery"
                                                   data-color="#81c868"/>
                                            {{--<span id="js--referral-status-text">  @if($user->referral_status==1) Active @else De-activated @endif</span>--}}
                                        </td>
                                    </tr>
                                @endif
                                @foreach($referral_infos as $key=>$value)
                                    @if(isset($value['title']))
                                        <tr class="text-muted font-13">
                                            <td style="width: 60%; border:none;"><strong>{!! $value['title'] !!} :</strong></td>
                                            <td style="text-align: right; border:none;"><span class="m-l-15">{!! $value['value'] !!}</span></td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td style="border: none;" colspan="2"></td>
                                        </tr>
                                    @endif
                                @endforeach
                            </table>
                        </div>
                    </div>
                @endif
            </div>


            <div class="col-lg-9 col-md-8">
                <ul class="nav nav-tabs tabs">
                    <li class="tab">
                        <a href="#home-2" data-toggle="tab" aria-expanded="false">
                            Personal Info
                        </a>
                    </li>
                    @if($user->role_id==3)
                        <li class="tab">
                            <a href="#profile-2" data-toggle="tab" aria-expanded="false">
                                Employee Info
                            </a>
                        </li>
                        <li class="tab">
                            <a href="#messages-2" data-toggle="tab" aria-expanded="true">
                                Bank Info
                            </a>
                        </li>
                        <li class="tab">
                            <a href="#settings-2" data-toggle="tab" aria-expanded="false">
                                Reference Info
                            </a>
                        </li>
                    @endif
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="home-2">
                        <div class="row">
                            <div class="col-md-4 portlet">
                                <div class="portlet-heading bg-custom">
                                    <h4 class="portlet-title">First Name</h4>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="portlet-body">{!! $user->firstname !!}</div>
                            </div>
                            <div class="col-md-4 portlet">
                                <div class="portlet-heading bg-custom">
                                    <h4 class="portlet-title">Last Name</h4>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="portlet-body">{!! $user->lastname !!}</div>
                            </div>
                            <div class="col-md-4 portlet">
                                <div class="portlet-heading bg-custom">
                                    <h4 class="portlet-title">Email</h4>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="portlet-body">{!! $user->email !!}</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 portlet">
                                <div class="portlet-heading bg-custom">
                                    <h4 class="portlet-title">Extra Emails</h4>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="portlet-body">{!! $user->extra_emails !!}</div>
                            </div>
                            <div class="col-md-4 portlet">
                                <div class="portlet-heading bg-custom">
                                    <h4 class="portlet-title">Telephones</h4>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="portlet-body">{!! $user->telephones !!}</div>
                            </div>
                            <div class="col-md-4 portlet">
                                <div class="portlet-heading bg-custom">
                                    <h4 class="portlet-title">Cellphones</h4>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="portlet-body">{!! $user->cellphones !!}</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 portlet">
                                <div class="portlet-heading bg-custom">
                                    <h4 class="portlet-title">Role</h4>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="portlet-body">{!! $user->role->name !!}</div>
                            </div>
                            <div class="col-md-4 portlet">
                                <div class="portlet-heading bg-custom">
                                    <h4 class="portlet-title">Language</h4>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="portlet-body">{!! strtoupper($user->lang) !!}</div>
                            </div>
                            <div class="col-md-4 portlet">
                                <div class="portlet-heading bg-custom">
                                    <h4 class="portlet-title">Sex</h4>
                                    <div class="clearfix"></div>
                                </div>
                                @if($user->sex==1)
                                    <div class="portlet-body">Male</div>
                                @elseif($user->sex==2)
                                    <div class="portlet-body">Female</div>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 portlet">
                                <div class="portlet-heading bg-custom">
                                    <h4 class="portlet-title">Date Of Birth</h4>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="portlet-body">
                                    @if($user->dob!=null)
                                        {!! \App\Library\Helper::datebaseToFrontDate($user->dob) !!}
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4 portlet">
                                <div class="portlet-heading bg-custom">
                                    <h4 class="portlet-title">Place Of Birth</h4>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="portlet-body">{!! $user->place_of_birth !!}</div>
                            </div>
                            <div class="col-md-4 portlet">
                                <div class="portlet-heading bg-custom">
                                    <h4 class="portlet-title">Address</h4>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="portlet-body">{!! $user->address !!}</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 portlet">
                                <div class="portlet-heading bg-custom">
                                    <h4 class="portlet-title">Country</h4>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="portlet-body">{!! $user->country !!}</div>
                            </div>
                            <div class="col-md-4 portlet">
                                <div class="portlet-heading bg-custom">
                                    <h4 class="portlet-title">District</h4>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="portlet-body">{!! ucwords(strtolower($user->territory)) !!}</div>
                            </div>
                            <div class="col-md-4 portlet">
                                <div class="portlet-heading bg-custom">
                                    <h4 class="portlet-title">Branch</h4>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="portlet-body">{!! $user->branch !!}</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 portlet">
                                <div class="portlet-heading bg-custom">
                                    <h4 class="portlet-title">Civil Status</h4>
                                    <div class="clearfix"></div>
                                </div>
                                @if($user->civil_status==1)
                                    <div class="portlet-body">Single</div>
                                @elseif($user->civil_status==2)
                                    <div class="portlet-body">Married</div>
                                @endif
                            </div>
                            @if($user->civil_status==2)
                                <div class="col-md-4 portlet">
                                    <div class="portlet-heading bg-custom">
                                        <h4 class="portlet-title">Spouse First Name</h4>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="portlet-body">{!! ucwords(strtolower($user->spouse_first_name)) !!}</div>
                                </div>
                                <div class="col-md-4 portlet">
                                    <div class="portlet-heading bg-custom">
                                        <h4 class="portlet-title">Spouse Last Name</h4>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="portlet-body">{!! ucwords(strtolower($user->spouse_last_name)) !!}</div>
                                </div>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-md-4 portlet">
                                <div class="portlet-heading bg-custom">
                                    <h4 class="portlet-title">Status</h4>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="portlet-body">{!! $user->status !!}</div>
                            </div>
                            <div class="col-md-4 portlet">
                                <div class="portlet-heading bg-custom">
                                    <h4 class="portlet-title">ID Number</h4>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="portlet-body">{!! $user->id_number !!}</div>
                            </div>
                            <div class="col-md-4 portlet">
                                <div class="portlet-heading bg-custom">
                                    <h4 class="portlet-title">Expiry Date</h4>
                                    <div class="clearfix"></div>
                                </div>
                                @if($user->exp_date!=null)
                                    <div class="portlet-body @if($user->exp_date < date('Y-m-d')) red @endif">
                                        {!! \App\Library\Helper::datebaseToFrontDate($user->exp_date) !!}
                                    </div>
                                @else
                                    <div class="portlet-body">
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 portlet">
                                <div class="portlet-heading bg-custom">
                                    <h4 class="portlet-title">Passport Number</h4>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="portlet-body">{!! $user->pp_number !!}</div>
                            </div>
                            <div class="col-md-4 portlet">
                                <div class="portlet-heading bg-custom">
                                    <h4 class="portlet-title">Exp Date</h4>
                                    <div class="clearfix"></div>
                                </div>
                                @if($user->pp_exp_date!=null)
                                    <div class="portlet-body @if($user->pp_exp_date < date('Y-m-d')) red @endif">
                                        {!! \App\Library\Helper::datebaseToFrontDate($user->pp_exp_date) !!}
                                    </div>
                                @else
                                    <div class="portlet-body">
                                    </div>
                                @endif
                            </div>
                        </div>
                        <hr>
                        <h3>Documents</h3>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>File</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>Pay Slip 1</td>
                                        <td>
                                            @if($user->payslip1!='')
                                                <a href="{!! url('uploads/'.$user->payslip1) !!}"
                                                   class="btn btn-default" target="_blank"
                                                   download>
                                                    <i class="fa fa-paperclip"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Pay Slip 2</td>
                                        <td>
                                            @if($user->payslip2!='')
                                                <a href="{!! url('uploads/'.$user->payslip2) !!}"
                                                   class="btn btn-default" target="_blank"
                                                   download>
                                                    <i class="fa fa-paperclip"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Address Proof</td>
                                        <td>
                                            @if($user->address_proof!='')
                                                <a href="{!! url('uploads/'.$user->address_proof) !!}"
                                                   class="btn btn-default" target="_blank"
                                                   download>
                                                    <i class="fa fa-paperclip"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Scan Id</td>
                                        <td>
                                            @if($user->scan_id!='')
                                                <a href="{!! url('uploads/'.$user->scan_id) !!}" class="btn btn-default" target="_blank" download>
                                                    <i class="fa fa-paperclip"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <hr>
                        <h3>Other Documents</h3>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>File</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($user->other_documents as $key=>$value)
                                        <tr>
                                            <td>{!! $value->name !!}</td>
                                            <td>
                                                <a href="{!! $value->document !!}" class="btn btn-default"
                                                   target="_blank" download>
                                                    <i class="fa fa-paperclip"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @if($user->role_id==3)
                        <div class="tab-pane" id="profile-2">
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
                        </div>
                        <div class="tab-pane" id="messages-2">
                            <table class="table table-bordered table-user">
                                <thead>
                                <tr>
                                    <th>Bank Name *</th>
                                    <th>Bank Account Number *</th>
                                    <th>Name On Account *</th>
                                    <th>Address On Account *</th>
                                </tr>
                                </thead>
                                <tbody class="tableBankInfo"></tbody>
                            </table>
                        </div>
                        <div class="tab-pane" id="settings-2">
                            <table class="table table-bordered table-user">
                                <thead>
                                <tr>
                                    <th>First Name(s) *</th>
                                    <th>Last Name(s) *</th>
                                    <th>Relationship *</th>
                                    <th>Tel Number</th>
                                    <th>Cel Number *</th>
                                    <th>Address *</th>
                                </tr>
                                </thead>
                                <tbody class="tableReferenceInfo"></tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
    @include('admin1.popups.user_work_info_form')
@stop
@section('contentFooter')
    <script>
        var user_id = '{!! $user->id !!}';
        var country_code = '{!! $user->country_code !!}';
        getUserWorkInfo(user_id);
        getUserBankInfo(user_id);
        getUserReferenceInfo(user_id);

        $(document).on('click', '.editUserWorkInfo', function (e) {
            e.preventDefault();
            var type = $(this).data('type');
            $.ajax({
                dataType: 'json',
                method: 'get',
                url: adminAjaxURL + 'users/' + user_id + '/works/' + $(this).data('id') + '/edit',
                success: function (data) {
                    $('#usersWorkInfoForm')[0].reset();
                    setForm('#usersWorkInfoForm', data.work);
                    $('#userWorkModal').modal('show');
                    $('#userWorkModal').find('input,select,number,textarea').prop('disabled', true);
                    $('#userWorkModal').find('button[type="submit"]').hide();
                }
            });
        });

        function getUserWorkInfo(user_id) {
            $.ajax({
                dataType: 'json',
                method: 'get',
                url: adminAjaxURL + 'users/' + user_id + '/works',
                success(data) {
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

                        str += '<tr>' +
                            '     <td>' + work['employer'] + '</td>' +
                            '     <td>' + work['position'] + '</td>' +
                            '     <td>' + work['employed_since'] + '</td>' +
                            '     <td>' + work['contract_expires'] + '</td>' +
                            '     <td>' +
                            '         <button class="editUserWorkInfo btn btn-default" data-type="view"  data-id="' + work['id'] + '">' +
                            '             <i class="fa fa-eye"></i>' +
                            '         </button>' +
                            '     </td>' +
                            '  </tr>';
                    }
                    $('.tableWorkInfo').append(str);
                }
            });
        }

        function getUserBankInfo(user_id) {
            var _this = this;
            $.ajax({
                dataType: 'json',
                method: 'get',
                url: adminAjaxURL + 'users/' + user_id + '/banks',
                success: function (data) {
                    str = '';
                    $('.tableBankInfo').html(str);
                    for (var index in data['banks']) {
                        var bank = data['banks'][index];
                        if (bank == undefined || bank == '') {
                            bank = {
                                bank_id: '',
                                account_number: '',
                                name_on_account: '',
                                address_on_account: '',
                            };
                        }
                        str = '<tr>' +
                            '     <td>' + data['banks_data'][bank['bank_id']] + '</td>' +
                            '     <td>' + bank['account_number'] + '</td>' +
                            '     <td>' + bank['name_on_account'] + '</td>' +
                            '     <td>' + bank['address_on_account'] +
                            '     </td>' +
                            '  </tr>';
                        $('.tableBankInfo').append(str);
                    }
                }
            });
        }

        function getUserReferenceInfo(user_id) {
            $.ajax({
                dataType: 'json',
                method: 'get',
                url: adminAjaxURL + 'users/' + user_id + '/references',
                success: function (data) {
                    str = '';
                    $('.tableReferenceInfo').html(str);
                    var loop = 3;
                    if (data['references'].length > 3) {
                        loop = data['references'].length;
                    }
                    for (var i = 0; i < loop; i++) {
                        var reference = data['references'][i];

                        if (reference != undefined) {
                            if (reference['telephone'] == null) {
                                reference['telephone'] = '';
                            } else {
                                reference['telephone'] = country_code + reference['telephone'];
                            }
                            str += '<tr>' +
                                '     <td>' + reference['first_name'] + '     </td>' +
                                '     <td>' + reference['last_name'] + '     </td>' +
                                '     <td>';
                            str += data['relationships'][reference['relationship']];
                            str += '     </td>' +
                                '     <td>' + reference['telephone'] + '</td>' +
                                '     <td>' + country_code + reference['cellphone'] + '</td>' +
                                '     <td>' + reference['address'] + '</td>' +
                                '  </tr>';
                        }
                    }
                    $('.tableReferenceInfo').append(str);
                }
            })
        }

        $(document).on('change', '#js--referral-status', function (e) {
            $.ajax({
                dataType: 'json',
                method: 'post',
                url: adminAjaxURL + 'users/' + user_id + '/referral-status',
                success: function (data) {
                    swal({
                        type: data['status'],
                        text: data['message']
                    });
                    var text = '';
                    if (data['referral_status'] == 1) {
                        text = 'Active';
                        $('#js--referral-status').attr('checked', true);
                    } else {
                        text = 'De-activated';
                        $('#js--referral-status').attr('checked', false);
                    }
                    $('#js--referral-status-text').text(text);
                }
            })
        });
    </script>
@stop