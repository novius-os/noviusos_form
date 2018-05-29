<?php

Nos\I18n::current_dictionary('noviusos_form::common');

return array(
    'name' => __('Email address'),
    'icon' => 'static/apps/noviusos_form/img/fields/email.png',

    // Fields default value
    'default_values' => array(
        'field_label' => __('Your email address:'),
    ),

    'admin' => array(
        // Meta layout
        'layout' => array(
            'main' => array(
                'fields' => array(
                    'field_label',
                    'field_placeholder',
                ),
            ),
            'optional' => array(
                'fields' => array(
                    'field_mandatory',
                    'field_default_value',
                    'field_origin',
                    'field_origin_var',
                    'field_details',
                    'field_width',
                    'field_limited_to',
                ),
            ),
        ),
        'fields' => array(
            'field_default_value' => array(
                'label' => __('Default Email Address:'),
                'form' => array(
                    'type' => 'email',
                ),
            ),
            'field_placeholder' => array(
                'label' => __('Placeholder :'),
                'form' => array(
                    'type' => 'text',
                ),
            ),
        ),
    ),

    // Sets true or an array with a custom config to make this field displayable as an answer column
    'answer_appdesk_config' => array(
        'dataType' => 'string',
    ),
);
