function setForm(form, data) {
    for (var index in data) {
        var element = data[index];
        switch (element['type']) {
            case('text'):
                $(form).find('[name="' + index + '"][type="text"]').val(element['value']);
                break;
            case('number'):
                $(form).find('[name="' + index + '"][type="number"]').val(element['value']);
                break;
            case('hidden'):
                $(form).find('[name="' + index + '"][type="hidden"]').val(element['value']);
                break;
            case('email'):
                $(form).find('[name="' + index + '"][type="email"]').val(element['value']);
                break;
            case('textarea'):
                $(form).find('textarea[name="' + index + '"]').val(element['value']);
                break;
            case('select'):
                $(form).find('[name="' + index + '"]').val(element['value']);
                break;
            case('select2'):
                if (element['value'] != '' && element['value'] != []) {
                    $(form).find('[name="' + index + '"]').val(element['value']).select2();
                }
                break;
            case('radio'):
                $(form).find('[name="' + index + '"][value="' + element['value'] + '"]').prop('checked', true);
                break;
            case('checkbox'):
                $(form).find('[name="' + index + '"]').prop('checked', false);
                $(form).find('[name="' + index + '"][value="' + element['value'] + '"]').prop('checked', true);
                break;
            case('image'):
                if (element['value'] != '' && element['value'] != null) {
                    $(form).find('.' + index + '_image').show();
                    $(form).find('.' + index + '_image').find('img').attr('src', siteURL + 'uploads/' + element['value']);
                }
                break;
            case('file'):
                if (element['value'] != '' && element['value'] != null) {
                    $(form).find('.' + index + '_image').show();
                    $(form).find('.' + index + '_image').find('a').attr('href', siteURL + 'uploads/' + element['value']);
                }
                break;
            case 'radio':
                var Values = element.value;
                var Checkbox = $(form).find('input:radio[name="' + index + '"][value="' + Values + '"]');
                Checkbox.prop('checked', true);
                break;
            case 'tinymce':
                // $(tinymce.get(index).getBody()).html(element.value);
                $(form).find('textarea[name="' + index + '"]').trumbowyg('html', element['value']);
                break;
        }
    }
}

function alertShowing(div, message, type) {

}

function initTooltip() {
    $('[data-toggle="tooltip"]').tooltip();
}

function round(value, decimals) {
    return Number(Math.round(value + 'e' + decimals) + 'e-' + decimals);
}

function round2(value) {
    return round(value, 2);
}

function datePickerInit() {
    var startDate = new Date();
    startDate.setDate(startDate.getDate());
    var dateFormat = 'dd/mm/yyyy';

    $('.date-picker').datepicker({
        orientation: "bottom auto",
        clearBtn: true,
        startDate: startDate,
        autoclose: true,
        format: dateFormat
    });
    $('.old-date-picker').datepicker({
        orientation: "bottom auto",
        clearBtn: true,
        endDate: startDate,
        autoclose: true,
        format: dateFormat
    });
    $('.all-date-picker').datepicker({
        orientation: "bottom auto",
        clearBtn: true,
        autoclose: true,
        format: dateFormat
    });

    $('.js--month-picker').datepicker({
        orientation: "bottom auto",
        clearBtn: true,
        autoclose: true,
        format: 'mm/yyyy',
        viewMode: "months",
        minViewMode: "months",
        endDate: startDate,
    });
}

function displayErrorMessages(Response, ErrorBlock, type) {
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
        Form.find('span.help-block').html('');
        Form.find('.form-group').removeClass('has-error');
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
            var ErrorInput = $(Form).find('input[name="' + index + '"]');
            if ($(ErrorInput).length >= 1) {
                $(ErrorInput).parents('.form-group').addClass('has-error');
                $(ErrorInput).parents('.input-group').addClass('has-error');
            }

            var ErrorSelect = $(Form).find('select[name*="' + index + '"]');
            if ($(ErrorSelect).length >= 1) {
                $(ErrorSelect).parents('.form-group').addClass('has-error');
            }

            var ErrorSelect = $(Form).find('textarea[name="' + index + '"]');
            if ($(ErrorSelect).length >= 1) {
                $(ErrorSelect).parents('.form-group').addClass('has-error');
            }
            $(Form).find('span[for="' + index + '"]').html(element);
        });
    }
}

