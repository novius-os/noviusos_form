<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */


\Nos\Nos::main_controller()->addCss('static/apps/noviusos_form/css/front.css');

\Nos\Nos::main_controller()->addJavascript('static/apps/noviusos_form/js/foundation.js');

function add_attr_to_thing(&$thing, $attr, $value)
{
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
            if (!isset($thing['args'][$key][$attr])) {
                $thing['args'][$key][$attr] = $value;
            } else {
                $thing['args'][$key][$attr] .= ' '.$value;
            }
        }
    }
}

function get_html_attrs($thing)
{
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
            return $thing['args'][$key];
        }
    }
    return;
}

function add_content_to_thing(&$thing, $content)
{
    if (isset($thing['callback'])) {
        $key = false;
        if ($thing['callback'] == 'html_tag') {
            $key = 2;
        }
        if (is_array($thing['callback']) and $thing['callback'][0] == 'Form') {
            $key = 0;
        }
        $thing['args'][$key] .= $content;
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

if (in_array($enhancer_args['label_position'], array('top', 'placeholder'))) {
    $template = '<div class="{label_class} columns">{label} {field}</div>';
    $label_class = 'label';
} else {
    $template = '<div class="{label_class} columns">{label}</div><div class="{field_class} columns">{field}</div>';
    $label_class = 'label '.$enhancer_args['label_position'];
}

foreach ($fields as $name => &$field) {

    add_attr_to_thing($field['label'], 'class', $label_class);
    add_attr_to_thing($field['field'], 'class', 'input_text');

    if (!empty($field['item']->field_mandatory)) {
        // For fields using a label, add a <span> at the end
        add_content_to_thing($field['label'], ' <span clas="required">*</span>');
        if ($enhancer_args['label_position'] == 'placeholder') {
            // For placeholder, add * at the end of placeholder's text
            add_attr_to_thing($field['field'], 'placeholder', ' *');
        }
    }
}
unset($field);

?>
<div id="<?= $id = uniqid('form_') ?>">
<?php

foreach ($errors as $name => $error) {
    $attrs = get_html_attrs($fields[$name]['field']);
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

if ($has_page_break) {
    echo '</div>';
}

$page_break_layout = '{previous}{pagination}{next}';

if ($has_page_break) {
    ?>
    <div class="page_break_control row">
    <?php
    echo strtr($page_break_layout, array(
        '{previous}' => '
            <div class="columns four">
                <a class="page_break_previous" href="">'.__('Previous page').'</a>
            </div>',
        '{next}' => '
            <div class="columns four">
                <button type="button" class="page_break_next">'.__('Next page').'</button>'.
                \Form::submit('submit', $item->form_submit_label, array(
                    'class' => 'page_break_last',
                )).'
            </div>',
        '{pagination}' => '
                <div class="columns four">'.
                    strtr(__('{current} out of {total}'), array(
                        '{current}' => '<span class="page_break_current">1</span>',
                        '{total}' => '<span class="page_break_total">1</span>',
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