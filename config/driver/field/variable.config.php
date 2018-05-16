<?php

Nos\I18n::current_dictionary('noviusos_form::common');

return array(
    'name' => __('Variable'),
    'icon' => 'static/apps/noviusos_form/img/fields/variable.png',

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

    // Set true to display the field in the "Special fields" column when adding a new field to a form
    'special' => true,

    // Set true to display the field only for experts
    'expert' => true,
);
