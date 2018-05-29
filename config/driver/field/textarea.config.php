<?php

Nos\I18n::current_dictionary('noviusos_form::common');

return array(
    'name' => __('Paragraph text'),
    'icon' => 'static/apps/noviusos_form/img/fields/textarea.png',

    // Fields default value
    'default_values' => array(
        'field_label' => __('Enter a number:'),
    ),

    'admin' => array(
        // Meta layout
        'layout' => array(
            'main' => array(
                'fields' => array(
                    'field_label',
                    'field_placeholder',
                ),
            ),
            'optional' => array(
                'fields' => array(
                    'field_mandatory',
                    'field_default_value',
                    'field_origin',
                    'field_origin_var',
                    'field_details',
                    'field_height',
                ),
            ),
        ),
        'fields' => array(
            'field_placeholder' => array(
                'label' => __('Placeholder :'),
                'form'  => array(
                    'type' => 'text',
                ),
            ),
        ),
    ),
);
