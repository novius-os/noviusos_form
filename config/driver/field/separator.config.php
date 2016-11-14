<?php

return array(
    'name' => __('Separator'),

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

    // Sets false if not displayable as an answer in backoffice (eg. for cosmetic fields, like separators, titles...)
    'display_as_answer' => false,

    // Sets false if not exportable
    'exportable' => false,
);
