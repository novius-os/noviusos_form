<?php

return array(
    'name' => __('Message'),

    // Fields default value
    'default_values' => array(
        'field_type' => 'message',
    ),

    'admin' => array(
        // Meta layout
        'layout' => array(
            'main' => array(
                'fields' => array(
                    'field_label',
                    'field_message',
                    'field_style',
                ),
            ),
            'optional' => array(
                'fields' => array(),
            ),
        ),
    ),
);
