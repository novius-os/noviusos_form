<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */
?>
<div id="<?= $id = uniqid('form_') ?>">
<?php
$layout = explode("\n", $item->form_layout);
array_walk($layout, function(&$v) {
    $v = explode(',', $v);
});

// Cleanup empty values
foreach ($layout as $a => $rows) {
    $layout[$a] = array_filter($rows);
    if (empty($layout[$a])) {
        unset($layout[$a]);
        continue;
    }
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

if (in_array($args['label_position'], array('top', 'placeholder'))) {
    $template = '<div class="{label_class} columns">{label} {field}</div>';
    $label_class = 'label';
} else {
    $template = '<div class="{label_class} columns">{label}</div><div class="{field_class} columns">{field}</div>';
    $label_class = 'label '.$args['label_position'];
}



// Label width will be set according to the smallest columns
$label_width = 4;
foreach ($layout as $rows) {
    list(, $width) = explode('=', current($rows));
    $label_width = min($label_width, $width);
}

echo '<form method="POST" enctype="multipart/form-data" action="">';

$fields = array();

// Loop through rows...
foreach ($layout as $rows) {
    $first_col = true;
    $col_width = 0;
    // ...and cols
    foreach ($rows as $row) {
        list($field_id, $width) = explode('=', $row);

        $available_width = $width * 3;
        $col_width += $available_width;

        $field = $item->fields[$field_id];
        $name = !empty($field->field_virtual_name) ? $field->field_virtual_name : 'field_'.$field->field_id;

        $html_attrs = array(
            'id' => $field->field_technical_id ?: $name,
            'class' => $field->field_technical_css,
        );

        if ($args['label_position'] == 'placeholder') {
            $html_attrs['placeholder'] = $field->field_label;
        }

        $label_attrs = array(
            'class' => $label_class,
            'for' => $html_attrs['id'],
        );

        $html = '';

        if ($args['label_position'] == 'placeholder') {
            $label = '';
        } else {
            $label = \Fuel\Core\Form::label($field->field_label, $field->field_technical_id, $label_attrs);
        }

        if (in_array($field->field_type, array('text', 'textarea', 'select', 'email', 'number', 'date'))) {
            $html_attrs['class'] .= ' input_text';


            if (in_array($field->field_type, array('text', 'email', 'number', 'date'))) {
                $html_attrs['type'] = $field->field_type;
                if (!empty($field->field_width)) {
                    $html_attrs['size'] = $field->field_width;
                }
                if (!empty($field->field_limited_to)) {
                    $html_attrs['maxlength'] = $field->field_limited_to;
                }
                $html = \Fuel\Core\Form::input($name, $field->field_default_value, $html_attrs);
            } else if ($field->field_type == 'textarea') {
                if (!empty($field->field_height)) {
                    $html_attrs['rows'] = $field->field_height;
                }
                $html = \Fuel\Core\Form::textarea($name, $field->field_default_value, $html_attrs);
            } else if ($field->field_type == 'select') {
                $label = html_tag('span', $label_attrs, $field->field_label);
                $choices = explode("\n", $field->field_choices);
                $choices = array_combine($choices, $choices);
                $html = \Fuel\Core\Form::select($name, $field->field_default_value, $choices, $html_attrs);
            }

        } else if (in_array($field->field_type, array('checkbox', 'radio'))) {

            $label = html_tag('span', $label_attrs, $field->field_label);

            if (in_array($field->field_type, array('checkbox', 'radio'))) {
                $html = array();
                $default = explode("\n", $field->field_default_value);
                $choices = explode("\n", $field->field_choices);
                foreach ($choices as $i => $choice) {
                    $html_attrs_choice = $html_attrs;
                    $html_attrs_choice['id'] .= $i;
                    if ($field->field_type == 'checkbox') {
                        $item_html = \Fuel\Core\Form::checkbox($name.'[]', $choice, in_array($choice, $default), $html_attrs_choice);
                    } else if ($field->field_type == 'radio') {
                        $item_html = \Fuel\Core\Form::radio($name, $choice, in_array($choice, $default), $html_attrs_choice);
                    }
                    $item_label = \Fuel\Core\Form::label($choice, $field->field_technical_id, array(
                        'for' => $html_attrs_choice['id'],
                    ));
                    $html[] = $item_html . $item_label;
                }
                $html = implode('<br />', $html);
            }
        } else if ($field->field_type == 'message') {
            $label = '';
            $type = in_array($field->field_style, array('p', 'h1', 'h2', 'h3')) ? $field->field_style : 'p';
            $html = html_tag($type, array('class' => 'label_text'), nl2br($field->field_message));
        } else if ($field->field_type == 'separator') {
            $label = '';
            $html = html_tag('hr');
        } else if (in_array($field->field_type, array('hidden', 'variable'))) {
            switch($field->field_origin) {
                case 'get':
                    $value = \Input::get($field->field_origin_var, '');
                    break;

                case 'post':
                    $value = \Input::post($field->field_origin_var, '');
                    break;

                case 'request':
                    $value = \Input::param($field->field_origin_var, '');
                    break;

                case 'global':
                    $value = \Arr::get($GLOBALS, $field->field_origin_var, '');
                    break;

                case 'session':
                    $value = \Session::get($field->field_origin_var, '');
                    break;

                default:
            }
            if ($field->field_type == 'hidden') {
                $label = '';
                $html = \Form::hidden($name, e($value));
            } else if ($field->field_type == 'variable') {
                $html = html_tag('p', array(), e($value));
            }
        } else {
            $label = '';
        }

        $fields[$name] = array(
            'label' => $label,
            'field' => $html,
            'label_class' => in_array($args['label_position'], array('top', 'placeholder')) ? $widths[$available_width] : $widths[$label_width],
            'field_class' => in_array($args['label_position'], array('top', 'placeholder')) ? $widths[$available_width] : $widths[$available_width - $label_width],
            'new_col' => $first_col,
            'width' => $available_width,
        );
        $first_col = false;
    }
}



$first_row = true;
$col_width = 12;

// Loop through rows...
foreach ($fields as $name => $field) {

    if ($field['new_col']) {
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

    $col_width += $field['width'];

    echo strtr($template, array(
        '{label}' => $field['label'],
        '{field}' => $field['field'],
        '{label_class}' => $field['label_class'],
        '{field_class}' => $field['field_class'],
    ));
}

if (!$first_row) {
    if ($col_width < 12) {
        echo '<div class="columns '.$widths[12 - $col_width].'"></div>';
    }
    echo '</div>';
}


\Debug::dump($fields);


echo \Form::submit('submit', 'Send the form');
echo '</form>';