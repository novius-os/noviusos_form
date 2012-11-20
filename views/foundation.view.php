<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */


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

if (in_array($enhancer_args['label_position'], array('top', 'placeholder'))) {
    $template = '<div class="{label_class} columns">{label} {field}</div>';
    $label_class = 'label';
} else {
    $template = '<div class="{label_class} columns">{label}</div><div class="{field_class} columns">{field}</div>';
    $label_class = 'label '.$enhancer_args['label_position'];
}

// Label width will be set according to the smallest columns
$label_width = 4;
foreach ($fields as $field) {
    $label_width = min($label_width, $field['width']);
}

$first_row = true;
$col_width = 12;

function add_class_to_thing(&$thing, $class) {
    if (isset($thing['callback'])) {
        $key = false;
        if ($thing['callback'] == 'html_tag') {
            $key = 1;
        }
        if (is_array($thing['callback']) and $thing['callback'][0] == 'Form') {
            if (in_array($thing['callback'][1], array('select', 'checkbox'))) {
                $key = 3;
            } else {
                $key = 2;
            }
        }
        if (false !== $key) {
            if (!isset($thing['args'][$key]['class'])) {
                $thing['args'][$key]['class'] = $class;
            } else {
                $thing['args'][$key]['class'] .= ' '.$class;
            }
        }
    }
}

$render_thing = null;
$render_thing = function($thing) use(&$render_thing, &$render_template) {
    if (is_string($thing)) {
        return $thing;
    }
    if (is_array($thing)) {
        if (isset($thing['callback']) && is_callable($thing['callback'])) {
            $args = isset($thing['args']) ? $thing['args'] : array();
            return call_user_func_array($thing['callback'], $args);
        } else {
            $out = array();
            foreach ($thing as $t) {
                if (is_array($t) && isset($t['template'])) {
                    $template = $t['template'];
                    unset($t['template']);
                    $vars = $t;
                    $out[] = call_user_func($render_template, $template, $vars);
                } else {
                    $out[] = call_user_func($render_thing, $t);
                }
            }
            return implode($out);
        }
    }
};

$render_template = null;
$render_template = function($template, $args) use (&$render_template, &$render_thing) {
    $replacements = array();
    foreach ($args as $name => $value) {
        $replacements['{' . $name . '}'] = $render_thing($value);
    }
    return strtr($template, $replacements);
};

?>
<div id="<?= $id = uniqid('form_') ?>">
<?php

foreach ($errors as $error) {
    echo '<p class="error">'.nl2br(htmlspecialchars($error)).'</p>';
}
echo html_tag('form', $form_attrs);

// Loop through fields now
foreach ($fields as $name => $field) {

    if ($field['new_row']) {
        if (!$first_row) {
            if ($col_width < 12) {
                echo '<div class="columns '.$widths[12 - $col_width].'"></div>';
            }
            echo '</div>';
        }
        if ($first_row) {
            $first_row = false;
        }
        echo '<div class="row">';
        $col_width = 0;
    }

    $available_width = $field['width'] * 3; // 3 = 12 columns grid / 4 column form
    $col_width += $available_width;

    add_class_to_thing($field['label'], $label_class);
    add_class_to_thing($field['field'], 'input_text');

    echo $render_template($template, array(
        'label' => $field['label'],
        'field' => $field['field'],
        'label_class' => in_array($enhancer_args['label_position'], array('top', 'placeholder')) ? $widths[$available_width] : $widths[$label_width],
        'field_class' => in_array($enhancer_args['label_position'], array('top', 'placeholder')) ? $widths[$available_width] : $widths[$available_width - $label_width],
    ));
}

if (!$first_row) {
    if ($col_width < 12) {
        echo '<div class="columns '.$widths[12 - $col_width].'"></div>';
    }
    echo '</div>';
}


echo \Form::submit('submit', $item->form_submit_label);
echo '</form></div>';