<?php

return array(
    'name' => __('Unique choice (drop-down list)'),

    // Fields default value
    'default_values' => array(
        'field_choices' => __("First option\nSecond option"),
    ),

    'admin' => array(
        // Meta layout
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
                    'field_origin',
                    'field_origin_var',
                    'field_details',
                    'field_width',
                ),
            ),
        ),

        // The custom javascript file that will be loaded after the field meta is created
        'js_file' => 'static/apps/noviusos_form/dist/js/admin/field/select.min.js',
    ),

    // Sets true or an array with a custom config to make this field displayable as an answer column
    'answer_appdesk_config' => array(
        'dataType' => 'string',
    ),
);
