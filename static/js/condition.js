function init_form_condition(type, json) {
    $(document).ready(function() {
        check_option(type, json);
    });
    $('[name^="'+json.inputname+'"]').change(function() {
        check_option(type, json);
    });

};

//handler
function check_option(type, json) {
    //test "type" to do deal with fields correctly
    switch (type) {
        case "select":
            if ($('select[name="'+json.inputname+'"]').val() == json.value) {
                $('[name="'+json.condition+'"]').parent().removeClass('nos_form_disabled').addClass('nos_form_enabled');
            } else {
                $('[name="'+json.condition+'"]').parent().addClass('nos_form_disabled').removeClass('nos_form_enabled');
            }
            break;
        case "radio":
            if ($('input[name="'+json.inputname+'"][value="'+json.value+'"]').is(":checked")) {
                $('[name="'+json.condition+'"]').parent().removeClass('nos_form_disabled').addClass('nos_form_enabled');
            } else {
                $('[name="'+json.condition+'"]').parent().addClass('nos_form_disabled').removeClass('nos_form_enabled');
            }

            break;
        case "checkbox":
            if ($('input[name^="'+json.inputname+'"][value="'+json.value+'"]').is(":checked")) {
                $('[name="'+json.condition+'"]').parent().removeClass('nos_form_disabled').addClass('nos_form_enabled');
            } else {
                $('[name="'+json.condition+'"]').parent().addClass('nos_form_disabled').removeClass('nos_form_enabled');
            }
            break;
        default:
            if ($('input[name="'+json.inputname+'"]').val() == json.value) {
                $('[name="'+json.condition+'"]').parent().removeClass('nos_form_disabled').addClass('nos_form_enabled');
            } else {
                $('[name="'+json.condition+'"]').parent().addClass('nos_form_disabled').removeClass('nos_form_enabled');
            }
    }
};