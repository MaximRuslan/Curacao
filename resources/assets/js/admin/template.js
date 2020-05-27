var template = {
    el: {
        form: '#template_form',
        editButton: ".editTemplate",
        modal: '#templateModal',
    },
    data: {
        datatable: '',
    },
    init() {
        var _this = this;
        _this.validationForm(adminAjaxURL + 'templates', 'post');
        if (template_type == 1) {
            $('textarea.cms_textarea').trumbowyg({
                svgPath: '../resources/css/admin/icons.svg'
            });
        }
        _this.bindUiActions();
    },
    bindUiActions() {
        var _this = this;
        _this.data.datatable = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": adminAjaxURL + 'datatable-templates',
                data: function (d) {
                    d.type = template_type;
                }
            },
            columns: [
                {data: 'id', name: 'templates.id', visible: false},
                {data: 'name', name: 'templates.name'},
                {data: 'receivers', name: 'templates.receivers'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            order: [[0, 'desc']],
            drawCallback(settings) {
                initTooltip();
            },
        });

        $(document).on('click', _this.el.editButton, function (e) {
            e.preventDefault();
            _this.formReset();
            _this.setEdit($(this).data('id'), $(this).data('type'));
        });

    },
    formReset() {
        var _this = this;
        $(_this.el.form).find('[name="id"]').val('');
        $(_this.el.form).find('input').prop('disabled', false);
        $(_this.el.form).find('select').prop('disabled', false);
        $(_this.el.form).find('button[type="submit"]').show();
        $('#country_id').val('').select2();
        $(_this.el.form)[0].reset();
    },
    setEdit(id, type) {
        var _this = this
        $.ajax({
            dataType: 'json',
            method: 'get',
            url: adminAjaxURL + 'templates/' + id + '/edit',
            success(data) {
                setForm(_this.el.form, data['inputs']);
                if (data['type'] == 3) {
                    $('.js--subject-div').hide();
                } else {
                    $('.js--subject-div').show();
                }
                if (type == 'view') {
                    $(_this.el.form).find('input').prop('disabled', true);
                    $(_this.el.form).find('select').prop('disabled', true);
                    $(_this.el.form).find('textarea').prop('disabled', true);
                    $(_this.el.form).find('button[type="submit"]').hide();
                }
                $('#js--key-tooltip').attr('title', data['inputs']['key']['value']);
                initTooltip();
                $('[for="params"]').html(data['inputs']['params']['value']);
                $('[for="receivers"]').html(data['inputs']['receivers']['value']);
                $(_this.el.modal).modal('show');
                setTimeout(function () {
                    $('#language-eng-click').click();
                }, 1000);
            }
        })
    },
    validationForm(url, method) {
        var _this = this;
        $(_this.el.form).data('validator', null);
        $(_this.el.form).unbind();

        validator = $(_this.el.form).validate({
            rules: {
                name: {required: true},
                subject: {required: true},
                subject_esp: {required: true},
                subject_pap: {required: true},
                content: {required: true},
                content_esp: {required: true},
                content_pap: {required: true},
            },

            errorPlacement(error, element) {
                $('span[for="' + $(element).attr('name') + '"]').html(error);
                $(element).removeClass('error');
            },

            submitHandler(form) {
                $.ajax({
                    dataType: 'json',
                    method: method,
                    url: url,
                    data: $(form).serialize(),
                    success: function (data) {
                        _this.data.datatable.draw();
                        $(_this.el.modal).modal('hide');
                    }
                })
            }
        });
    }
};