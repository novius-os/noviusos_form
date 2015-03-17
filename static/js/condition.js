function init_form_condition(json) {
    $(document).ready(function() {
        check_option(json);
    });
    $('[name^="'+json.inputname+'"]').change(function() {
        check_option(json);
    });

};

//handler
function check_option(json) {
    var $conditionnal_input = $('[name="'+json.condition+'"]');
    if ($conditionnal_input.data('required') == undefined) {
        if ($conditionnal_input.attr('required') == undefined) {
            $conditionnal_input.data('required', '');
        } else {
            $conditionnal_input.data('required', $conditionnal_input.attr('required'));
        }
    }

    var todo = 'nothing';

    //test "type" to do deal with fields correctly
    switch (json.inputtype) {
        case "select":
            if ($('select[name="'+json.inputname+'"]').val() == json.value) {
                todo = 'show';
            } else {
                todo = 'hide';
            }
            break;
        case "radio":
            if ($('input[name="'+json.inputname+'"][value="'+json.value+'"]').is(":checked")) {
                todo = 'show';
            } else {
                todo = 'hide';
            }

            break;
        case "checkbox":
            if ($('input[name^="'+json.inputname+'"][value="'+json.value+'"]').is(":checked")) {
                todo = 'show';
            } else {
                todo = 'hide';
            }
            break;
        default:
            if ($('input[name="'+json.inputname+'"]').val() == json.value) {
                todo = 'show';
            } else {
                todo = 'hide';
            }
    }

    var $parent = $conditionnal_input.parent();
    if (!$parent.hasClass('row') && (!$conditionnal_input.prev().is('label') && $parent.prev('.columns').find('label').length != 0)) {
        //In this case, labels are in another column (when label are left or right aligned)
        //Or the conditionnal field don't take full width
        $parent = $parent.add($parent.prev('.columns'));
    }
    switch (todo) {
        case 'show' :
            $parent.removeClass('nos_form_disabled').addClass('nos_form_enabled');
            if ($conditionnal_input.data('required') != '') {
                $conditionnal_input.attr('required', 'required');
            }
            break;
        case 'hide' :
            $parent.addClass('nos_form_disabled').removeClass('nos_form_enabled');
            if ($conditionnal_input.data('required') != '') {
                $conditionnal_input.removeAttr('required');
            }
            break;
    }
};