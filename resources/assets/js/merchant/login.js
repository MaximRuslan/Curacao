const login = {
    el: {
        form: '#js--login-form'
    },

    data: {},

    init() {
        let _this = this;
        _this.bindUiActions();

        let inputs = {
            form: _this.el.form,
            rules: {
                email: {required: true},
                password: {required: true}
            },
            submit_type: 'normal'
        };

        common.validationForm(inputs);
    },

    bindUiActions() {
        $(".forgot").click(function () {
            $(".help-block").html('<strong</strong>');
            $("#reset-password-modal").modal("show");
        });
    }
};

login.init();