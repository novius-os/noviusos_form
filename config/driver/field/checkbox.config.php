<?php

return array(
    'name' => __('Multiple choice (checkboxes)'),

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

        // The custom javascript file that will be loaded after the field meta is created
        'js_file' => 'static/apps/noviusos_form/js/admin/field/checkbox.js',
    ),
);
