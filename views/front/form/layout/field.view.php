<?php

// Field template
$field_template = \Arr::get($field, 'template');
if (isset($field['template'])) {
    $field_template = $field['template'];
} else {
    if (in_array($enhancer_args['label_position'], array('left', 'right'))) {
        $field_template = '
            <div class="twelve columns">
                <span class="inline-label">{label}</span>
                <span class="inline-field" >{field} {instructions}</span>
            </div>
        ';
    } else {
        $field_template = '<div class="{field_class} columns">{field} {instructions}</div>';
        if (!in_array($enhancer_args['label_position'], array('top', 'placeholder')) || !empty($field['label'])) {
            $field_template = '<div class="{label_class} columns">{label}</div>'.$field_template;
        }
    }
}

// Label and field class names
$label_class = $field_class = 'twelve';
if (!in_array($enhancer_args['label_position'], array('top', 'placeholder'))) {
    $label_class = $widths[$label_width[$page] * 3];
    if ($label_width[$page] < 4) {
        $field_class = $label_class = $widths[12 - ($label_width[$page] * 3)];
    }
}

// Captcha
if ($field['name'] === 'form_captcha') {
    echo \Nos\FrontCache::viewForgeUncached('noviusos_form::front/form/captcha', array(
        'form_id' => $item->form_id,
        'template' => $field_template,
        'config' => array(
            'error' => nl2br(htmlspecialchars(\Arr::get($errors, $field['name']))),
            'label' => $field['label'],
            'field' => $field['field'],
            'instructions' => $field['instructions'],
            'label_class' => $label_class,
            'field_class' => $field_class,
        )
    ), false);
}
// Others
else {
    echo \Nos\Form\Helper_Front_Form::renderTemplate($field_template, array(
        'error' => nl2br(htmlspecialchars(\Arr::get($errors, $field['name']))),
        'label' => $field['label'],
        'field' => $field['field'],
        'instructions' => $field['instructions'],
        'label_class' => $label_class,
        'field_class' => $field_class,
    ));
}

