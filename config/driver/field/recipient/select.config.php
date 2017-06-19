<?php

return array(
    'name' => __('Email Recipient List'),
    'icon' => 'static/apps/noviusos_form/img/fields/dropdown.png',

    // Fields default value
    'default_values' => array(
        'field_technical_id' => 'recipient-list',
        'field_details' => __('A notification of the form answer will be sent to the selected email.'),
        'field_choices' => __("First option=mail@domain.com\nSecond option=othermail@domain.com"),
    ),

    'admin' => array(
        // Meta layout
        'layout' => array(
            'main' => array(
                'fields' => array(
                    'field_label',
                    'field_choices',
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
                ),
            ),
        ),
        'fields' => array(
            'field_choices' => array(
                'label' => __('Recipients:'),
                'form' => array(
                    'type' => 'textarea',
                    'rows' => '5',
                    'value' => '',
                    'placeholder' => __('One recipient per line'),
                ),
            ),
        ),
        // The custom javascript file that will be loaded after the field meta is created
        'js_file' => 'static/apps/noviusos_form/dist/js/admin/field/select.min.js',
    ),

    // Sets true or an array with a custom config to make this field displayable as an answer column
    'answer_appdesk_config' => array(
        'dataType' => 'string',
    ),

    // Set true to display the field in the "Special fields" column when adding a new field to a form
    'special' => true,

);