function filesizeValidation(element) {
    size = $(element)[0].files[0].size / 1024 / 1024;
    if (size > max_file_size) {
        $(element).val('');
        $(element).siblings('.has-error').find('.help-block').text('File size shouldn\'t exceed ' + max_file_size + ' MB');
    } else {
        $(element).siblings('.has-error').find('.help-block').text('');
    }
}

// Disable Mouse scrolling
$(document).on('wheel', 'input[type=number]', function (e) {
    e.preventDefault();
    $(this).blur();
});
// Disable keyboard scrolling
$('input[type=number]').on('keydown', function (e) {
    var key = e.charCode || e.keyCode;
    // Disable Up and Down Arrows on Keyboard
    if (key == 38 || key == 40) {
        e.preventDefault();
    } else {
        return;
    }
});

(function ($) {

    $.fn.inputFileText = function (userOptions) {
        // Shortcut for plugin reference
        var P = $.fn.inputFileText;

        var options = P.getOptions(userOptions);

        if (P.shouldRemoveInputFileText(this, options.remove)) {
            return P.removeInputFileText(this);
        }
        else if (P.hasInputFileText(this)) {
            return this;
        }

        // Keep track of input file element's display setting
        this.attr(P.DISPLAY_ATTRIBUTE, this.css('display'));

        // Hide input file element
        this.css({
            display: 'none'
            //width:  0
        });

        // Insert button after input file element
        var button = $(
            '<input type="button" value="' + options.text + '" class="' + options.buttonClass + '" />'
        ).insertAfter(this);

        // Insert text after button element
        var text = $(
            '<span style="margin-left: 5px" class="' + options.textClass + '"></span>'
        ).insertAfter(button);

        // Open input file dialog when button clicked
        var self = this;
        button.click(function () {
            self.click();
        });

        // Update text when input file chosen
        this.change(function () {
            // Chrome puts C:\fakepath\... for file path
            text.text(self.val().replace('C:\\fakepath\\', ''));
        });

        // Mark that this plugin has been applied to the input file element
        return this.attr(P.MARKER_ATTRIBUTE, 'true');
    };

    $.fn.inputFileText.MARKER_ATTRIBUTE = 'data-inputFileText';
    $.fn.inputFileText.DISPLAY_ATTRIBUTE = 'data-inputFileText-display';

    $.fn.inputFileText.getOptions = function (userOptions) {
        return $.extend({
            // Defaults
            text: 'Choose File',
            remove: false,
            buttonClass: '',
            textClass: ''
        }, userOptions);
    };

    /**
     Check if plugin has already been applied to input file element.
     */
    $.fn.inputFileText.hasInputFileText = function (inputFileElement) {
        return inputFileElement.attr($.fn.inputFileText.MARKER_ATTRIBUTE) === 'true';
    };

    /**
     Check if plugin should be removed from input file element.
     */
    $.fn.inputFileText.shouldRemoveInputFileText = function (inputFileElement, remove) {
        return remove && $.fn.inputFileText.hasInputFileText(inputFileElement);
    };

    /**
     Remove plugin from input file element.
     */
    $.fn.inputFileText.removeInputFileText = function (inputFileElement) {
        var P = $.fn.inputFileText;

        inputFileElement.next('input[type=button]').remove();
        inputFileElement.next('span').remove();
        return inputFileElement.attr(P.MARKER_ATTRIBUTE, null)
            .css({
                display: inputFileElement.attr(P.DISPLAY_ATTRIBUTE)
            })
            .attr(P.DISPLAY_ATTRIBUTE, null);
    };

}(jQuery));

$.fn.dataTable.ext.errMode = function (xhr, error, thrown) {
    var response = xhr.jqXHR;
    if (response.status == 401 && response.responseJSON.login_again == true) {
        location.reload();
    } else {
        alert(thrown);
    }
};


$(document).ajaxError(function (event, xhr, settings) {
    var response = xhr.responseJSON;
    if (response != undefined && response['login_again'] != undefined && response['login_again']) {
        location.reload();
    }
});