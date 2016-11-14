<?php

return array(
    'name' => __('Variable'),

    // If enabled this driver won't be available as field type in backoffice
    //
    //be hidden in the field type choice list and can be added only via the available layouts
    // In addition, the user won't be able to change the type of a field that use this driver
    'hidden' => false,

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
