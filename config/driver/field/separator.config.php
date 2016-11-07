<?php

return array(
    'name' => __('Separator'),

    // Available meta fields
    'fields' => array(),

    // Fields default value
    'default_values' => array(
        'field_label' => __('Separator'),
    ),

    'admin' => array(
        // Meta layout
        'layout' => array(
            'main' => array(
                'fields' => array(),
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
