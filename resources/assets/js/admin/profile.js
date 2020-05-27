var profile = {
    el: {
        profileModal: '#profileModal',
        profileForm: '#profileForm',
        profileOpen: '.profileOpen',
        deleteProfilePic: '.deleteProfilePic',
        changePasswordOpen: '.changePasswordOpen',
        changePasswordModal: '#changePasswordModal',
        changePasswordForm: '#changePasswordForm',
        languageSelect: '#lang_select',
        countrySelect: '#js__country_site',
    },
    data: {},
    init: function () {
        var _this = this;
        _this.bindUiActions();
        $(_this.el.languageSelect).select2();
        // $(_this.el.countrySelect).select2();
    },
    bindUiActions: function () {
        var _this = this;
        $(document).on('click', _this.el.profileOpen, function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'get',
                url: adminAjaxURL + 'profile',
                success: function (data) {
                    setForm($(_this.el.profileForm), data['user']);
                    $(_this.el.profileModal).modal('show');
                }
            });
        });
        $(document).on('click', _this.el.deleteProfilePic, function (e) {
            $.ajax({
                dataType: 'json',
                method: 'delete',
                url: adminAjaxURL + 'profile-pic',
                success: function (data) {
                    $(_this.el.profileModal).modal('hide');
                    window.location.reload();
                }
            });
        });
        $(document).on('submit', _this.el.profileForm, function (e) {
            e.preventDefault();
            var formData = new FormData($(this)[0]);
            $.ajax({
                datdType: 'json',
                method: 'post',
                url: adminAjaxURL + 'profile',
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {
                    $(_this.el.profileModal).modal('hide');
                    window.location.reload();
                }
            })
        });
        $(document).on('click', '.changeEmailAddress', function (e) {
            e.preventDefault();
            $(_this.el.profileModal).modal('hide');
            swal({
                title: 'Change Email',
                input: 'text',
                showCancelButton: true,
                confirmButtonText: 'Submit',
                showLoaderOnConfirm: true,
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger m-l-10',
                preConfirm: function (email) {
                    return new Promise(function (resolve, reject) {
                        $.ajax({
                            dataType: 'json',
                            method: 'post',
                            url: adminAjaxURL + 'profile/email',
                            data: {
                                email: email
                            },
                            success: function (data) {
                                location.reload();
                                resolve();
                            },
                            error: function (errors) {
                                reject(errors.responseJSON.email[0]);
                            }
                        });
                    })
                },
                allowOutsideClick: false
            }).then(function (email) {
                swal({
                    type: 'success',
                    title: 'Email successfully changed! Please verify your email and login again.',
                    html: 'Submitted email: ' + email
                }, function () {
                    location.reload();
                });
            });
        });
        $(document).on('click', '.changePasswordOpen', function (e) {
            e.preventDefault();
            _this.formPasswordReset();
            $(_this.el.changePasswordModal).modal('show');
        });
        $(document).on('submit', _this.el.changePasswordForm, function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'post',
                url: adminAjaxURL + 'profile/password',
                data: $(this).serialize(),
                success: function (data) {
                    $(_this.el.changePasswordModal).modal('hide');
                    _this.formPasswordReset();
                    window.location.reload();
                },
                error: function (jqXHR) {
                    var Response = jqXHR.responseText;
                    Response = $.parseJSON(Response);
                    displayErrorMessages(Response, $(_this.el.changePasswordForm), 'input');
                    fullLoader.off();
                }
            });
        });

        $(document).on('change', _this.el.countrySelect, function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'post',
                url: adminAjaxURL + 'profile/country',
                data: {
                    country: $(this).val()
                },
                success: function (data) {
                    location.reload();
                }
            })
        });
    },
    formReset: function () {
        var _this = this;
        $(_this.el.profileForm)[0].reset();
        $(_this.el.profileForm).find('.text-danger').html('');
        $(_this.el.profileForm).find('.alertDiv').html('');
    },
    formPasswordReset: function () {
        var _this = this;
        $(_this.el.changePasswordForm)[0].reset();
        $(_this.el.changePasswordForm).find('.text-danger').html('');
        $(_this.el.changePasswordForm).find('.alertDiv').html('');
    },
};