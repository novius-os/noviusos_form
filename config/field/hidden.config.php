<?php

return array(
    'name' => __('Hidden'),

    // Fields default value
    'default_values' => array(
        'field_type' => 'hidden',
        'field_label' => __('I’m the label for internal use only as I won’t be shown to users:'),
    ),

    'meta' => array(
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
    ),
);
