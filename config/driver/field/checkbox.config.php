<?php

return array(
    'name' => __('Multiple choice (checkboxes)'),
    'icon' => 'static/apps/noviusos_form/img/fields/checkbox.png',

    // Fields default value
    'default_values' => array(
        'field_choices' => __("First option\nSecond option"),
    ),

    // The field meta config
    'admin' => array(
        'layout' => array(
            'main' => array(
                'fields' => array(
                    'field_label',
                    'field_choices',
                ),
            ),
            'optional' => array(
                'fields' => array(
                    'field_mandatory',
                    'field_default_value',
                    'field_details',
                ),
            ),
        ),
        'fields' => array(
            'field_choices' => array(
                'label' => __('Answers:'),
                'form' => array(
                    'type' => 'textarea',
                    'rows' => '5',
                    'value' => '',
                    'placeholder' => __('One answer per line'),
                ),
            ),
        ),
        // The custom javascript file that will be loaded after the field meta is created
        'js_file' => 'static/apps/noviusos_form/dist/js/admin/field/checkbox.min.js',
    ),
);
