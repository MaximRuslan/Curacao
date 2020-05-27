var country = {
    el: {
        form: '#country_form',
        addButton: ".addCountry",
        editButton: ".editCountry",
        deleteButton: ".deleteCountry",
        modal: '#countryModal',
        deleteCountryModal: '#deleteCountryModal',
        confirmDeleteCountryButton: '.confirmDeleteCountryButton',
    },
    data: {
        datatable: '',
    },
    init() {
        var _this = this;
        _this.validationForm(adminAjaxURL + 'countries', 'post');
        /*var tiny_mce = tinymce.init({
            selector: "textarea.cms_textarea",
            theme: "modern",
            height: 300,
            plugins: [
                "advlist autolink link lists charmap print preview hr anchor pagebreak spellchecker",
                "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime nonbreaking",
                "save table contextmenu directionality emoticons template paste textcolor"
            ],
            toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | l      ink | print preview fullpage | forecolor backcolor emoticons",
            // style_formats: [
            //     {title: 'Bold text', inline: 'b'},
            //     {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
            //     {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
            //     {title: 'Example 1', inline: 'span', classes: 'example1'},
            //     {title: 'Example 2', inline: 'span', classes: 'example2'},
            //     {title: 'Table styles'},
            //     {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
            // ],
            setup: function (editor) {
                editor.on('change', function () {
                    editor.save();
                });
            }
        });*/
        $('textarea.cms_textarea').trumbowyg({
            svgPath: '../resources/css/admin/icons.svg'
        });
        _this.bindUiActions();
    },
    bindUiActions() {
        var _this = this;
        _this.data.datatable = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": adminAjaxURL + 'datatable-countries',
            },
            columns: [
                {data: 'id', name: 'id', visible: false},
                {data: 'name', name: 'name'},
                {data: 'country_code', name: 'country_code'},
                {data: 'phone_length', name: 'phone_length'},
                {data: 'valuta_name', name: 'valuta_name'},
                {data: 'tax', name: 'tax'},
                {data: 'tax_percentage', name: 'tax_percentage'},
                {data: 'timezone', name: 'timezone'},
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
            $('[name="logo"]').rules('add', {
                required: true
            });
            $('.hideAgreementEditor').click();
            $(_this.el.modal).modal('show');
        });

        $(document).on('click', _this.el.editButton, function (e) {
            e.preventDefault();
            _this.formReset();
            $('.hideAgreementEditor').click();
            _this.setEdit($(this).data('id'), $(this).data('type'));
        });

        $(document).on('click', _this.el.deleteButton, function (e) {
            e.preventDefault();
            $(_this.el.deleteCountryModal).find(_this.el.confirmDeleteCountryButton).data('id', $(this).data('id'));
            $(_this.el.deleteCountryModal).modal('show');
        });

        $(document).on('click', _this.el.deleteCountryModal + ' ' + _this.el.confirmDeleteCountryButton, function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'delete',
                url: adminAjaxURL + 'countries/' + $(this).data('id'),
                success: function (data) {
                    _this.data.datatable.draw();
                    $(_this.el.deleteCountryModal).modal('hide');
                }
            })
        });

        $(document).on('click', '.showAgreementEditor', function (e) {
            e.preventDefault();
            $(this).text('Hide');
            $(this).removeClass('showAgreementEditor');
            $(this).addClass('hideAgreementEditor');
            $('.agreementDiv').show();
        });
        $(document).on('click', '.hideAgreementEditor', function (e) {
            e.preventDefault();
            $(this).text('Show');
            $(this).removeClass('hideAgreementEditor');
            $(this).addClass('showAgreementEditor');
            $('.agreementDiv').hide();
        });

    },
    formReset() {
        var _this = this;
        $('textarea.cms_textarea').trumbowyg('empty');
        $(_this.el.form).find('[name="id"]').val('');
        $(_this.el.form).find('input').prop('disabled', false);
        $(_this.el.form).find('select').prop('disabled', false);
        $(_this.el.form).find('button[type="submit"]').show();
        $(_this.el.form).find('.logo-holder').hide();
        $(_this.el.form)[0].reset();
    },
    setEdit(id, type) {
        var _this = this
        $.ajax({
            dataType: 'json',
            method: 'get',
            url: adminAjaxURL + 'countries/' + id + '/edit',
            success(data) {
                setForm(_this.el.form, data['inputs']);
                $('[name="logo"]').rules('remove', 'required');
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
                name: {required: true,},
                country_code: {required: true,},
                phone_length: {required: true,},
                tax: {required: true,},
                map_link: {required: true,},
                tax_percentage: {required: true,},
                timezone: {required: true,},
                web: {required: true, url: true},
                telephone: {required: true, digits: true},
                email: {required: true, email: true},
                company_name: {required: true,},
            },

            errorPlacement(error, element) {
                $('span[for="' + $(element).attr('name') + '"]').html(error);
                $(element).removeClass('error');
            },

            submitHandler(form) {
                var custom_data = new FormData($(_this.el.form)[0]);
                $.ajax({
                    dataType: 'json',
                    method: method,
                    url: url,
                    data: custom_data,
                    processData: false,
                    contentType: false,
                    cache: false,
                    crossDomain: true,
                    success: function (data) {
                        _this.data.datatable.draw();
                        $(_this.el.modal).modal('hide');
                    }
                })
            }
        });
    }
};