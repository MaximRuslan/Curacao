@extends('admin.layouts.app')
@section('page_name')
    Merchants
@stop
@section('extra-styles')

    <link href="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.bootstrap4.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{url(config('theme.admin.plugins'))}}/datatables/buttons.bootstrap4.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{url(config('theme.admin.plugins'))}}/datatables/responsive.bootstrap4.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{url(config('theme.admin.plugins'))}}/jquery.steps/css/jquery.steps.css" rel="stylesheet"
          type="text/css"/>
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
            <div class="btn-group pull-right m-b-20">
                <a href="{!! url()->route('merchants.create') !!}" class="btn btn-default waves-effect waves-light">
                    Add
                </a>
            </div>
            <h4 class="page-title">Merchants</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <table id="user-table" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    @include('common.delete_confirm',[
        'modalId'=>'deleteMerchant',
        'action'=>route('merchants.destroy','deleteId'),
        'item'=>'it',
        'callback'=>'showMsg'
        ])
@endsection

@section('extra-js')
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/jquery.dataTables.min.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.responsive.min.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/responsive.bootstrap4.min.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/jquery-validation/js/jquery.validate.min.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/jquery.steps/js/jquery.steps.min.js"></script>
@endsection

@section('custom-js')
    <script type="text/javascript">
        var oTable = "";
        $(document).ready(function () {
            oTable = $('#user-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{route('merchants-data')}}",
                    "type": "POST",
                    data: function (d) {
                        d.user_id = "{!! request('user_id') !!}";
                    }
                },
                order: [],
                "drawCallback": function (settings) {
                    InitTooltip();
                },
                columns: [
                    {data: 'id', name: 'id', visible: false},
                    {data: 'firstname', name: 'firstname'},
                    {data: 'email', name: 'email'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                order: [[0, 'desc']]
            });
            $('#userModal').on('hidden.bs.modal', function () {
                $('#userModal').find('form')[0].reset();
                $('#userModal').find('form').find('input[name="id"]').val('');
                $('#userModal').find('input, select').removeAttr('disabled');
                $('#userModal').find('button[type="submit"]').show();
                $('#userModal').find('#deleteImage').show();
                $('#userModal').find('.profile-pic-holder').hide();
                $('#userModal').find('.profile-pic-holder').find('img').attr('src', '');
            })
        });

        var steps_init = 0;
        var steps = '';

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
                                                $('#usersInfoForm').attr('action', ajaxURL + 'users/' + data['user_id']);
                                                $('#usersWorkInfoForm').attr('action', ajaxURL + 'users/' + data['user_id'] + '/works');
                                                $('#usersWorkInfoForm').data('user-id', data['user_id']);
                                                $('#usersBankInfoForm').attr('action', ajaxURL + 'users/' + data['user_id'] + '/banks');
                                                $('#usersBankInfoForm').data('user-id', data['user_id']);
                                                $('#usersReferenceInfoForm').attr('action', ajaxURL + 'users/' + data['user_id'] + '/references');
                                                $('#usersReferenceInfoForm').data('user-id', data['user_id']);
                                                steps.steps('next');
                                                oTable.draw();
                                            } else {
                                                $('#email_error').text(data['message']);
                                            }
                                        }
                                    });
                                }
                            }
                            if (currentIndex == 1) {
                                if ($('#usersWorkInfoForm').valid()) {
                                    $.ajax({
                                        dataType: 'json',
                                        method: 'post',
                                        url: $('#usersWorkInfoForm').attr('action'),
                                        data: $('#usersWorkInfoForm').serialize(),
                                        success: function (data) {
                                            if (data['status']) {
                                                process = true;
                                                steps.steps('next');
                                                oTable.draw();
                                            } else {
                                                str = '';
                                                var errors = data['errors'];
                                                $('.tableWorkInfo').html(str);
                                                for (var index in data['inputs']['company_name']) {
                                                    var work = data['inputs'];
                                                    if (work['company_name'][index] == null) {
                                                        work['company_name'][index] = '';
                                                    }
                                                    if (work['function'][index] == null) {
                                                        work['function'][index] = '';
                                                    }
                                                    if (work['telephone'][index] == null) {
                                                        work['telephone'][index] = '';
                                                    }
                                                    if (work['mail'][index] == null) {
                                                        work['mail'][index] = '';
                                                    }

                                                    str += '<tr>\n' +
                                                        '     <td>\n' +
                                                        '         <input class="form-control" type="text" name="company_name[]"\n' +
                                                        '                value="' + work['company_name'][index] + '"\n' +
                                                        '                placeholder="Company Name" required>\n';
                                                    if (errors['company_name.' + index] != undefined) {
                                                        str += '          <label class="error">' + errors['company_name.' + index] + '</label>';
                                                    }
                                                    str += '     </td>\n' +
                                                        '     <td>\n' +
                                                        '         <input class="form-control" type="text" name="function[]"\n' +
                                                        '                value="' + work['function'][index] + '"\n' +
                                                        '                placeholder="Function In Company" required>\n';
                                                    if (errors['function.' + index] != undefined) {
                                                        str += '          <label class="error">' + errors['function.' + index] + '</label>';
                                                    }
                                                    str += '     </td>\n' +
                                                        '     <td>\n' +
                                                        '         <input class="form-control" type="text" name="telephone[]"\n' +
                                                        '                value="' + work['telephone'][index] + '"\n' +
                                                        '                placeholder="Telephone" required>\n';
                                                    if (errors['telephone.' + index] != undefined) {
                                                        str += '          <label class="error">' + errors['telephone.' + index] + '</label>';
                                                    }
                                                    str += '     </td>\n' +
                                                        '     <td>\n' +
                                                        '         <input class="form-control" type="text" name="mail[]" value="' + work['mail'][index] + '"\n' +
                                                        '                placeholder="Email" required>\n';
                                                    if (errors['mail.' + index] != undefined) {
                                                        str += '          <label class="error">' + errors['mail.' + index] + '</label>';
                                                    }
                                                    str += '     </td>\n' +
                                                        '     <td>\n' +
                                                        '         <button class="deleteWorkinfo btn btn-danger">\n' +
                                                        '             <i class="fa fa-trash"></i>\n' +
                                                        '         </button>\n' +
                                                        '     </td>\n' +
                                                        '  </tr>';
                                                }
                                                $('.tableWorkInfo').append(str);
                                            }
                                        }
                                    });
                                }
                            }
                            if (currentIndex == 2) {
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
                                                oTable.draw();
                                            } else {
                                                str = '';
                                                var errors = data['errors'];
                                                $('.tableBankInfo').html(str);
                                                for (var index in data['inputs']['account_number']) {
                                                    var work = data['inputs'];
                                                    if (work['account_number'][index] == null) {
                                                        work['account_number'][index] = '';
                                                    }
                                                    if (work['bank_name'][index] == null) {
                                                        work['bank_name'][index] = '';
                                                    }
                                                    str += '<tr>\n' +
                                                        '     <td>\n' +
                                                        '         <input class="form-control" type="text" name="account_number[]"\n' +
                                                        '                value="' + work['account_number'][index] + '"\n' +
                                                        '                placeholder="Account Number" required>\n';
                                                    if (errors['account_number.' + index] != undefined) {
                                                        str += '          <label class="error">' + errors['account_number.' + index] + '</label>';
                                                    }
                                                    str += '     </td>\n' +
                                                        '     <td>\n' +
                                                        '         <input class="form-control" type="text" name="bank_name[]"\n' +
                                                        '                value="' + work['bank_name'][index] + '"\n' +
                                                        '                placeholder="Bank Name" required>\n';
                                                    if (errors['bank_name.' + index] != undefined) {
                                                        str += '          <label class="error">' + errors['bank_name.' + index] + '</label>';
                                                    }
                                                    str += '     </td>\n' +
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
                            if (currentIndex == 3) {
                                if ($('#usersReferenceInfoForm').valid()) {
                                    $.ajax({
                                        dataType: 'json',
                                        method: 'post',
                                        url: $('#usersReferenceInfoForm').attr('action'),
                                        data: $('#usersReferenceInfoForm').serialize(),
                                        success: function (data) {
                                            if (data['status']) {
                                                process = true;
                                                steps.steps('next');
                                                oTable.draw();
                                            } else {
                                                str = '';
                                                var errors = data['errors'];
                                                $('.tableReferenceInfo').html(str);
                                                for (var index in data['inputs']['name']) {
                                                    var work = data['inputs'];
                                                    if (work['name'][index] == null) {
                                                        work['name'][index] = '';
                                                    }
                                                    if (work['tel1'][index] == null) {
                                                        work['tel1'][index] = '';
                                                    }
                                                    if (work['tel2'][index] == null) {
                                                        work['tel2'][index] = '';
                                                    }
                                                    if (work['email'][index] == null) {
                                                        work['email'][index] = '';
                                                    }

                                                    str += '<tr>\n' +
                                                        '     <td>\n' +
                                                        '         <input class="form-control" type="text" name="name[]"\n' +
                                                        '                value="' + work['name'][index] + '"\n' +
                                                        '                placeholder="Name" required>\n';
                                                    if (errors['name.' + index] != undefined) {
                                                        str += '          <label class="error">' + errors['name.' + index] + '</label>';
                                                    }
                                                    str += '     </td>\n' +
                                                        '     <td>\n' +
                                                        '         <input class="form-control" type="number" name="tel1[]"\n' +
                                                        '                value="' + work['tel1'][index] + '"\n' +
                                                        '                placeholder="Telephone 1" required>\n';
                                                    if (errors['tel1.' + index] != undefined) {
                                                        str += '          <label class="error">' + errors['tel1.' + index] + '</label>';
                                                    }
                                                    str += '     </td>\n' +
                                                        '     <td>\n' +
                                                        '         <input class="form-control" type="number" name="tel2[]"\n' +
                                                        '                value="' + work['tel2'][index] + '"\n' +
                                                        '                placeholder="Telephone 2" required>\n';
                                                    if (errors['tel2.' + index] != undefined) {
                                                        str += '          <label class="error">' + errors['tel2.' + index] + '</label>';
                                                    }
                                                    str += '     </td>\n' +
                                                        '     <td>\n' +
                                                        '         <input class="form-control" type="text" name="email[]" value="' + work['email'][index] + '"\n' +
                                                        '                placeholder="Email" required>\n';
                                                    if (errors['email.' + index] != undefined) {
                                                        str += '          <label class="error">' + errors['email.' + index] + '</label>';
                                                    }
                                                    str += '     </td>\n' +
                                                        '     <td>\n' +
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
                                }
                            }

                        }
                    }
                },
                onStepChanged: function (event, currentIndex, priorIndex) {
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
                    if ($('#usersReferenceInfoForm').valid()) {
                        $.ajax({
                            dataType: 'json',
                            method: 'post',
                            url: $('#usersReferenceInfoForm').attr('action'),
                            data: $('#usersReferenceInfoForm').serialize(),
                            success: function (data) {
                                if (data['status']) {
                                    $('#userModal').modal('hide');
                                } else {
                                    str = '';
                                    var errors = data['errors'];
                                    $('.tableReferenceInfo').html(str);
                                    for (var index in data['inputs']['name']) {
                                        var work = data['inputs'];
                                        if (work['name'][index] == null) {
                                            work['name'][index] = '';
                                        }
                                        if (work['tel1'][index] == null) {
                                            work['tel1'][index] = '';
                                        }
                                        if (work['tel2'][index] == null) {
                                            work['tel2'][index] = '';
                                        }
                                        if (work['email'][index] == null) {
                                            work['email'][index] = '';
                                        }

                                        str += '<tr>\n' +
                                            '     <td>\n' +
                                            '         <input class="form-control" type="text" name="name[]"\n' +
                                            '                value="' + work['name'][index] + '"\n' +
                                            '                placeholder="Name" required>\n';
                                        if (errors['name.' + index] != undefined) {
                                            str += '          <label class="error">' + errors['name.' + index] + '</label>';
                                        }
                                        str += '     </td>\n' +
                                            '     <td>\n' +
                                            '         <input class="form-control" type="number" name="tel1[]"\n' +
                                            '                value="' + work['tel1'][index] + '"\n' +
                                            '                placeholder="Telephone 1" required>\n';
                                        if (errors['tel1.' + index] != undefined) {
                                            str += '          <label class="error">' + errors['tel1.' + index] + '</label>';
                                        }
                                        str += '     </td>\n' +
                                            '     <td>\n' +
                                            '         <input class="form-control" type="number" name="tel2[]"\n' +
                                            '                value="' + work['tel2'][index] + '"\n' +
                                            '                placeholder="Telephone 2" required>\n';
                                        if (errors['tel2.' + index] != undefined) {
                                            str += '          <label class="error">' + errors['tel2.' + index] + '</label>';
                                        }
                                        str += '     </td>\n' +
                                            '     <td>\n' +
                                            '         <input class="form-control" type="text" name="email[]" value="' + work['email'][index] + '"\n' +
                                            '                placeholder="Email" required>\n';
                                        if (errors['email.' + index] != undefined) {
                                            str += '          <label class="error">' + errors['email.' + index] + '</label>';
                                        }
                                        str += '     </td>\n' +
                                            '     <td>\n' +
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
                    }
                },
                onFinished: function (event, currentIndex) {
                    oTable.draw();
                }
            });
        }

        $(document).on('click', '.addNew', function (e) {
            e.preventDefault();
            $('#usersInfoForm').attr('action', ajaxURL + 'users');
            wizardInit('add');
        });

        $(document).on('click', '.addUserWorkInfo', function (e) {
            e.preventDefault();
            str = '<tr>\n' +
                '     <td>\n' +
                '         <input class="form-control" type="text" name="company_name[]"\n' +
                '                value=""\n' +
                '                placeholder="Company Name" required>\n' +
                '     </td>\n' +
                '     <td>\n' +
                '         <input class="form-control" type="text" name="function[]"\n' +
                '                value=""\n' +
                '                placeholder="Function In Company" required>\n' +
                '     </td>\n' +
                '     <td>\n' +
                '         <input class="form-control" type="text" name="telephone[]"\n' +
                '                value=""\n' +
                '                placeholder="Telephone" required>\n' +
                '     </td>\n' +
                '     <td>\n' +
                '         <input class="form-control" type="text" name="mail[]" value=""\n' +
                '                placeholder="Email" required>\n' +
                '     </td>\n' +
                '     <td>\n' +
                '         <button class="deleteWorkinfo btn btn-danger">\n' +
                '             <i class="fa fa-trash"></i>\n' +
                '         </button>\n' +
                '     </td>\n' +
                '  </tr>';
            $('.tableWorkInfo').append(str);
        });

        $(document).on('click', '.deleteWorkinfo', function (e) {
            e.preventDefault();
            $(this).parent().parent().remove();
        });

        $(document).on('click', '.addUserBankInfo', function (e) {
            e.preventDefault();
            str = '<tr>\n' +
                '     <td>\n' +
                '         <input class="form-control" type="text" name="account_number[]"\n' +
                '                value=""\n' +
                '                placeholder="Account Number" required>\n' +
                '     </td>\n' +
                '     <td>\n' +
                '         <input class="form-control" type="text" name="bank_name[]"\n' +
                '                value=""\n' +
                '                placeholder="Bank Name" required>\n' +
                '     </td>\n' +
                '     <td>\n' +
                '         <button class="deleteWorkinfo btn btn-danger">\n' +
                '             <i class="fa fa-trash"></i>\n' +
                '         </button>\n' +
                '     </td>\n' +
                '  </tr>';
            $('.tableBankInfo').append(str);
        });

        $(document).on('click', '.addUserReferenceInfo', function (e) {
            e.preventDefault();
            str = '<tr>\n' +
                '     <td>\n' +
                '         <input class="form-control" type="text" name="name[]" value="" placeholder="Name" required>' +
                '     </td>\n' +
                '     <td>\n' +
                '         <input class="form-control" type="number" name="tel1[]" value="" placeholder="Telephone 1" required>' +
                '     </td>\n' +
                '     <td>\n' +
                '         <input class="form-control" type="number" name="tel2[]" value="" placeholder="Telephone 2" required>\n' +
                '     </td>\n' +
                '     <td>\n' +
                '         <input class="form-control" type="email" name="email[]" value="" placeholder="Email" required>\n' +
                '     </td>\n' +
                '     <td>\n' +
                '         <button class="deleteWorkinfo btn btn-danger">\n' +
                '             <i class="fa fa-trash"></i>\n' +
                '         </button>\n' +
                '     </td>\n' +
                '  </tr>';
            $('.tableReferenceInfo').append(str);
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
                            '     <td>\n' +
                            '         <input class="form-control" type="text" name="account_number[]"\n' +
                            '                value="' + bank['account_number'] + '"\n' +
                            '                placeholder="Account Number" required>\n' +
                            '     </td>\n' +
                            '     <td>\n' +
                            '         <input class="form-control" type="text" name="bank_name[]"\n' +
                            '                value="' + bank['bank_name'] + '"\n' +
                            '                placeholder="Bank Name" required>\n' +
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
                    for (var index in data['works']) {
                        var work = data['works'][index];

                        str += '<tr>\n' +
                            '     <td>\n' +
                            '         <input class="form-control" type="text" name="company_name[]"\n' +
                            '                value="' + work['company_name'] + '"\n' +
                            '                placeholder="Company Name" required>\n' +
                            '     </td>\n' +
                            '     <td>\n' +
                            '         <input class="form-control" type="text" name="function[]"\n' +
                            '                value="' + work['function'] + '"\n' +
                            '                placeholder="Function In Company" required>\n' +
                            '     </td>\n' +
                            '     <td>\n' +
                            '         <input class="form-control" type="text" name="telephone[]"\n' +
                            '                value="' + work['telephone'] + '"\n' +
                            '                placeholder="Telephone" required>\n' +
                            '     </td>\n' +
                            '     <td>\n' +
                            '         <input class="form-control" type="text" name="mail[]" value="' + work['mail'] + '"\n' +
                            '                placeholder="Email" required>\n' +
                            '     </td>\n' +
                            '     <td>\n' +
                            '         <button class="deleteWorkinfo btn btn-danger">\n' +
                            '             <i class="fa fa-trash"></i>\n' +
                            '         </button>\n' +
                            '     </td>\n' +
                            '  </tr>';
                    }
                    $('.tableWorkInfo').append(str);
                }
            })
        }

        function getUserReferenceInfo(user_id) {
            $.ajax({
                dataType: 'json',
                method: 'get',
                url: ajaxURL + 'users/' + user_id + '/references',
                success: function (data) {
                    str = '';
                    $('.tableReferenceInfo').html(str);
                    for (var index in data['references']) {
                        var reference = data['references'][index];

                        str += '<tr>\n' +
                            '     <td>\n' +
                            '         <input class="form-control" type="text" name="name[]" value="' + reference['name'] + '" placeholder="Name" required>' +
                            '     </td>\n' +
                            '     <td>\n' +
                            '         <input class="form-control" type="number" name="tel1[]" value="' + reference['tel1'] + '" placeholder="Telephone 1" required>' +
                            '     </td>\n' +
                            '     <td>\n' +
                            '         <input class="form-control" type="number" name="tel2[]" value="' + reference['tel2'] + '" placeholder="Telephone 2" required>\n' +
                            '     </td>\n' +
                            '     <td>\n' +
                            '         <input class="form-control" type="email" name="email[]" value="' + reference['email'] + '" placeholder="Email" required>\n' +
                            '     </td>\n' +
                            '     <td>\n' +
                            '         <button class="deleteWorkinfo btn btn-danger">\n' +
                            '             <i class="fa fa-trash"></i>\n' +
                            '         </button>\n' +
                            '     </td>\n' +
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
                    if (data.inputs.profile_pic.value != '') {
                        $('.profile-pic-holder').show();
                        $('.profile-pic-holder').find('img').attr('src', data.inputs.profile_pic.value);
                    } else {
                        $('.profile-pic-holder').hide();
                        $('.profile-pic-holder').find('img').attr('src', '');
                    }
                    $('#userModal').modal('show');
                    if (type == 'view') {
                        setTimeout(function () {
                            $('#userModal').find('input, select').attr('disabled', 'disabled');
                            $('#userModal').find('button[type="submit"]').hide();
                            $('#userModal').find('#deleteImage').hide();
                        }, 100);
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
                    $('#userModal').modal('hide');
                    oTable.draw(true);
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

        function toggleStatus(element) {
            var role = $(element).val();
            if (role == 3) {
                $('.status-group').show();
            } else {
                $('.status-group').hide();
            }
        }
    </script>
    @if(request('user_id'))
        <script>
            var user_id = "{!! request('user_id') !!}";
            setEdit(user_id, 'view');
        </script>
    @endif
@endsection
