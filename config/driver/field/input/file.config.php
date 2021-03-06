<?php

Nos\I18n::current_dictionary('noviusos_form::common');

return array(
    'name' => __('File upload'),
    'icon' => 'static/apps/noviusos_form/img/fields/file.png',

    // Fields default value
    'default_values' => array(
        'field_label' => __('I’m the label of a file input, click to edit me:'),
    ),

    'admin'                 => array(
        // Meta layout
        'layout' => array(
            'main'     => array(
                'fields' => array(
                    'field_label',
                ),
            ),
            'optional' => array(
                'fields' => array(
                    'field_mandatory',
                    'field_details',
                ),
            ),
        ),
    ),

    // Sets true or an array with a custom config to make this field displayable as an answer column
    'answer_appdesk_config' => array(
        'dataType' => 'string',
    ),
);
