<?php

return array(
    'name' => __('Hidden'),
    'icon' => 'static/apps/noviusos_form/img/fields/hidden.png',

    // Fields default value
    'default_values' => array(
        'field_label' => __('I’m the label for internal use only as I won’t be shown to users:'),
    ),

    'admin' => array(
        // Meta layout
        'layout' => array(
            'main' => array(
                'fields' => array(
                    'field_label',
                    'field_default_value',
                    'field_origin',
                    'field_origin_var',
                ),
            ),
            'optional' => array(
                'fields' => array(),
            ),
            'technical' => array(
                'fields' => array(
                    'field_technical_id',
                    'field_technical_css',
                ),
            ),
        ),
        'field_label' => array(
            'label' => __('Label (displayed only in backoffice) :'),
        ),
    ),

    // Set true to display the field in the "Special fields" column when adding a new field to a form
    'special' => true,

    // Set true to display the field only for experts
    'expert' => true,
);
