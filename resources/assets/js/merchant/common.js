const common = {
    el: {
        changePasswordOpen: '.changePasswordOpen',
        changePasswordModal: '#changePasswordModal',
        changePasswordForm: '#changePasswordForm',
    },

    data: {},

    init() {
        let _this = this;
        _this.bindUiActions();
        $('.select2Single').select2();
    },

    bindUiActions() {
        let _this = this;
        $(document).on('change', '#js--branch-id', function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'post',
                url: merchant_ajax_url + 'branches/' + $(this).val(),
                success(data) {
                    location.reload();
                }
            });
        });

        $(document).on('click', _this.el.changePasswordOpen, function (e) {
            e.preventDefault();
            _this.formPasswordReset();
            $(_this.el.changePasswordModal).modal('show');
        });
        $(document).on('submit', _this.el.changePasswordForm, function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'post',
                url: merchant_ajax_url + 'profile/password',
                data: $(this).serialize(),
                success: function (data) {
                    $(_this.el.changePasswordModal).modal('hide');
                    _this.formPasswordReset();
                    window.location.reload();
                },
                error: function (jqXHR) {
                    var Response = jqXHR.responseText;
                    Response = $.parseJSON(Response);
                    common.displayErrorMessages(Response, $(_this.el.changePasswordForm), 'input');
                }
            });
        });
    },
    formPasswordReset: function () {
        var _this = this;
        $(_this.el.changePasswordForm)[0].reset();
        $(_this.el.changePasswordForm).find('.text-danger').html('');
        $(_this.el.changePasswordForm).find('.alertDiv').html('');
    },

    validationForm(inputs) {
        var _this = this;
        $(inputs.form).data('validator', null);
        $(inputs.form).unbind();

        validator = $(inputs.form).validate({
            rules: inputs.rules,
            errorPlacement(error, element) {
                $('span[for="' + $(element).attr('name') + '"]').html(error);
                $(element).removeClass('error');
            },

            submitHandler(form) {
                if (inputs.beforeSubmit != undefined) {
                    inputs.beforeSubmit();
                }
                if (inputs.submit_type != undefined && inputs.submit_type == 'normal') {
                    form.submit();
                } else {
                    $(form).find('.has-danger').removeClass('has-danger');
                    $(form).find('.has-error').removeClass('has-error');
                    let url = '';
                    if (inputs.url_type !== undefined && inputs.url_type == 'front') {
                        url += ajax_url + inputs.url;
                    } else {
                        url += merchant_ajax_url + inputs.url;
                    }
                    if (inputs.method == 'put' || inputs.method == 'PUT') {
                        form_data.append('_method', 'PUT');
                        inputs.method = 'post';
                    }
                    if (inputs['type'] != undefined && inputs['type'] == 'files') {
                        var form_data = new FormData($(form)[0]);
                        $.ajax({
                            dataType: 'json',
                            method: inputs.method,
                            url: url,
                            data: form_data,
                            processData: false,
                            contentType: false,
                            success: function (data) {
                                inputs.callback(data);
                            },
                            error: function (jqXHR) {
                                let Response = jqXHR.responseJSON.errors;
                                let ErrorBlock = $(form);
                                _this.displayErrorMessages(Response, ErrorBlock, 'input');
                            }
                        });
                    } else {
                        $.ajax({
                            dataType: 'json',
                            method: inputs.method,
                            url: url,
                            data: $(form).serialize(),
                            success: function (data) {
                                inputs.callback(data);
                            },
                            error: function (jqXHR) {
                                let Response = jqXHR.responseJSON.errors;
                                let ErrorBlock = $(form);
                                _this.displayErrorMessages(Response, ErrorBlock, 'input');
                            }
                        });
                    }
                }
            }
        });
    },

    displayErrorMessages(Response, ErrorBlock, type) {
        var ErrorHtml = "";
        $.each(Response, function (index, element) {
            ErrorHtml += "<li>" + element + "</li>";
        });
        if (type == 'ul') {
            ErrorBlock.find('ul').html(ErrorHtml);
            ErrorBlock.slideDown('1000');
        } else if (type == 'toaster') {
            $.Notification.notify(
                'error',
                'top right',
                'Error !',
                ErrorHtml
            );
        } else if (type == 'input') {
            var Form = ErrorBlock;
            Form.find('span.error').html('');
            $.each(Response, function (index, element) {
                var parts = index.split('.');
                if (parts.length > 1) {
                    var str = '';
                    for (var index in parts) {
                        if (index == 0) {
                            str = parts[0];
                        } else {
                            str += '[' + parts[1] + ']';
                        }
                    }
                    index = str;
                }
                $(Form).find('span[for="' + index + '"]').html(element);
            });
        }
    },

    datatable(inputs) {
        if (inputs['selector'] == undefined) {
            inputs['selector'] = '#indexDatatable';
        }
        if (inputs['order'] == undefined) {
            inputs['order'] = [0, 'desc'];
        }
        if (inputs.callback == undefined) {
            inputs.callback = function () {
            }
        }
        if (inputs['pageLength'] == undefined) {
            inputs['pageLength'] = 10;
        }
        if (inputs['paginate'] == undefined) {
            inputs['paginate'] = true;
        }
        if (inputs['type'] !== undefined && inputs['type'] == 'front') {
            inputs['url'] = merchant_url + inputs['url'];
        } else {
            inputs['url'] = merchant_ajax_url + inputs['url'];
        }
        if (inputs['lengthMenu'] == undefined) {
            inputs['lengthMenu'] = [10, 25, 50, 100];
        } else {
            inputs['pageLength'] = inputs['lengthMenu'][0];
        }
        var datatable = $(inputs['selector']).DataTable({
            ajax: {
                url: inputs['url'],
                data: function (d) {
                    // d.search['value'] = $('#generalSearch').val();
                    for (let i in inputs['data']) {
                        d[i] = inputs['data'][i];
                    }
                }
            },
            lengthMenu: inputs['lengthMenu'],
            responsive: true,
            searchDelay: 500,
            processing: true,
            serverSide: true,
            paginate: inputs['paginate'],
            columns: inputs['columns'],
            order: [inputs['order']],
            pageLength: inputs['pageLength'],
            drawCallback: function (settings) {
                $('[data-toggle="tooltip"]').tooltip();
                inputs.callback(settings);
            },
            "language": {
                "lengthMenu": keywords.Show + " _MENU_ " + keywords.Entries,
                "zeroRecords": keywords.NoMatchingRecordsFound,
                "info": keywords.Showing + ('_END_' == 0 ? 0 : " _START_ ") + keywords.To + " _END_ " + keywords.Of + " _TOTAL_ " + keywords.Entries,
                "infoEmpty": keywords.Showing + " 0 " + keywords.To + " 0 " + keywords.Of + " 0 " + keywords.Entries,
                "infoFiltered": "(" + keywords.FilteredFrom + " _MAX_ " + keywords.TotalRecords + ")",
                "search": keywords.Search,
                "paginate": {
                    "previous": keywords.Previous,
                    "next": keywords.Next,
                },
            },
        });
        //
        // $('#generalSearch').on('keydown keyup blur change', $.debounce(500, function () {
        //     datatable.draw();
        // }));

        return datatable;
    },
};

common.init();