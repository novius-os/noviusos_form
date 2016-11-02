<?php

return array(
    'name' => __('Unique choice (drop-down list)'),

    // Fields default value
    'default_values' => array(
        'field_type' => 'select',
        'field_choices' => __("First option\nSecond option"),
    ),

    'meta' => array(
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
    ),
);
