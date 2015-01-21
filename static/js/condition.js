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
    //test "type" to do deal with fields correctly
    switch (json.inputtype) {
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