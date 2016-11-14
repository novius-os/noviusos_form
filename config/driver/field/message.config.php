<?php

return array(
    'name' => __('Message'),

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

    // Sets false if not displayable as an answer in backoffice (eg. for cosmetic fields, like separators, titles...)
    'display_as_answer' => false,

    // Sets false if not exportable
    'exportable' => false,
);
