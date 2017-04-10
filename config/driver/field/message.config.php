<?php

return array(
    'name' => __('Message'),
    'icon' => 'static/apps/noviusos_form/img/fields/message.png',

    'default_values' => array(
        'field_label' => __('Message:'),
        'field_message' => 'Your message',
    ),

    'admin'  => array(
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

    // Sets true to display the field in the "Special fields" column when adding a new field to a form
    'special' => true,
);
