<?php

return array(
    'name' => __('File upload'),

    // Fields default value
    'default_values' => array(
        'field_type' => 'text',
        'field_label' => __('I’m the label of a file input, click to edit me:'),
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
                    'field_details',
                ),
            ),
        ),
    ),
);
