<?php

return array(
    'name' => __('Variable'),

    // Fields default value
    'default_values' => array(
        'field_type' => 'variable',
    ),

    'admin' => array(
        // Meta layout
        'layout' => array(
            'main' => array(
                'fields' => array(
                    'field_label',
                    'field_origin',
                    'field_origin_var',
                ),
            ),
            'optional' => array(
                'fields' => array(),
            ),
        ),
    ),
);
