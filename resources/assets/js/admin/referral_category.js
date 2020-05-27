var referral_category = {
    el: {
        form: '#referral_category_form',
        addButton: ".addCategory",
        editButton: ".editCategory",
        deleteButton: ".deleteCategory",
        modal: '#referralCategoryModal',
        deleteReferralCategoryModal: '#deleteReferralCategoryModal',
        confirmDeleteReferralCategoryButton: '.confirmDeleteReferralCategoryButton',
    },
    data: {
        datatable: '',
    },
    init() {
        var _this = this;
        $('#country_id').select2();
        _this.validationForm(adminAjaxURL + 'referral-categories', 'post');
        _this.bindUiActions();
    },
    bindUiActions() {
        var _this = this;
        _this.data.datatable = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": adminAjaxURL + 'datatable-referral-categories',
            },
            columns: [
                {data: 'id', name: 'referral_categories.id', visible: false},
                {data: 'country_name', name: 'countries.name'},
                {data: 'title', name: 'referral_categories.title'},
                {data: 'min_referrals', name: 'referral_categories.min_referrals'},
                {data: 'max_referrals', name: 'referral_categories.max_referrals'},
                {data: 'loan_start', name: 'referral_categories.loan_start'},
                {data: 'loan_pif', name: 'referral_categories.loan_pif'},
                {data: 'status', name: 'referral_categories.status'},
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
            $(_this.el.deleteReferralCategoryModal).find(_this.el.confirmDeleteReferralCategoryButton).data('id', $(this).data('id'));
            $(_this.el.deleteReferralCategoryModal).modal('show');
        });

        $(document).on('click', _this.el.deleteReferralCategoryModal + ' ' + _this.el.confirmDeleteReferralCategoryButton, function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'delete',
                url: adminAjaxURL + 'referral-categories/' + $(this).data('id'),
                success: function (data) {
                    _this.data.datatable.draw();
                    $(_this.el.deleteReferralCategoryModal).modal('hide');
                }
            })
        });

    },
    formReset() {
        var _this = this;
        $(_this.el.form).find('[name="id"]').val('');
        $(_this.el.form).find('input').prop('disabled', false);
        $(_this.el.form).find('select').prop('disabled', false);
        $(_this.el.form).find('button[type="submit"]').show();
        $(_this.el.form).find('.error').html('');
        $('#country_id').val('').select2();
        $(_this.el.form)[0].reset();
    },
    setEdit(id, type) {
        var _this = this
        $.ajax({
            dataType: 'json',
            method: 'get',
            url: adminAjaxURL + 'referral-categories/' + id + '/edit',
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
                title: {required: true},
                min_referrals: {required: true},
                loan_start: {required: true},
                loan_pif: {required: true},
                status: {required: true},
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
                    },
                    error: function (xhr) {
                        $(_this.el.form).find('.error').html('');
                        let errors = xhr.responseJSON;
                        for (let i in errors) {
                            $(_this.el.form).find('.error[for="' + i + '"]').html(errors[i][0]);
                        }
                    }
                });
            }
        });
    }
};