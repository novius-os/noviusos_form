<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

//Nos\I18n::current_dictionary('noviusos_form::common');

    $format_default = <<<'EOF'
    var %1$s = %2$s;
$(document).ready(check_option_%1$s);
$('input[name="'+%1$s.inputname+'"]').change(check_option_%1$s);
function check_option_%1$s() {
    if ($('input[name="'+%1$s.inputname+'"]').val() == %1$s.value) {
        $('[name="'+%1$s.condition+'"]').parent().removeClass('nos_form_disabled').addClass('nos_form_enabled');
    } else {
        $('[name="'+%1$s.condition+'"]').parent().addClass('nos_form_disabled').removeClass('nos_form_enabled');
    }
};

EOF;
    $format_radio = <<<'EOF'
    var %1$s = %2$s;
$(document).ready(check_option_%1$s);
$('input[name="'+%1$s.inputname+'"]').change(check_option_%1$s);
function check_option_%1$s() {
    if ($('input[name="'+%1$s.inputname+'"][value="'+%1$s.value+'"]').is(":checked")) {
        $('[name="'+%1$s.condition+'"]').parent().removeClass('nos_form_disabled').addClass('nos_form_enabled');
    } else {
        $('[name="'+%1$s.condition+'"]').parent().addClass('nos_form_disabled').removeClass('nos_form_enabled');
    }
};

EOF;
    $format_select = <<<'EOF'
    var %1$s = %2$s;
$(document).ready(check_option_%1$s);
$('select[name="'+%1$s.inputname+'"]').change(check_option_%1$s);
function check_option_%1$s() {
    if ($('select[name="'+%1$s.inputname+'"]').val() == %1$s.value) {
        $('[name="'+%1$s.condition+'"]').parent().removeClass('nos_form_disabled').addClass('nos_form_enabled');
    } else {
        $('[name="'+%1$s.condition+'"]').parent().addClass('nos_form_disabled').removeClass('nos_form_enabled');
    }
};

EOF;

    // input[name^="'+%1$s.inputname+'"] is actually name = %1$s.inputnameXX where XX is number.
    // There may be an accurate filtering.
    $format_checkbox = <<<'EOF'
    var %1$s = %2$s;
$(document).ready(check_option_%1$s);
$('input[name="'+%1$s.inputname+'[]"]').change(check_option_%1$s);
function check_option_%1$s() {
    if ($('input[name^="'+%1$s.inputname+'"][value="'+%1$s.value+'"]').is(":checked")) {
        $('[name="'+%1$s.condition+'"]').parent().removeClass('nos_form_disabled').addClass('nos_form_enabled');
    } else {
        $('[name="'+%1$s.condition+'"]').parent().addClass('nos_form_disabled').removeClass('nos_form_enabled');
    }
};

EOF;


return array(
    'jsformat' => array(
        'default' => $format_default,
        'radio' => $format_radio,
        'select' => $format_select,
        'checkbox' => $format_checkbox,
    ),
);