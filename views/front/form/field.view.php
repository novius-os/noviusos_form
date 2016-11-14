<?php
/**
 * @var \Nos\Form\Model_Form $form
 * @var array $field
 * @var array $errors
 * @var array $labelWidthPerPage
 * @var int $page
 * @var array $enhancer_args
 */

// Gets the field error
$fieldError = \Arr::get($errors, $field['name']);
if (!empty($fieldError)) {
    $fieldError = '
        <div class="parsley-custom-error-message">
            '.nl2br(htmlspecialchars($fieldError)).'
        </div>
    ';
}

// Builds the field template
$fieldTemplate = \Arr::get($field, 'template');
if (isset($field['template'])) {
    $fieldTemplate = $field['template'];
} else {
    if (in_array($enhancer_args['label_position'], array('left', 'right'))) {
        $fieldTemplate = '
            <div class="twelve columns">
                <span class="inline-label">{label}</span>
                <span class="inline-field" >{field} {instructions}</span>
                <span class="inline-field-error" >{field_error}</span>
            </div>
        ';
    } else {
        $fieldTemplate = '';
        if (!in_array($enhancer_args['label_position'], array('top', 'placeholder')) || !empty($field['label'])) {
            // Displays the label only if not empty
            $fieldTemplate .= '<div class="{label_lass} columns form-label">{label}</div>';
        }
        $fieldTemplate .= '<div class="{field_class} columns form-field">{field} {instructions}</div>';
        if (!empty($fieldError)) {
            $fieldTemplate .= '<div class="twelve columns form-field-error">{field_error}</div>';
        }
    }
}

// Label and field class names
$labelClass = $fieldClass = 'twelve';
if (!in_array($enhancer_args['label_position'], array('top', 'placeholder'))) {
    $labelClass = \Nos\Form\Helper_Front_Form::getWidthClassName($labelWidthPerPage[$page] * 3);
    if ($labelWidthPerPage[$page] < 4) {
        $fieldClass = $labelClass = \Nos\Form\Helper_Front_Form::getWidthClassName(12 - ($labelWidthPerPage[$page] * 3));
    }
}

// Custom view
if (!empty($field['view'])) {
    \Nos\FrontCache::viewForgeUncached($field['view'], array(
        'form' => $form,
        'errors' => $errors,
        'field' => $field,
        'template' => $fieldTemplate,
        'labelClass' => $labelClass,
        'fieldClass' => $fieldClass,
        'fieldError' => $fieldError,
        'enhancer_args' => $enhancer_args,
    ), false);
}

// Default view
else {
    echo \Nos\Form\Helper_Front_Form::renderTemplate($fieldTemplate, array(
        'label' => $field['label'],
        'field' => $field['field'],
        'instructions' => $field['instructions'],
        'label_class' => $labelClass,
        'field_class' => $fieldClass,
        'field_error' => $fieldError,
    ));
}

