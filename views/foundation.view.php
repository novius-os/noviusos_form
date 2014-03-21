<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

use \Nos\Form\Helper_Foundation;

\Nos\Nos::main_controller()->addCss('static/apps/noviusos_form/css/front.css');
\Nos\Nos::main_controller()->addJavascript('static/apps/noviusos_form/js/foundation.js');

\Nos\I18n::current_dictionary('noviusos_form::front');

if (in_array($enhancer_args['label_position'], array('top', 'placeholder'))) {
    $template = '<div class="{label_class} columns">{label} {field} {instructions}</div>';
    $label_class = 'label';
} else {
    $template = '<div class="{label_class} columns">{label}</div><div class="{field_class} columns">{field} {instructions}</div>';
    $label_class = 'label '.$enhancer_args['label_position'];
}

foreach ($fields as $name => &$field) {

    Helper_Foundation::addAttrToThing($field['label'], 'class', $label_class);
    Helper_Foundation::addAttrToThing($field['field'], 'class', 'input_text');

    if (!empty($field['item']->field_mandatory)) {
        // For fields using a label, add a <span> at the end
        Helper_Foundation::addContentToThing($field['label'], ' <span class="required">*</span>');
        if ($enhancer_args['label_position'] == 'placeholder') {
            // For placeholder, add * at the end of placeholder's text
            Helper_Foundation::addAttrToThing($field['field'], 'placeholder', ' *');
        }
    }
}
unset($field);

?>
<div class="noviusos_form noviusos_enhancer" id="<?= $id = uniqid('form_') ?>">
<?php

foreach ($errors as $name => $error) {
    $attrs = Helper_Foundation::getHtmlAttrs($fields[$name]['field']);
    $id = !empty($attrs['id']) ? $attrs['id'] : '';
    echo '<p class="error"><label for="'.$id.'">'.nl2br(htmlspecialchars($error)).'</label></p>';
}

$widths = array(
    1 => 'one',
    2 => 'two',
    3 => 'three',
    4 => 'four',
    5 => 'five',
    6 => 'six',
    7 => 'seven',
    8 => 'eight',
    9 => 'nine',
    10 => 'ten',
    11 => 'eleven',
    12 => 'twelve',
);

// Label width will be set according to the smallest columns
$label_width = 4;
foreach ($fields as $field) {
    $label_width = min($label_width, $field['width']);
}

$first_page = true;
$first_row = true;
$col_width = 12;

if (empty($form_attrs['class'])) {
    $form_attrs['class'] = '';
}
$form_attrs['class'] .= ' foundation';

echo '<form '.array_to_attr($form_attrs).'>';
// Loop through fields now
foreach ($fields as $name => $field) {

    if ($field['new_row']) {
        if (!$first_row) {
            if ($col_width < 12) {
                echo '<div class="columns '.$widths[12 - $col_width].'"></div>';
            }
            echo '</div>';
        }
    }

    if ($field['new_page']) {
        if (!$first_page) {
            echo '</div>';
        } else {
            $first_page = false;
        }
        echo '<div class="page_break">';
    }

    if ($field['new_row']) {
        if ($first_row) {
            $first_row = false;
        }
        echo '<div class="row">';
        $col_width = 0;
    }

    $available_width = $field['width'] * 3; // 3 = 12 columns grid / 4 column form
    $col_width += $available_width;

    if ($name === 'form_captcha') {
        echo \Nos\FrontCache::viewForgeUncached('noviusos_form::captcha', array(
            'form_id' => $item->form_id,
            'template' => $template,
            'config' => array(
                'label' => $field['label'],
                'field' => $field['field'],
                'instructions' => $field['instructions'],
                'label_class' => in_array($enhancer_args['label_position'], array('top', 'placeholder')) ? $widths[$available_width] : $widths[$label_width],
                'field_class' => in_array($enhancer_args['label_position'], array('top', 'placeholder')) ? $widths[$available_width] : $widths[$available_width - $label_width],
            )
        ), false);
    } else {
        echo Helper_Foundation::renderTemplate($template, array(
            'label' => $field['label'],
            'field' => $field['field'],
            'instructions' => $field['instructions'],
            'label_class' => in_array($enhancer_args['label_position'], array('top', 'placeholder')) ? $widths[$available_width] : $widths[$label_width],
            'field_class' => in_array($enhancer_args['label_position'], array('top', 'placeholder')) ? $widths[$available_width] : $widths[$available_width - $label_width],
        ));
    }
}

if (!$first_row) {
    if ($col_width < 12) {
        echo '<div class="columns '.$widths[12 - $col_width].'"></div>';
    }
    echo '</div>';
}

if ($page_break_count > 0) {
    echo '</div>';
}

$page_break_layout = __('{{previous}}{{pagination}}{{next}}');

if ($page_break_count > 0) {
    ?>
    <div class="page_break_control row">
    <?php
    echo strtr($page_break_layout, array(
        '{{previous}}' => '
            <div class="columns four">
                <a class="page_break_previous" href="">'.__('Previous page').'</a>
            </div>',
        '{{next}}' => '
            <div class="columns four">
                <button type="button" class="page_break_next">'.__('Next page').'</button>'.
                \Form::submit('submit', $item->form_submit_label, array(
                    'class' => 'page_break_last',
                )).'
            </div>',
        '{{pagination}}' => '
                <div class="columns four"> <progress id="progress" value="1" max="'.($page_break_count + 1).'"></progress> '.
                    strtr(__('{{current}} out of {{total}}'), array(
                        '{{current}}' => '<span class="page_break_current">1</span>',
                        '{{total}}' => '<span class="page_break_total">'.($page_break_count + 1).'</span>',
                    )).'
                </div>',
    ));
    ?>
    </div>
    <?php
} else {
    echo \Form::submit('submit', $item->form_submit_label);
}


echo '</form></div>';
