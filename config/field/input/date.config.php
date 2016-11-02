<?php

return array(
    'name' => __('Date'),

    // Fields default value
    'default_values' => array(
        'field_type' => 'text',
        'field_label' => __('Pick a date:'),
    ),

    'meta' => array(
        // Meta layout
        'layout' => array(
            'main' => array(
                'fields' => array(
                    'field_label',
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
