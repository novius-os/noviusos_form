<?php

Nos\I18n::current_dictionary('noviusos_form::common');

return array(
    'name' => __('Captcha'),

    'front' => array(
        // Custom field view
        'view' => 'noviusos_form::front/form/field/captcha',
    ),

    'admin'             => array(
        // Meta layout
        'layout' => array(
            'main'     => array(
                'fields' => array(
                    'field_label',
                    'field_details',
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
    'exportable'        => false,
);
