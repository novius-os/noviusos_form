<?php

Nos\I18n::current_dictionary('noviusos_form::common');

return array(
    'name' => __('Date'),
    'icon' => 'static/apps/noviusos_form/img/fields/date.png',

    // Fields default value
    'default_values' => array(
        'field_label' => __('Pick a date:'),
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
                ),
            ),
        ),
        'fields' => array(
            'field_placeholder' => array(
                'label' => __('Placeholder :'),
                'form' => array(
                    'type' => 'text',
                ),
            ),
        ),
    ),

    // Sets true if answer can be displayed in a column of the answer appdesk
    'answer_appdesk_config' => array(
        'dataType' => 'datetime',
//        'value' => function($fieldDriver, $answerField) {
//            return $fieldDriver->myCustomMethod($answerField);
//        }
    ),
);
