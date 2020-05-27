var nlbReason = {
    el: {
        form: '#nlb_reason_form',
        addButton: ".addReason",
        editButton: ".editReason",
        deleteButton: ".deleteReason",
        modal: '#nlbReasonsModal',
        deleteNlbReasonModal: '#deleteNlbReasonModal',
        confirmDeleteNlbReasonButton: '.confirmDeleteNlbReasonButton',
    },
    data: {
        datatable: '',
    },
    init() {
        var _this = this;
        _this.validationForm(adminAjaxURL + 'nlb-reasons', 'post');
        $('.select2single').select2();
        _this.bindUiActions();
    },
    bindUiActions() {
        var _this = this;
        _this.data.datatable = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": adminAjaxURL + 'datatable-nlb-reasons',
            },
            columns: [
                {data: 'id', name: 'id', visible: false},
                {data: 'type', name: 'type'},
                {data: 'title', name: 'name'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            order: [[0, 'desc']],
            drawCallback(settings) {
                initTooltip();
            },
        });

        $(document).on('click', _this.el.addButton, function (e) {
            e.preventDefault();
            _this.formReset();
            $(_this.el.modal).modal('show');
        });

        $(document).on('click', _this.el.editButton, function (e) {
            e.preventDefault();
            _this.formReset();
            _this.setEdit($(this).data('id'), $(this).data('type'));
        });

        $(document).on('click', _this.el.deleteButton, function (e) {
            e.preventDefault();
            $(_this.el.deleteNlbReasonModal).find(_this.el.confirmDeleteNlbReasonButton).data('id', $(this).data('id'));
            $(_this.el.deleteNlbReasonModal).modal('show');
        });

        $(document).on('click', _this.el.deleteNlbReasonModal + ' ' + _this.el.confirmDeleteNlbReasonButton, function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'delete',
                url: adminAjaxURL + 'nlb-reasons/' + $(this).data('id'),
                success: function (data) {
                    _this.data.datatable.draw();
                    $(_this.el.deleteNlbReasonModal).modal('hide');
                }
            })
        });

    },
    formReset() {
        var _this = this;
        $('.select2single').val('').select2();
        $(_this.el.form).find('[name="id"]').val('');
        $(_this.el.form).find('input').prop('disabled', false);
        $(_this.el.form).find('select').prop('disabled', false);
        $(_this.el.form).find('button[type="submit"]').show();
        $(_this.el.form)[0].reset();
    },
    setEdit(id, type) {
        var _this = this
        $.ajax({
            dataType: 'json',
            method: 'get',
            url: adminAjaxURL + 'nlb-reasons/' + id + '/edit',
            success(data) {
                setForm(_this.el.form, data['inputs']);
                if (type == 'view') {
                    $(_this.el.form).find('input').prop('disabled', true);
                    $(_this.el.form).find('select').prop('disabled', true);
                    $(_this.el.form).find('button[type="submit"]').hide();
                }
                $(_this.el.modal).modal('show');
            }
        })
    },
    validationForm(url, method) {
        var _this = this;
        $(_this.el.form).data('validator', null);
        $(_this.el.form).unbind();

        validator = $(_this.el.form).validate({
            rules: {
                'title': {required: true},
                'title_es': {required: true},
                'title_nl': {required: true},
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