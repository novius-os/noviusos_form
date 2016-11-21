/**
 * The form condition script
 */
(function() {

    /**
     * Wizard constructor
     */
    function NosFormCondition($form, options) {
        this.$form = $form;
        this.options = $.extend(true, {
            currentPageClassName: 'current',
            selectors: {
                sections: '.form-fields-group',
                controls: '.wizard-controls',
                controlPrevious: '.wizard-control-previous',
                controlNext: '.wizard-control-next',
                controlCurrentPage: '.wizard-control-current-page',
                controlProgress: '.wizard-control-progress',
                controlSubmit: '.wizard-control-submit[type=submit]',
            },
        }, options);
    }

    /**
     * Initializes the wizard
     */
    NosFormWizard.prototype.init = function () {
    }

    window.NosFormCondition = NosFormCondition;
})();

function init_form_condition(settings) {
    $(document).ready(function() {
        check_option(settings);
    });
    $('[name^="'+settings.inputname+'"]').on('keyup input change', function() {
        check_option(settings);
    });

};

//handler
function check_option(settings) {
    var $conditionnal_input = $('[name^="'+settings.condition+'"]');
    if ($conditionnal_input.data('required') == undefined) {
        if ($conditionnal_input.attr('required') == undefined) {
            $conditionnal_input.data('required', '');
        } else {
            $conditionnal_input.data('required', $conditionnal_input.attr('required'));
        }
    }

    var todo = 'nothing';
    var $input = $('[name="'+settings.inputname+'"]');
    if ($input) {
        switch ($input.prop('tagName')) {
            case "radio":
                if ($input.is('[value="'+settings.value+'"]:checked')) {
                    todo = 'show';
                } else {
                    todo = 'hide';
                }
                break;
            case "checkbox":
                if ($input.is('[value="'+settings.value+'"]:checked')) {
                    todo = 'show';
                } else {
                    todo = 'hide';
                }
                break;
            default:
                if ($input.val() == settings.value) {
                    todo = 'show';
                } else {
                    todo = 'hide';
                }
        }
    }

    var $conditionnal_elements = $conditionnal_input.closest('.nos_form_field');
    switch (todo) {
        case 'show' :
            $conditionnal_elements.removeClass('nos_form_disabled').addClass('nos_form_enabled');
            if ($conditionnal_input.data('required') != '') {
                $conditionnal_input.attr('required', 'required');
            }
            break;
        case 'hide' :
            $conditionnal_elements.addClass('nos_form_disabled').removeClass('nos_form_enabled');
            if ($conditionnal_input.data('required') != '') {
                $conditionnal_input.removeAttr('required');
            }
            break;
    }
};
