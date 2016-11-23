<?php

return array(
    'name'           => __('Number'),

    // Fields default value
    'default_values' => array(
        'field_label' => __('Enter a number:'),
    ),

    'admin'                 => array(
        // Meta layout
        'layout' => array(
            'main'     => array(
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
                    'field_width',
                    'field_limited_to',
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

    // Sets true or an array with a custom config to make this field displayable as an answer column
    'answer_appdesk_config' => array(
        'dataType' => 'number',
    ),
);
