var push_notifications = {
    el: {
        form: '#messageModelForm',
        addButton: ".addMessage",
        modal: '#messageModel',
    },
    data: {
        datatable: '',
    },
    init() {
        var _this = this;
        _this.validationForm(adminAjaxURL + 'push-notifications', 'post');
        $('.select2single').select2();
        $('#user_select').select2({
            placeholder: 'All',
            closeOnSelect: false
        });
        _this.bindUiActions();
        _this.getUsers();
    },
    bindUiActions() {
        var _this = this;
        _this.data.datatable = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": adminAjaxURL + 'datatable-push-notifications',
            },
            columns: [
                {data: 'id', name: 'firebase_notifications.id', visible: false},
                {data: 'user_name', name: 'users.lastname'},
                {data: 'user_name', name: 'users.firstname', visible: false},
                {data: 'title', name: 'firebase_notifications.title'},
                {data: 'body', name: 'firebase_notifications.body'},
                {data: 'type', name: 'firebase_notifications.type'},
                {data: 'created_at', name: 'firebase_notifications.created_at', searchable: false},
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

        $(document).on('change', '#country_select, #status_select, #loans_select', function (e) {
            e.preventDefault();
            _this.getUsers();
        });

        $(document).on('change', '#select_all_checkbox', function () {
            if ($('#select_all_checkbox:checked').val() == 1) {
                $('#user_select').val('').select2({
                    'placeholder': 'All',
                });
                $('#user_select').prop('disabled', true).select2({
                    'placeholder': 'All',
                });
            } else {
                $('#user_select').prop('disabled', false).select2({
                    'placeholder': 'All',
                    closeOnSelect: false
                });
            }
        });
    },
    getUsers() {
        let country = $('#country_select').val();
        if (country == '') {
            country = 0;
        }
        $.ajax({
            dataType: 'json',
            method: 'get',
            url: adminAjaxURL + 'countries/' + country + '/users',
            data: {
                status: $('[name="status"]').val(),
                loan: $('[name="loan"]').val()
            },
            success: function (data) {
                str = '';
                for (var index in data['users']) {
                    str += '<option value="' + index + '">' + data['users'][index] + '</option>';
                }
                $('#user_select').html(str);
                $('#user_select').prop('disabled', false).select2({
                    'placeholder': 'All',
                    closeOnSelect: false
                });
            }
        });
    },
    formReset() {
        var _this = this;
        $(_this.el.form).find('[name="id"]').val('');
        $(_this.el.form).find('input').prop('disabled', false);
        $(_this.el.form).find('select').prop('disabled', false);
        $(_this.el.form).find('button[type="submit"]').show();
        $('#country_select').val('').select2();
        $('#status_select').val('').select2();
        $('#loans_select').val('').select2();
        $('#user_select').select2({
            'placeholder': 'All',
            closeOnSelect: false
        });
        $(_this.el.form)[0].reset();
    },
    validationForm(url, method) {
        var _this = this;
        $(_this.el.form).data('validator', null);
        $(_this.el.form).unbind();

        validator = $(_this.el.form).validate({
            rules: {
                title: {required: true},
                body: {required: true},
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