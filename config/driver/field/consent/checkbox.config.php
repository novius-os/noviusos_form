<?php

Nos\I18n::current_dictionary('noviusos_form::common');

return array(
    'name' => __('Consent (checkbox)'),
    'icon' => 'static/apps/noviusos_form/img/fields/checkbox.png',

    // Fields default value
    'default_values' => array(
        'field_label' => __('This label will only be displayed in backoffice'),
        'field_content' => __('By submitting this form, I accept that...'),
        'field_mandatory' => 1,
    ),

    // The field meta config
    'admin' => array(
        'layout' => array(
            'main' => array(
                'fields' => array(
                    'field_label',
                    'field_content',
                ),
            ),
            'optional' => array(
                'fields' => array(
                    'field_mandatory',
                ),
            ),
        ),
        'fields' => array(
            'field_content' => array(
                'label' => __('Content:'),
                'renderer' => \Nos\Renderer_Wysiwyg::class,
            ),
        ),
    ),
);
